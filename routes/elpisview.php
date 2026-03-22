<?php

use App\Http\Controllers\Auth\ElpisLoginController;
use App\Http\Controllers\ClassAttendanceController;
use App\Http\Controllers\ElpisDashboardController;
use App\Http\Controllers\ParentPortalController;
use App\Http\Controllers\ProgressReportController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TutorController;
use App\Http\Controllers\TutorPayrollController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Director\DirectorDashboardController;
use App\Http\Controllers\Manager\ManagerDashboardController;
use App\Http\Controllers\Tutor\TutorDashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Elpis View Educational Portal Routes
|--------------------------------------------------------------------------
|
| School subjects tutoring: Math, English, Chemistry, Physics, Yoruba,
| French, Coding, Bible Study, IJMB prep. Regions: UK, US, Canada.
|
*/

// ─────────────────────────────────────────────────────────────────────────────
// Authentication Routes (guests only)
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware(['guest'])->prefix('elpisview')->name('elpisview.')->group(function () {
    Route::get('/login', [ElpisLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [ElpisLoginController::class, 'login'])->name('login.submit');
});

Route::middleware(['auth'])->prefix('elpisview')->name('elpisview.')->group(function () {
    Route::post('/logout', [ElpisLoginController::class, 'logout'])->name('logout');
});

// ─────────────────────────────────────────────────────────────────────────────
// Shared / General Routes (all authenticated users)
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware(['auth'])->prefix('elpisview')->name('elpisview.')->group(function () {

    // Default Dashboard (redirects or shows based on role)
    Route::get('/dashboard', [ElpisDashboardController::class, 'index'])->name('dashboard');

    // Students Management (Admin/Director full access, others view-only via portals)
    Route::resource('students', StudentController::class);

    // Tutors Management
    Route::resource('tutors', TutorController::class);

    // Class Attendance
    Route::resource('attendance', ClassAttendanceController::class)->parameters([
        'attendance' => 'classAttendance',
    ]);
    Route::post('attendance/{classAttendance}/approve', [ClassAttendanceController::class, 'approve'])
        ->name('attendance.approve');
    Route::post('attendance/{classAttendance}/reject', [ClassAttendanceController::class, 'reject'])
        ->name('attendance.reject');

    // Pending attendance approval queue
    Route::get('attendance-pending', [ClassAttendanceController::class, 'pending'])
        ->name('attendance.pending');

    // Bulk approve attendance
    Route::post('attendance-bulk-approve', [ClassAttendanceController::class, 'bulkApprove'])
        ->name('attendance.bulk-approve');

    // Duplicate check (AJAX)
    Route::post('attendance-check-duplicate', [ClassAttendanceController::class, 'checkDuplicate'])
        ->name('attendance.check-duplicate');

    // AJAX endpoints for attendance form
    Route::get('ajax/tutor/{tutor}/students', [ClassAttendanceController::class, 'getStudentsByTutor'])
        ->name('ajax.tutor.students');
    Route::get('ajax/tutor/{tutor}/student/{student}/subjects', [ClassAttendanceController::class, 'getSubjects'])
        ->name('ajax.tutor.student.subjects');

    // Progress Reports
    Route::resource('reports', ProgressReportController::class)->parameters([
        'reports' => 'progressReport',
    ]);
    Route::post('reports/{progressReport}/approve', [ProgressReportController::class, 'approve'])
        ->name('reports.approve');
    Route::post('reports/{progressReport}/request-adjustment', [ProgressReportController::class, 'requestAdjustment'])
        ->name('reports.request-adjustment');
    Route::post('reports/{progressReport}/send-to-parent', [ProgressReportController::class, 'sendToParent'])
        ->name('reports.send-to-parent');
    Route::post('reports/bulk-send', [ProgressReportController::class, 'bulkSend'])
        ->name('reports.bulk-send');

    // Payroll
    Route::get('payroll', [TutorPayrollController::class, 'index'])->name('payroll.index');
    Route::get('payroll/summary', [TutorPayrollController::class, 'summary'])->name('payroll.summary');
    Route::post('payroll/generate', [TutorPayrollController::class, 'generate'])->name('payroll.generate');
    Route::get('payroll/{tutorPayroll}', [TutorPayrollController::class, 'show'])->name('payroll.show');
    Route::post('payroll/{tutorPayroll}/adjust', [TutorPayrollController::class, 'adjust'])->name('payroll.adjust');
    Route::post('payroll/{tutorPayroll}/approve', [TutorPayrollController::class, 'approve'])->name('payroll.approve');
    Route::post('payroll/{tutorPayroll}/mark-paid', [TutorPayrollController::class, 'markPaid'])->name('payroll.mark-paid');

    // ─────────────────────────────────────────────────────────────────────────
    // TUTOR PORTAL
    // ─────────────────────────────────────────────────────────────────────────
    Route::prefix('tutor-portal')->name('tutor.')->group(function () {
        Route::get('/dashboard', [TutorDashboardController::class, 'index'])->name('dashboard');
    });

    // ─────────────────────────────────────────────────────────────────────────
    // MANAGER PORTAL (Location-based: UK, US, Canada)
    // ─────────────────────────────────────────────────────────────────────────
    Route::prefix('manager-portal')->name('manager.')->group(function () {
        Route::get('/dashboard', [ManagerDashboardController::class, 'index'])->name('dashboard');
    });

    // ─────────────────────────────────────────────────────────────────────────
    // DIRECTOR PORTAL
    // ─────────────────────────────────────────────────────────────────────────
    Route::prefix('director-portal')->name('director.')->group(function () {
        Route::get('/dashboard', [DirectorDashboardController::class, 'index'])->name('dashboard');
    });

    // ─────────────────────────────────────────────────────────────────────────
    // ADMIN PORTAL
    // ─────────────────────────────────────────────────────────────────────────
    Route::prefix('admin-portal')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    });

    // ─────────────────────────────────────────────────────────────────────────
    // PARENT PORTAL
    // ─────────────────────────────────────────────────────────────────────────
    Route::prefix('parent')->name('parent.')->group(function () {
        Route::get('/dashboard', [ParentPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/student/{studentId}/reports', [ParentPortalController::class, 'studentReports'])
            ->name('student.reports');
        Route::get('/report/{reportId}', [ParentPortalController::class, 'viewReport'])
            ->name('report.view');
    });
});
