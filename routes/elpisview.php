<?php

use App\Http\Controllers\ClassAttendanceController;
use App\Http\Controllers\ElpisDashboardController;
use App\Http\Controllers\ParentPortalController;
use App\Http\Controllers\ProgressReportController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TutorController;
use App\Http\Controllers\TutorPayrollController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Elpis View Educational Portal Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('elpisview')->name('elpisview.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [ElpisDashboardController::class, 'index'])->name('dashboard');

    // Students Management
    Route::resource('students', StudentController::class);

    // Tutors Management
    Route::resource('tutors', TutorController::class);

    // Class Attendance (Tutor submits attendance for students)
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

    // Parent Portal (separate dashboard for parents)
    Route::prefix('parent')->name('parent.')->group(function () {
        Route::get('/dashboard', [ParentPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/student/{studentId}/reports', [ParentPortalController::class, 'studentReports'])
            ->name('student.reports');
        Route::get('/report/{reportId}', [ParentPortalController::class, 'viewReport'])
            ->name('report.view');
    });
});
