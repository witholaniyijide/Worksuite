<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassAttendance;
use App\Models\ProgressReport;
use App\Models\Student;
use App\Models\Tutor;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Student counts by status
        $studentCounts = [
            'active' => Student::where('status', 'active')->count(),
            'inactive' => Student::where('status', 'inactive')->count(),
            'graduated' => Student::where('status', 'graduated')->count(),
            'withdrawn' => Student::where('status', 'withdrawn')->count(),
        ];

        // Tutor counts by status
        $tutorCounts = [
            'active' => Tutor::where('status', 'active')->count(),
            'inactive' => Tutor::where('status', 'inactive')->count(),
            'on_leave' => Tutor::where('status', 'on_leave')->count(),
        ];

        // Pending counts
        $pendingAttendance = ClassAttendance::where('status', 'submitted')->count();
        $pendingReports = ProgressReport::where('status', 'submitted')->count();

        // Today's classes
        $todaysClasses = ClassAttendance::where('class_date', Carbon::today())->count();

        // Recent activity
        $recentActivity = ClassAttendance::with(['student', 'tutor.user', 'subject'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Latest students and tutors
        $latestStudents = Student::orderBy('created_at', 'desc')->limit(5)->get();
        $latestTutors = Tutor::with('user')->orderBy('created_at', 'desc')->limit(5)->get();

        return view('elpisview.admin.dashboard', compact(
            'studentCounts',
            'tutorCounts',
            'pendingAttendance',
            'pendingReports',
            'todaysClasses',
            'recentActivity',
            'latestStudents',
            'latestTutors'
        ));
    }
}
