<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\ClassAttendance;
use App\Models\ProgressReport;
use App\Models\RegionalManager;
use App\Models\Student;
use App\Models\Tutor;

class ManagerDashboardController extends Controller
{
    public function index()
    {
        $manager = RegionalManager::where('user_id', auth()->id())->first();

        if (!$manager) {
            abort(403, 'You do not have a regional manager profile.');
        }

        $regionId = $manager->region_id;
        $region = $manager->region;

        // Stats filtered by region
        $activeStudents = Student::where('region_id', $regionId)
            ->where('status', 'active')
            ->count();

        $activeTutors = Tutor::where('region_id', $regionId)
            ->where('status', 'active')
            ->count();

        $tutorIds = Tutor::where('region_id', $regionId)->pluck('id');

        $pendingAttendance = ClassAttendance::whereIn('tutor_id', $tutorIds)
            ->where('status', 'submitted')
            ->count();

        $pendingReports = ProgressReport::whereIn('tutor_id', $tutorIds)
            ->where('status', 'submitted')
            ->count();

        // Recent attendance for region
        $recentAttendance = ClassAttendance::with(['tutor.user', 'student', 'subject'])
            ->whereIn('tutor_id', $tutorIds)
            ->orderBy('class_date', 'desc')
            ->limit(10)
            ->get();

        return view('elpisview.manager.dashboard', compact(
            'manager',
            'region',
            'activeStudents',
            'activeTutors',
            'pendingAttendance',
            'pendingReports',
            'recentAttendance'
        ));
    }
}
