<?php

namespace App\Http\Controllers\Director;

use App\Http\Controllers\Controller;
use App\Models\ClassAttendance;
use App\Models\ProgressReport;
use App\Models\Region;
use App\Models\Student;
use App\Models\Tutor;

class DirectorDashboardController extends Controller
{
    public function index()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Global stats
        $totalStudents = Student::where('status', 'active')->count();
        $totalTutors = Tutor::where('status', 'active')->count();

        $thisMonthRevenue = ClassAttendance::whereMonth('class_date', $currentMonth)
            ->whereYear('class_date', $currentYear)
            ->where('status', 'approved')
            ->sum('amount_earned');

        $pendingApprovals = ProgressReport::whereIn('status', ['submitted', 'approved'])
            ->count();

        $totalAttendanceThisMonth = ClassAttendance::whereMonth('class_date', $currentMonth)
            ->whereYear('class_date', $currentYear)
            ->count();

        $approvedAttendanceThisMonth = ClassAttendance::whereMonth('class_date', $currentMonth)
            ->whereYear('class_date', $currentYear)
            ->where('status', 'approved')
            ->count();

        $attendanceRate = $totalAttendanceThisMonth > 0
            ? round(($approvedAttendanceThisMonth / $totalAttendanceThisMonth) * 100, 1)
            : 0;

        // Region breakdown
        $regions = Region::withCount([
            'students' => fn($q) => $q->where('status', 'active'),
            'tutors' => fn($q) => $q->where('status', 'active'),
        ])->get();

        // Reports needing director approval (approved by manager but not yet delivered)
        $pendingDirectorReports = ProgressReport::with(['tutor.user', 'student', 'subject'])
            ->where('status', 'approved')
            ->whereNull('delivered_at')
            ->orderBy('approved_at', 'desc')
            ->limit(10)
            ->get();

        return view('elpisview.director.dashboard', compact(
            'totalStudents',
            'totalTutors',
            'thisMonthRevenue',
            'pendingApprovals',
            'attendanceRate',
            'regions',
            'pendingDirectorReports'
        ));
    }
}
