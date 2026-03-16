<?php

namespace App\Http\Controllers;

use App\Models\Guardian;
use App\Models\Region;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Tutor;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['region', 'guardian.user']);

        if ($request->filled('region_id')) {
            $query->where('region_id', $request->region_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('first_name')->paginate(20);
        $regions = Region::all();

        return view('elpisview.students.index', compact('students', 'regions'));
    }

    public function create()
    {
        $regions = Region::all();
        $guardians = Guardian::with('user')->get();
        $subjects = Subject::where('is_active', true)->get();
        $tutors = Tutor::with('user')->where('status', 'active')->get();

        return view('elpisview.students.create', compact('regions', 'guardians', 'subjects', 'tutors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'date_of_birth' => 'nullable|date',
            'region_id' => 'required|exists:regions,id',
            'guardian_id' => 'required|exists:guardians,id',
            'status' => 'in:active,inactive,graduated,withdrawn',
            'notes' => 'nullable|string',
        ]);

        $validated['student_id'] = Student::generateStudentId();
        $student = Student::create($validated);

        // Assign tutors/subjects if provided
        if ($request->has('assignments')) {
            foreach ($request->assignments as $assignment) {
                $student->tutors()->attach($assignment['tutor_id'], [
                    'subject_id' => $assignment['subject_id'],
                    'is_active' => true,
                ]);
            }
        }

        return redirect()->route('elpisview.students.show', $student)
            ->with('success', 'Student created successfully.');
    }

    public function show(Student $student)
    {
        $student->load(['region', 'guardian.user', 'tutors.user', 'classAttendances' => function ($q) {
            $q->latest('class_date')->limit(20);
        }, 'progressReports' => function ($q) {
            $q->latest()->limit(10);
        }]);

        return view('elpisview.students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $regions = Region::all();
        $guardians = Guardian::with('user')->get();
        $subjects = Subject::where('is_active', true)->get();
        $tutors = Tutor::with('user')->where('status', 'active')->get();

        return view('elpisview.students.edit', compact('student', 'regions', 'guardians', 'subjects', 'tutors'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'date_of_birth' => 'nullable|date',
            'region_id' => 'required|exists:regions,id',
            'guardian_id' => 'required|exists:guardians,id',
            'status' => 'in:active,inactive,graduated,withdrawn',
            'notes' => 'nullable|string',
        ]);

        $student->update($validated);

        return redirect()->route('elpisview.students.show', $student)
            ->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('elpisview.students.index')
            ->with('success', 'Student deleted successfully.');
    }
}
