<?php

namespace App\Http\Controllers;

use App\Models\ClassAttendance;
use App\Models\ProgressReport;
use App\Models\Region;
use App\Models\Student;
use App\Models\Tutor;
use App\Models\TutorPayroll;

class ElpisDashboardController extends Controller
{
    public function index()
    {
        $totalStudents = Student::where('status', 'active')->count();
        $totalTutors = Tutor::where('status', 'active')->count();
        $regions = Region::withCount(['students', 'tutors'])->get();

        // Current month stats
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $monthlyClasses = ClassAttendance::whereMonth('class_date', $currentMonth)
            ->whereYear('class_date', $currentYear)
            ->count();

        $monthlyHours = ClassAttendance::whereMonth('class_date', $currentMonth)
            ->whereYear('class_date', $currentYear)
            ->sum('duration_hours');

        $pendingReports = ProgressReport::where('status', 'submitted')->count();

        $recentAttendance = ClassAttendance::with(['tutor.user', 'student', 'subject'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $pendingPayrolls = TutorPayroll::whereIn('status', ['calculated', 'reviewed'])->count();

        return view('elpisview.dashboard', compact(
            'totalStudents', 'totalTutors', 'regions', 'monthlyClasses',
            'monthlyHours', 'pendingReports', 'recentAttendance', 'pendingPayrolls'
        ));
    }
}
