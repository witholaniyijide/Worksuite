<?php

namespace App\Http\Controllers;

use App\Models\ClassAttendance;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Tutor;
use Illuminate\Http\Request;

class ClassAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = ClassAttendance::with(['tutor.user', 'student', 'subject']);

        if ($request->filled('tutor_id')) {
            $query->where('tutor_id', $request->tutor_id);
        }
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        if ($request->filled('month')) {
            $query->whereMonth('class_date', $request->month);
        }
        if ($request->filled('year')) {
            $query->whereYear('class_date', $request->year);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->orderBy('class_date', 'desc')->paginate(25);
        $tutors = Tutor::with('user')->where('status', 'active')->get();
        $students = Student::where('status', 'active')->get();
        $subjects = Subject::where('is_active', true)->get();

        return view('elpisview.attendance.index', compact('attendances', 'tutors', 'students', 'subjects'));
    }

    public function create()
    {
        $tutors = Tutor::with('user')->where('status', 'active')->get();
        $students = Student::where('status', 'active')->get();
        $subjects = Subject::where('is_active', true)->get();

        return view('elpisview.attendance.create', compact('tutors', 'students', 'subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tutor_id' => 'required|exists:tutors,id',
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'class_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'student_status' => 'required|in:present,absent,late,excused',
            'class_notes' => 'nullable|string',
            'topics_covered' => 'nullable|string',
        ]);

        // Look up pay rate
        $tutor = Tutor::findOrFail($validated['tutor_id']);
        $payRate = $tutor->getPayRate($validated['subject_id'], $validated['student_id'], $validated['class_date']);
        $validated['rate_applied'] = $payRate ? $payRate->rate_per_hour : 0;
        $validated['currency'] = $payRate ? $payRate->currency : 'USD';
        $validated['status'] = 'submitted';

        // duration_hours and amount_earned calculated in model boot
        $attendance = ClassAttendance::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Attendance recorded. Earnings: ' . $attendance->currency . ' ' . number_format($attendance->amount_earned, 2),
            ]);
        }

        return redirect()->route('elpisview.attendance.index')
            ->with('success', 'Attendance recorded. Earnings: ' . $attendance->currency . ' ' . number_format($attendance->amount_earned, 2));
    }

    public function show(ClassAttendance $classAttendance)
    {
        $classAttendance->load(['tutor.user', 'student', 'subject', 'approvedByUser']);

        return view('elpisview.attendance.show', compact('classAttendance'));
    }

    public function edit(ClassAttendance $classAttendance)
    {
        $tutors = Tutor::with('user')->where('status', 'active')->get();
        $students = Student::where('status', 'active')->get();
        $subjects = Subject::where('is_active', true)->get();

        return view('elpisview.attendance.edit', compact('classAttendance', 'tutors', 'students', 'subjects'));
    }

    public function update(Request $request, ClassAttendance $classAttendance)
    {
        $validated = $request->validate([
            'class_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'student_status' => 'required|in:present,absent,late,excused',
            'class_notes' => 'nullable|string',
            'topics_covered' => 'nullable|string',
        ]);

        $classAttendance->update($validated);

        return redirect()->route('elpisview.attendance.show', $classAttendance)
            ->with('success', 'Attendance updated.');
    }

    public function approve(ClassAttendance $classAttendance)
    {
        $classAttendance->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Attendance approved.');
    }

    public function destroy(ClassAttendance $classAttendance)
    {
        $classAttendance->delete();

        return redirect()->route('elpisview.attendance.index')
            ->with('success', 'Attendance record deleted.');
    }

    /**
     * Get students assigned to a tutor (AJAX).
     */
    public function getStudentsByTutor(Tutor $tutor)
    {
        $students = $tutor->students()->where('student_tutor.is_active', true)->get();

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
}
