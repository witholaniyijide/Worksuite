<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProgressReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'tutor_id', 'student_id', 'subject_id', 'report_month', 'report_year',
        'overall_performance', 'strengths', 'areas_for_improvement', 'tutor_comments',
        'attendance_count', 'total_classes', 'status', 'reviewed_by',
        'manager_comments', 'submitted_at', 'approved_at', 'delivered_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(Tutor::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function weeklyEvaluations(): HasMany
    {
        return $this->hasMany(WeeklyEvaluation::class)->orderBy('week_number');
    }

    /**
     * Submit this report to the regional manager.
     */
    public function submit(): void
    {
        $this->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);
    }

    /**
     * Approve this report.
     */
    public function approve(int $userId): void
    {
        $this->update([
            'status' => 'approved',
            'reviewed_by' => $userId,
            'approved_at' => now(),
        ]);
    }

    /**
     * Request adjustments from tutor.
     */
    public function requestAdjustment(int $userId, string $comments): void
    {
        $this->update([
            'status' => 'adjustment_requested',
            'reviewed_by' => $userId,
            'manager_comments' => $comments,
        ]);
    }

    /**
     * Mark as delivered to parent.
     */
    public function markDelivered(): void
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);
    }

    /**
     * Get the reporting period label.
     */
    public function getPeriodLabelAttribute(): string
    {
        $monthName = \Carbon\Carbon::create($this->report_year, $this->report_month, 1)->format('F Y');
        return $monthName;
    }

    /**
     * Auto-calculate attendance stats from ClassAttendance records.
     */
    public function calculateAttendanceStats(): void
    {
        $attendances = ClassAttendance::where('tutor_id', $this->tutor_id)
            ->where('student_id', $this->student_id)
            ->where('subject_id', $this->subject_id)
            ->whereMonth('class_date', $this->report_month)
            ->whereYear('class_date', $this->report_year)
            ->get();

        $this->update([
            'total_classes' => $attendances->count(),
            'attendance_count' => $attendances->where('student_status', 'present')->count(),
        ]);
    }
}
