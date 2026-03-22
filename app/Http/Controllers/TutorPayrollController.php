<?php

namespace App\Http\Controllers;

use App\Models\ClassAttendance;
use App\Models\Region;
use App\Models\Tutor;
use App\Models\TutorPayroll;
use Illuminate\Http\Request;

class TutorPayrollController extends Controller
{
    public function index(Request $request)
    {
        $query = TutorPayroll::with(['tutor.user', 'tutor.region']);

        if ($request->filled('tutor_id')) {
            $query->where('tutor_id', $request->tutor_id);
        }
        if ($request->filled('month')) {
            $query->where('pay_month', $request->month);
        }
        if ($request->filled('year')) {
            $query->where('pay_year', $request->year);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payrolls = $query->orderBy('pay_year', 'desc')
            ->orderBy('pay_month', 'desc')
            ->paginate(20);

        $tutors = Tutor::with('user')->get();

        return view('elpisview.payroll.index', compact('payrolls', 'tutors'));
    }

    /**
     * Generate payroll for a specific month.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'pay_month' => 'required|integer|min:1|max:12',
            'pay_year' => 'required|integer|min:2020|max:2100',
        ]);

        $tutors = Tutor::where('status', 'active')->get();
        $generated = 0;

        foreach ($tutors as $tutor) {
            // Check if payroll already exists
            $existing = TutorPayroll::where('tutor_id', $tutor->id)
                ->where('pay_month', $request->pay_month)
                ->where('pay_year', $request->pay_year)
                ->first();

            if ($existing) {
                $existing->calculate();
                $generated++;
                continue;
            }

            $payroll = TutorPayroll::create([
                'tutor_id' => $tutor->id,
                'pay_month' => $request->pay_month,
                'pay_year' => $request->pay_year,
                'currency' => $tutor->region->currency ?? 'USD',
            ]);

            $payroll->calculate();
            $generated++;
        }

        return redirect()->route('elpisview.payroll.index', [
            'month' => $request->pay_month,
            'year' => $request->pay_year,
        ])->with('success', "Payroll generated/updated for {$generated} tutors.");
    }

    public function show(TutorPayroll $tutorPayroll)
    {
        $tutorPayroll->load(['tutor.user', 'tutor.region']);
        $breakdown = $tutorPayroll->getBreakdown();

        return view('elpisview.payroll.show', compact('tutorPayroll', 'breakdown'));
    }

    /**
     * Add manual adjustment.
     */
    public function adjust(Request $request, TutorPayroll $tutorPayroll)
    {
        $request->validate([
            'adjustments' => 'required|numeric',
            'adjustment_notes' => 'required|string',
        ]);

        $tutorPayroll->update([
            'adjustments' => $request->adjustments,
            'adjustment_notes' => $request->adjustment_notes,
            'net_amount' => $tutorPayroll->gross_amount + $request->adjustments,
        ]);

        return back()->with('success', 'Adjustment applied.');
    }

    public function approve(TutorPayroll $tutorPayroll)
    {
        $tutorPayroll->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Payroll approved.');
    }

    public function markPaid(TutorPayroll $tutorPayroll)
    {
        $tutorPayroll->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        return back()->with('success', 'Payroll marked as paid.');
    }

    /**
     * Monthly summary across all tutors.
     */
    public function summary(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $payrolls = TutorPayroll::where('pay_month', $month)
            ->where('pay_year', $year)
            ->with(['tutor.user', 'tutor.region'])
            ->get();

        $totalGross = $payrolls->sum('gross_amount');
        $totalNet = $payrolls->sum('net_amount');
        $totalHours = $payrolls->sum('total_hours');
        $totalClasses = $payrolls->sum('total_classes');

        $regions = Region::all();

        // Group by region
        $byRegion = $payrolls->groupBy(fn($p) => $p->tutor->region->name ?? 'Unknown');

        return view('elpisview.payroll.summary', compact(
            'payrolls', 'month', 'year', 'totalGross', 'totalNet',
            'totalHours', 'totalClasses', 'regions', 'byRegion'
        ));
    }
}
