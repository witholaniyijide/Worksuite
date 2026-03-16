<?php

namespace App\Http\Controllers;

use App\Models\Region;
use App\Models\Tutor;
use App\Models\TutorPayRate;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;

class TutorController extends Controller
{
    public function index(Request $request)
    {
        $query = Tutor::with(['user', 'region']);

        if ($request->filled('region_id')) {
            $query->where('region_id', $request->region_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('staff_id', 'like', "%{$search}%");
        }

        $tutors = $query->paginate(20);
        $regions = Region::all();

        return view('elpisview.tutors.index', compact('tutors', 'regions'));
    }

    public function create()
    {
        $regions = Region::all();
        $subjects = Subject::where('is_active', true)->get();

        return view('elpisview.tutors.create', compact('regions', 'subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'region_id' => 'required|exists:regions,id',
            'staff_id' => 'nullable|string|unique:tutors,staff_id',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string',
            'qualifications' => 'nullable|string',
            'hire_date' => 'nullable|date',
        ]);

        // Create user account
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt('password'), // temporary
        ]);

        $tutor = Tutor::create([
            'user_id' => $user->id,
            'staff_id' => $validated['staff_id'],
            'region_id' => $validated['region_id'],
            'phone' => $validated['phone'],
            'bio' => $validated['bio'],
            'qualifications' => $validated['qualifications'],
            'hire_date' => $validated['hire_date'],
        ]);

        // Add pay rates if provided
        if ($request->has('pay_rates')) {
            foreach ($request->pay_rates as $rate) {
                TutorPayRate::create([
                    'tutor_id' => $tutor->id,
                    'subject_id' => $rate['subject_id'] ?? null,
                    'rate_per_hour' => $rate['rate_per_hour'],
                    'currency' => $rate['currency'] ?? 'USD',
                    'effective_from' => $rate['effective_from'] ?? now()->toDateString(),
                ]);
            }
        }

        return redirect()->route('elpisview.tutors.show', $tutor)
            ->with('success', 'Tutor created successfully.');
    }

    public function show(Tutor $tutor)
    {
        $tutor->load(['user', 'region', 'students.guardian', 'payRates.subject',
            'classAttendances' => fn($q) => $q->latest('class_date')->limit(20),
        ]);

        // Calculate current month earnings
        $currentMonthEarnings = $tutor->classAttendances()
            ->whereMonth('class_date', now()->month)
            ->whereYear('class_date', now()->year)
            ->where('status', '!=', 'disputed')
            ->sum('amount_earned');

        return view('elpisview.tutors.show', compact('tutor', 'currentMonthEarnings'));
    }

    public function edit(Tutor $tutor)
    {
        $regions = Region::all();
        $subjects = Subject::where('is_active', true)->get();

        return view('elpisview.tutors.edit', compact('tutor', 'regions', 'subjects'));
    }

    public function update(Request $request, Tutor $tutor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $tutor->user_id,
            'region_id' => 'required|exists:regions,id',
            'staff_id' => 'nullable|string|unique:tutors,staff_id,' . $tutor->id,
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string',
            'qualifications' => 'nullable|string',
            'hire_date' => 'nullable|date',
            'status' => 'in:active,inactive,on_leave',
        ]);

        $tutor->user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        $tutor->update(collect($validated)->except(['name', 'email'])->toArray());

        return redirect()->route('elpisview.tutors.show', $tutor)
            ->with('success', 'Tutor updated successfully.');
    }

    public function destroy(Tutor $tutor)
    {
        $tutor->delete();

        return redirect()->route('elpisview.tutors.index')
            ->with('success', 'Tutor removed successfully.');
    }
}
