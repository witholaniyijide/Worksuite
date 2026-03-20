<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\ClassAttendance;
use App\Models\ProgressReport;

class TutorDashboardController extends Controller
{
    public function index()
    {
        $tutor = auth()->user()->tutor;

        if (!$tutor) {
            abort(403, 'You do not have a tutor profile.');
        }

        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Stats
        $activeStudents = $tutor->students()->wherePivot('is_active', true)->count();

        $pendingAttendance = ClassAttendance::where('tutor_id', $tutor->id)
            ->where('status', 'submitted')
            ->count();

        $thisMonthClasses = ClassAttendance::where('tutor_id', $tutor->id)
            ->whereMonth('class_date', $currentMonth)
            ->whereYear('class_date', $currentYear)
            ->count();

        $thisMonthEarnings = ClassAttendance::where('tutor_id', $tutor->id)
            ->whereMonth('class_date', $currentMonth)
            ->whereYear('class_date', $currentYear)
            ->where('status', '!=', 'disputed')
            ->sum('amount_earned');

        // Recent records
        $recentAttendance = ClassAttendance::with(['student', 'subject'])
            ->where('tutor_id', $tutor->id)
            ->orderBy('class_date', 'desc')
            ->limit(5)
            ->get();

        $recentReports = ProgressReport::with(['student', 'subject'])
            ->where('tutor_id', $tutor->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('elpisview.tutor.dashboard', compact(
            'tutor',
            'activeStudents',
            'pendingAttendance',
            'thisMonthClasses',
            'thisMonthEarnings',
            'recentAttendance',
            'recentReports'
        ));
    }
}
