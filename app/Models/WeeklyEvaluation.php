<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeeklyEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'progress_report_id', 'week_number', 'topics_covered',
        'evaluation', 'performance_rating', 'attendance_this_week', 'comments',
    ];

    public function progressReport(): BelongsTo
    {
        return $this->belongsTo(ProgressReport::class);
    }

    public function getRatingBadgeAttribute(): string
    {
        return match ($this->performance_rating) {
            'excellent' => 'badge-success',
            'good' => 'badge-primary',
            'satisfactory' => 'badge-info',
            'needs_improvement' => 'badge-warning',
            'poor' => 'badge-danger',
            default => 'badge-secondary',
        };
    }
}
