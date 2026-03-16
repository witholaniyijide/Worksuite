<?php

namespace App\Http\Controllers;

use App\Models\Guardian;
use App\Models\ProgressReport;
use Illuminate\Http\Request;

class ParentPortalController extends Controller
{
    /**
     * Parent dashboard showing their children's delivered reports.
     */
    public function dashboard()
    {
        $guardian = Guardian::where('user_id', auth()->id())->firstOrFail();
        $studentIds = $guardian->students()->pluck('id');

        $recentReports = ProgressReport::whereIn('student_id', $studentIds)
            ->where('status', 'delivered')
            ->with(['student', 'subject', 'tutor.user'])
            ->orderBy('report_year', 'desc')
            ->orderBy('report_month', 'desc')
            ->limit(10)
            ->get();

        $students = $guardian->students()->with('region')->get();

        return view('elpisview.parent-portal.dashboard', compact('guardian', 'recentReports', 'students'));
    }

    /**
     * View all reports for a specific child.
     */
    public function studentReports(Request $request, $studentId)
    {
        $guardian = Guardian::where('user_id', auth()->id())->firstOrFail();
        $student = $guardian->students()->findOrFail($studentId);

        $query = ProgressReport::where('student_id', $student->id)
            ->where('status', 'delivered')
            ->with(['subject', 'tutor.user', 'weeklyEvaluations']);

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        if ($request->filled('month')) {
            $query->where('report_month', $request->month);
        }
        if ($request->filled('year')) {
            $query->where('report_year', $request->year);
        }

        $reports = $query->orderBy('report_year', 'desc')
            ->orderBy('report_month', 'desc')
            ->paginate(12);

        return view('elpisview.parent-portal.student-reports', compact('student', 'reports'));
    }

    /**
     * View a single report in detail.
     */
    public function viewReport($reportId)
    {
        $guardian = Guardian::where('user_id', auth()->id())->firstOrFail();
        $studentIds = $guardian->students()->pluck('id');

        $report = ProgressReport::whereIn('student_id', $studentIds)
            ->where('status', 'delivered')
            ->with(['student', 'subject', 'tutor.user', 'weeklyEvaluations'])
            ->findOrFail($reportId);

        return view('elpisview.parent-portal.view-report', compact('report'));
    }
}
