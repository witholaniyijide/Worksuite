<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'tutor_id', 'student_id', 'subject_id', 'class_date',
        'start_time', 'end_time', 'duration_hours', 'student_status',
        'rate_applied', 'amount_earned', 'currency', 'class_notes',
        'topics_covered', 'status', 'approved_by', 'approved_at',
    ];

    protected $casts = [
        'class_date' => 'date',
        'duration_hours' => 'decimal:2',
        'rate_applied' => 'decimal:2',
        'amount_earned' => 'decimal:2',
        'approved_at' => 'datetime',
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

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Calculate duration and earnings before saving.
     */
    public static function boot()
    {
        parent::boot();

        static::saving(function (self $attendance) {
            // Calculate duration from start/end time
            if ($attendance->start_time && $attendance->end_time) {
                $start = \Carbon\Carbon::parse($attendance->class_date->format('Y-m-d') . ' ' . $attendance->start_time);
                $end = \Carbon\Carbon::parse($attendance->class_date->format('Y-m-d') . ' ' . $attendance->end_time);
                $attendance->duration_hours = round($end->diffInMinutes($start) / 60, 2);
            }

            // Calculate earnings
            if ($attendance->duration_hours && $attendance->rate_applied) {
                $attendance->amount_earned = round($attendance->duration_hours * $attendance->rate_applied, 2);
            }
        });
    }
}
