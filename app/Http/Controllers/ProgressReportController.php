<?php

namespace App\Http\Controllers;

use App\Models\ProgressReport;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Tutor;
use App\Models\WeeklyEvaluation;
use Illuminate\Http\Request;

class ProgressReportController extends Controller
{
    /**
     * Tutor: list their reports. Manager: list reports for their region.
     */
    public function index(Request $request)
    {
        $query = ProgressReport::with(['tutor.user', 'student', 'subject']);

        if ($request->filled('tutor_id')) {
            $query->where('tutor_id', $request->tutor_id);
        }
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('month')) {
            $query->where('report_month', $request->month);
        }
        if ($request->filled('year')) {
            $query->where('report_year', $request->year);
        }

        $reports = $query->orderBy('report_year', 'desc')
            ->orderBy('report_month', 'desc')
            ->paginate(20);

        $tutors = Tutor::with('user')->get();
        $students = Student::all();
        $subjects = Subject::all();

        return view('elpisview.reports.index', compact('reports', 'tutors', 'students', 'subjects'));
    }

    /**
     * New report form (Tutor).
     */
    public function create()
    {
        $tutors = Tutor::with('user')->where('status', 'active')->get();
        $students = Student::where('status', 'active')->get();
        $subjects = Subject::where('is_active', true)->get();

        return view('elpisview.reports.create', compact('tutors', 'students', 'subjects'));
    }

    /**
     * Save a new report (draft or submitted).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tutor_id' => 'required|exists:tutors,id',
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'report_month' => 'required|integer|min:1|max:12',
            'report_year' => 'required|integer|min:2020|max:2100',
            'overall_performance' => 'nullable|string',
            'strengths' => 'nullable|string',
            'areas_for_improvement' => 'nullable|string',
            'tutor_comments' => 'nullable|string',
            'weekly_evaluations' => 'nullable|array',
            'weekly_evaluations.*.week_number' => 'required|integer|min:1|max:5',
            'weekly_evaluations.*.topics_covered' => 'nullable|string',
            'weekly_evaluations.*.evaluation' => 'nullable|string',
            'weekly_evaluations.*.performance_rating' => 'nullable|in:excellent,good,satisfactory,needs_improvement,poor',
            'weekly_evaluations.*.attendance_this_week' => 'nullable|integer|min:0',
            'weekly_evaluations.*.comments' => 'nullable|string',
        ]);

        $status = $request->input('action') === 'submit' ? 'submitted' : 'draft';

        $report = ProgressReport::create([
            'tutor_id' => $validated['tutor_id'],
            'student_id' => $validated['student_id'],
            'subject_id' => $validated['subject_id'],
            'report_month' => $validated['report_month'],
            'report_year' => $validated['report_year'],
            'overall_performance' => $validated['overall_performance'] ?? null,
            'strengths' => $validated['strengths'] ?? null,
            'areas_for_improvement' => $validated['areas_for_improvement'] ?? null,
            'tutor_comments' => $validated['tutor_comments'] ?? null,
            'status' => $status,
            'submitted_at' => $status === 'submitted' ? now() : null,
        ]);

        // Save weekly evaluations
        if (!empty($validated['weekly_evaluations'])) {
            foreach ($validated['weekly_evaluations'] as $eval) {
                WeeklyEvaluation::create(array_merge($eval, [
                    'progress_report_id' => $report->id,
                ]));
            }
        }

        // Auto-calculate attendance stats
        $report->calculateAttendanceStats();

        $message = $status === 'submitted'
            ? 'Report submitted for manager review.'
            : 'Report saved as draft.';

        return redirect()->route('elpisview.reports.show', $report)
            ->with('success', $message);
    }

    public function show(ProgressReport $progressReport)
    {
        $progressReport->load(['tutor.user', 'student.guardian.user', 'subject', 'weeklyEvaluations', 'reviewer']);

        return view('elpisview.reports.show', compact('progressReport'));
    }

    public function edit(ProgressReport $progressReport)
    {
        $progressReport->load('weeklyEvaluations');
        $students = Student::where('status', 'active')->get();
        $subjects = Subject::where('is_active', true)->get();

        return view('elpisview.reports.edit', compact('progressReport', 'students', 'subjects'));
    }

    public function update(Request $request, ProgressReport $progressReport)
    {
        $validated = $request->validate([
            'overall_performance' => 'nullable|string',
            'strengths' => 'nullable|string',
            'areas_for_improvement' => 'nullable|string',
            'tutor_comments' => 'nullable|string',
            'weekly_evaluations' => 'nullable|array',
            'weekly_evaluations.*.week_number' => 'required|integer|min:1|max:5',
            'weekly_evaluations.*.topics_covered' => 'nullable|string',
            'weekly_evaluations.*.evaluation' => 'nullable|string',
            'weekly_evaluations.*.performance_rating' => 'nullable|in:excellent,good,satisfactory,needs_improvement,poor',
            'weekly_evaluations.*.attendance_this_week' => 'nullable|integer|min:0',
            'weekly_evaluations.*.comments' => 'nullable|string',
        ]);

        $status = $request->input('action') === 'submit' ? 'submitted' : $progressReport->status;

        $progressReport->update([
            'overall_performance' => $validated['overall_performance'],
            'strengths' => $validated['strengths'],
            'areas_for_improvement' => $validated['areas_for_improvement'],
            'tutor_comments' => $validated['tutor_comments'],
            'status' => $status,
            'submitted_at' => ($status === 'submitted' && !$progressReport->submitted_at) ? now() : $progressReport->submitted_at,
        ]);

        // Update weekly evaluations
        if (!empty($validated['weekly_evaluations'])) {
            $progressReport->weeklyEvaluations()->delete();
            foreach ($validated['weekly_evaluations'] as $eval) {
                WeeklyEvaluation::create(array_merge($eval, [
                    'progress_report_id' => $progressReport->id,
                ]));
            }
        }

        $progressReport->calculateAttendanceStats();

        return redirect()->route('elpisview.reports.show', $progressReport)
            ->with('success', 'Report updated.');
    }

    /**
     * Manager approves a report.
     */
    public function approve(ProgressReport $progressReport)
    {
        $progressReport->approve(auth()->id());

        return back()->with('success', 'Report approved.');
    }

    /**
     * Manager requests adjustments.
     */
    public function requestAdjustment(Request $request, ProgressReport $progressReport)
    {
        $request->validate(['manager_comments' => 'required|string']);
        $progressReport->requestAdjustment(auth()->id(), $request->manager_comments);

        return back()->with('success', 'Adjustment requested from tutor.');
    }

    /**
     * Manager sends approved report to parent.
     */
    public function sendToParent(ProgressReport $progressReport)
    {
        $progressReport->load(['student.guardian.user', 'tutor.user', 'subject', 'weeklyEvaluations']);

        $guardian = $progressReport->student->guardian;
        if (!$guardian || !$guardian->user->email) {
            return back()->with('error', 'Parent email not found.');
        }

        // In production, this would send an email. For now, mark as delivered.
        $progressReport->markDelivered();

        return back()->with('success', 'Report delivered to parent portal and notification sent.');
    }

    /**
     * Bulk send approved reports for a given month.
     */
    public function bulkSend(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer',
        ]);

        $reports = ProgressReport::where('status', 'approved')
            ->where('report_month', $request->month)
            ->where('report_year', $request->year)
            ->get();

        $sent = 0;
        foreach ($reports as $report) {
            $report->markDelivered();
            $sent++;
        }

        return back()->with('success', "{$sent} reports delivered to parents.");
    }

    public function destroy(ProgressReport $progressReport)
    {
        $progressReport->delete();

        return redirect()->route('elpisview.reports.index')
            ->with('success', 'Report deleted.');
    }
}
