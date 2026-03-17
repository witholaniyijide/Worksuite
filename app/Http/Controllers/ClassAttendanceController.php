<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttendanceRequest;
use App\Models\ClassAttendance;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Tutor;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ClassAttendanceController extends Controller
{
    /**
     * Display attendance records with My Students / Stand-in tabs.
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'my-students');
        $month = $request->get('month', now()->format('Y-m'));
        $status = $request->get('status');

        $query = ClassAttendance::with(['tutor.user', 'student', 'subject']);

        // Filter by tab (stand-in vs regular)
        if ($tab === 'stand-in') {
            $query->where('is_stand_in', true);
        } else {
            $query->where('is_stand_in', false);
        }

        if ($request->filled('tutor_id')) {
            $query->where('tutor_id', $request->tutor_id);
        }
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        if ($month) {
            $startDate = Carbon::parse($month . '-01')->startOfMonth();
            $endDate = Carbon::parse($month . '-01')->endOfMonth();
            $query->whereBetween('class_date', [$startDate, $endDate]);
        }
        if ($status) {
            $query->where('status', $status);
        }

        $attendances = $query->orderBy('class_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Calculate monthly position for each record
        foreach ($attendances as $attendance) {
            if ($attendance->is_stand_in) {
                $attendance->monthly_position = 0;
            } else {
                $monthlyApproved = ClassAttendance::where('student_id', $attendance->student_id)
                    ->where('status', 'approved')
                    ->where('is_stand_in', false)
                    ->whereYear('class_date', $attendance->class_date->year)
                    ->whereMonth('class_date', $attendance->class_date->month)
                    ->whereDate('class_date', '<=', $attendance->class_date)
                    ->orderBy('class_date', 'asc')
                    ->orderBy('start_time', 'asc')
                    ->pluck('id')
                    ->toArray();

                $position = array_search($attendance->id, $monthlyApproved);
                if ($attendance->status === 'approved' && $position !== false) {
                    $attendance->monthly_position = $position + 1;
                } elseif ($attendance->status === 'submitted') {
                    $approvedBeforeCount = ClassAttendance::where('student_id', $attendance->student_id)
                        ->where('status', 'approved')
                        ->where('is_stand_in', false)
                        ->whereYear('class_date', $attendance->class_date->year)
                        ->whereMonth('class_date', $attendance->class_date->month)
                        ->whereDate('class_date', '<', $attendance->class_date)
                        ->count();
                    $attendance->monthly_position = $approvedBeforeCount + 1;
                } else {
                    $attendance->monthly_position = 0;
                }
            }

            $attendance->monthly_total = ClassAttendance::where('student_id', $attendance->student_id)
                ->where('is_stand_in', false)
                ->whereYear('class_date', $attendance->class_date->year)
                ->whereMonth('class_date', $attendance->class_date->month)
                ->count();
        }

        // Stats for current month
        $stats = [
            'total' => ClassAttendance::where('is_stand_in', false)
                ->whereMonth('class_date', now()->month)
                ->whereYear('class_date', now()->year)
                ->count(),
            'approved' => ClassAttendance::where('is_stand_in', false)
                ->where('status', 'approved')
                ->whereMonth('class_date', now()->month)
                ->whereYear('class_date', now()->year)
                ->count(),
            'pending' => ClassAttendance::where('is_stand_in', false)
                ->where('status', 'submitted')
                ->count(),
            'standin_count' => ClassAttendance::where('is_stand_in', true)
                ->whereMonth('class_date', now()->month)
                ->whereYear('class_date', now()->year)
                ->count(),
        ];

        $tutors = Tutor::with('user')->where('status', 'active')->get();
        $students = Student::where('status', 'active')->get();
        $subjects = Subject::where('is_active', true)->get();

        return view('elpisview.attendance.index', compact(
            'attendances', 'tutors', 'students', 'subjects',
            'tab', 'month', 'status', 'stats'
        ));
    }

    /**
     * Show form for creating attendance (regular or stand-in).
     */
    public function create(Request $request)
    {
        $isStandIn = $request->boolean('standin', false);
        $tutors = Tutor::with('user')->where('status', 'active')->get();
        $subjects = Subject::where('is_active', true)->get();

        // For regular: get students assigned to selected tutor (via AJAX)
        // For stand-in: show all active students
        $students = Student::where('status', 'active')->get();

        return view('elpisview.attendance.create', compact('tutors', 'students', 'subjects', 'isStandIn'));
    }

    /**
     * Store attendance record.
     */
    public function store(StoreAttendanceRequest $request)
    {
        $tutor = Tutor::findOrFail($request->tutor_id ?? $request->get('tutor_id'));
        $student = Student::findOrFail($request->student_id);
        $isStandIn = $request->boolean('is_stand_in', false);

        // Auto-detect stand-in: check if student is assigned to this tutor
        $assignedStudentIds = $tutor->students()
            ->wherePivot('is_active', true)
            ->pluck('students.id')
            ->toArray();
        $isActuallyStandIn = !in_array($student->id, $assignedStudentIds);

        // Determine if submission is late (6-hour grace period after class end)
        $classEndTime = Carbon::parse($request->class_date . ' ' . $request->end_time);
        $deadline = $classEndTime->copy()->addHours(6);
        $isLate = now()->gt($deadline);

        $isRescheduled = $request->boolean('is_rescheduled', false);

        // Look up pay rate
        $payRate = $tutor->getPayRate($request->subject_id, $request->student_id, $request->class_date);

        $attendance = ClassAttendance::create([
            'tutor_id' => $tutor->id,
            'student_id' => $request->student_id,
            'subject_id' => $request->subject_id,
            'class_date' => $request->class_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'student_status' => 'present',
            'rate_applied' => $payRate ? $payRate->rate_per_hour : 0,
            'currency' => $payRate ? $payRate->currency : 'USD',
            'topics_covered' => $request->topics_covered,
            'class_notes' => $request->class_notes,
            'status' => 'submitted',
            'is_stand_in' => $isActuallyStandIn || $isStandIn,
            'stand_in_reason' => ($isActuallyStandIn || $isStandIn) ? $request->stand_in_reason : null,
            'is_late' => $isLate,
            'is_late_submission' => $isLate,
            'is_rescheduled' => $isRescheduled,
            'original_scheduled_time' => $isRescheduled ? $request->original_scheduled_time : null,
            'reschedule_reason' => $isRescheduled ? $request->reschedule_reason : null,
            'reschedule_notes' => $isRescheduled ? $request->reschedule_notes : null,
        ]);

        $message = ($isActuallyStandIn || $isStandIn)
            ? 'Stand-in attendance submitted successfully! Awaiting approval.'
            : 'Attendance submitted successfully! Earnings: ' . $attendance->currency . ' ' . number_format($attendance->amount_earned, 2);

        if ($request->ajax()) {
            return response()->json(['status' => 'success', 'message' => $message]);
        }

        return redirect()->route('elpisview.attendance.show', $attendance)
            ->with('success', $message);
    }

    /**
     * Display attendance record details.
     */
    public function show(ClassAttendance $classAttendance)
    {
        $classAttendance->load(['tutor.user', 'student', 'subject', 'approvedByUser']);

        return view('elpisview.attendance.show', compact('classAttendance'));
    }

    /**
     * Approve an attendance record.
     */
    public function approve(ClassAttendance $classAttendance)
    {
        $classAttendance->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Attendance approved successfully.');
    }

    /**
     * Reject an attendance record with reason.
     */
    public function reject(Request $request, ClassAttendance $classAttendance)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $classAttendance->update([
            'status' => 'disputed',
            'rejection_reason' => $request->rejection_reason,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Attendance rejected.');
    }

    /**
     * Bulk approve multiple attendance records.
     */
    public function bulkApprove(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return redirect()->back()->with('error', 'No records selected.');
        }

        ClassAttendance::whereIn('id', $ids)
            ->where('status', 'submitted')
            ->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

        return redirect()->back()->with('success', count($ids) . ' attendance record(s) approved.');
    }

    /**
     * Display pending attendance records for approval.
     */
    public function pending(Request $request)
    {
        $query = ClassAttendance::where('status', 'submitted')
            ->with(['tutor.user', 'student', 'subject']);

        if ($request->filled('tutor_id')) {
            $query->where('tutor_id', $request->tutor_id);
        }
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        $attendances = $query->orderBy('class_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(20);

        $tutors = Tutor::with('user')->where('status', 'active')->get();
        $students = Student::where('status', 'active')->get();

        return view('elpisview.attendance.pending', compact('attendances', 'tutors', 'students'));
    }

    /**
     * Check for duplicate attendance records (AJAX).
     */
    public function checkDuplicate(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_date' => 'required|date',
        ]);

        $student = Student::find($request->student_id);

        $duplicates = ClassAttendance::where('student_id', $request->student_id)
            ->whereDate('class_date', $request->class_date)
            ->with('tutor.user')
            ->get();

        if ($duplicates->isEmpty()) {
            return response()->json(['has_duplicate' => false]);
        }

        $duplicateInfo = $duplicates->map(function ($record) {
            return [
                'id' => $record->id,
                'time' => $record->start_time ? Carbon::parse($record->start_time)->format('g:i A') : 'N/A',
                'status' => ucfirst($record->status),
                'tutor' => $record->tutor?->name ?? 'Unknown',
                'submitted_at' => $record->created_at->format('M j, Y g:i A'),
            ];
        });

        return response()->json([
            'has_duplicate' => true,
            'count' => $duplicates->count(),
            'student_name' => $student->full_name,
            'date' => Carbon::parse($request->class_date)->format('l, M j, Y'),
            'duplicates' => $duplicateInfo,
        ]);
    }

    /**
     * Get students assigned to a tutor (AJAX).
     */
    public function getStudentsByTutor(Tutor $tutor)
    {
        $students = $tutor->students()->wherePivot('is_active', true)->get();

        return response()->json($students);
    }

    /**
     * Get subjects for a tutor-student pair (AJAX).
     */
    public function getSubjects(Tutor $tutor, Student $student)
    {
        $subjectIds = \DB::table('student_tutor')
            ->where('tutor_id', $tutor->id)
            ->where('student_id', $student->id)
            ->where('is_active', true)
            ->pluck('subject_id');

        $subjects = Subject::whereIn('id', $subjectIds)->get();

        return response()->json($subjects);
    }

    public function destroy(ClassAttendance $classAttendance)
    {
        $classAttendance->delete();

        return redirect()->route('elpisview.attendance.index')
            ->with('success', 'Attendance record deleted.');
    }
}
