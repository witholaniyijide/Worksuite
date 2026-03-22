<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegionalManager extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'region_id', 'staff_id', 'phone', 'sender_email'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function getNameAttribute(): string
    {
        return $this->user->name ?? '';
    }

    /**
     * Get all tutors managed by this regional manager (same region).
     */
    public function tutors()
    {
        return Tutor::where('region_id', $this->region_id)->get();
    }

    /**
     * Get all pending reports for this manager's region.
     */
    public function pendingReports()
    {
        $tutorIds = Tutor::where('region_id', $this->region_id)->pluck('id');
        return ProgressReport::whereIn('tutor_id', $tutorIds)
            ->where('status', 'submitted')
            ->get();
    }
}
