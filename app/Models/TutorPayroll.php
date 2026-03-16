<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TutorPayroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'tutor_id', 'pay_month', 'pay_year', 'total_hours', 'total_classes',
        'gross_amount', 'adjustments', 'adjustment_notes', 'net_amount',
        'currency', 'status', 'approved_by', 'approved_at', 'paid_at',
    ];

    protected $casts = [
        'total_hours' => 'decimal:2',
        'gross_amount' => 'decimal:2',
        'adjustments' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(Tutor::class);
    }

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Calculate payroll from approved class attendances.
     */
    public function calculate(): void
    {
        $attendances = ClassAttendance::where('tutor_id', $this->tutor_id)
            ->whereMonth('class_date', $this->pay_month)
            ->whereYear('class_date', $this->pay_year)
            ->where('status', 'approved')
            ->get();

        $this->total_hours = $attendances->sum('duration_hours');
        $this->total_classes = $attendances->count();
        $this->gross_amount = $attendances->sum('amount_earned');
        $this->net_amount = $this->gross_amount + $this->adjustments;
        $this->status = 'calculated';
        $this->save();
    }

    public function getPeriodLabelAttribute(): string
    {
        return \Carbon\Carbon::create($this->pay_year, $this->pay_month, 1)->format('F Y');
    }

    /**
     * Get breakdown by student for this payroll period.
     */
    public function getBreakdown()
    {
        return ClassAttendance::where('tutor_id', $this->tutor_id)
            ->whereMonth('class_date', $this->pay_month)
            ->whereYear('class_date', $this->pay_year)
            ->where('status', 'approved')
            ->with(['student', 'subject'])
            ->get()
            ->groupBy('student_id');
    }
}
