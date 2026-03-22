<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tutor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'staff_id', 'region_id', 'phone', 'bio',
        'qualifications', 'hire_date', 'status',
    ];

    protected $casts = [
        'hire_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'student_tutor')
            ->withPivot('subject_id', 'is_active')
            ->withTimestamps();
    }

    public function payRates(): HasMany
    {
        return $this->hasMany(TutorPayRate::class);
    }

    public function classAttendances(): HasMany
    {
        return $this->hasMany(ClassAttendance::class);
    }

    public function progressReports(): HasMany
    {
        return $this->hasMany(ProgressReport::class);
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(TutorPayroll::class);
    }

    public function getNameAttribute(): string
    {
        return $this->user->name ?? '';
    }

    public function getEmailAttribute(): string
    {
        return $this->user->email ?? '';
    }

    /**
     * Get the applicable pay rate for a given student/subject/date.
     */
    public function getPayRate(?int $subjectId = null, ?int $studentId = null, ?string $date = null): ?TutorPayRate
    {
        $date = $date ?? now()->toDateString();

        return TutorPayRate::where('tutor_id', $this->id)
            ->where('effective_from', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('effective_to')->orWhere('effective_to', '>=', $date);
            })
            ->when($studentId, fn($q) => $q->where('student_id', $studentId))
            ->when($subjectId, fn($q) => $q->where('subject_id', $subjectId))
            ->orderByRaw('student_id IS NULL ASC') // prefer student-specific rate
            ->orderByRaw('subject_id IS NULL ASC') // then subject-specific
            ->orderBy('effective_from', 'desc')
            ->first();
    }

    public function getRegionalManager(): ?RegionalManager
    {
        return RegionalManager::where('region_id', $this->region_id)->first();
    }
}
