<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TutorPayRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'tutor_id', 'subject_id', 'student_id', 'rate_per_hour',
        'currency', 'effective_from', 'effective_to', 'notes',
    ];

    protected $casts = [
        'rate_per_hour' => 'decimal:2',
        'effective_from' => 'date',
        'effective_to' => 'date',
    ];

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(Tutor::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
