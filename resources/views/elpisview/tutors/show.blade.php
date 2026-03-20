@extends('layouts.app')

@section('filter-section')
<x-filters.filter-box>
    <div class="d-flex py-2 px-lg-3 px-0 w-100 justify-content-between align-items-center">
        <h4 class="mb-0 f-18 f-w-500 text-darkest-grey d-flex align-items-center">
            <i class="fa fa-chalkboard-teacher mr-2 text-primary"></i> Tutor Profile
        </h4>
        <div>
            <a href="{{ route('elpisview.tutors.index') }}" class="btn btn-light btn-sm rounded mr-2">
                <i class="fa fa-arrow-left mr-1"></i> Back to Tutors
            </a>
            <a href="{{ route('elpisview.tutors.edit', $tutor) }}" class="btn btn-primary btn-sm rounded">
                <i class="fa fa-edit mr-1"></i> Edit
            </a>
        </div>
    </div>
</x-filters.filter-box>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8">

            <!-- Profile Card -->
            <div class="card border-0 shadow-sm rounded mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3" style="width:64px;height:64px;font-size:22px;font-weight:700;">
                            {{ strtoupper(substr($tutor->name, 0, 1)) }}{{ strtoupper(substr($tutor->name, strpos($tutor->name, ' ') + 1, 1)) }}
                        </div>
                        <div>
                            <h4 class="mb-1 f-22 font-weight-bold text-darkest-grey">{{ $tutor->name }}</h4>
                            <div class="d-flex align-items-center">
                                <span class="f-13 text-dark-grey mr-3">{{ $tutor->staff_id }}</span>
                                @if($tutor->status === 'active')
                                    <span class="badge badge-success px-3 py-1">Active</span>
                                @elseif($tutor->status === 'inactive')
                                    <span class="badge badge-secondary px-3 py-1">Inactive</span>
                                @elseif($tutor->status === 'on_leave')
                                    <span class="badge badge-warning px-3 py-1">On Leave</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="f-11 text-lightest mb-1">Email</label>
                            <p class="f-14 text-darkest-grey mb-0">{{ $tutor->email ?? '-' }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="f-11 text-lightest mb-1">Phone</label>
                            <p class="f-14 text-darkest-grey mb-0">{{ $tutor->phone ?? '-' }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="f-11 text-lightest mb-1">Region</label>
                            <p class="f-14 text-darkest-grey mb-0">{{ $tutor->region->name ?? '-' }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="f-11 text-lightest mb-1">Hire Date</label>
                            <p class="f-14 text-darkest-grey mb-0">{{ $tutor->hire_date ? \Carbon\Carbon::parse($tutor->hire_date)->format('F d, Y') : '-' }}</p>
                        </div>
                    </div>

                    @if($tutor->bio)
                        <div class="mb-3">
                            <label class="f-11 text-lightest mb-1">Bio</label>
                            <p class="f-13 text-darkest-grey mb-0" style="white-space:pre-wrap;">{{ $tutor->bio }}</p>
                        </div>
                    @endif

                    @if($tutor->qualifications)
                        <div class="mb-0">
                            <label class="f-11 text-lightest mb-1">Qualifications</label>
                            <p class="f-13 text-darkest-grey mb-0" style="white-space:pre-wrap;">{{ $tutor->qualifications }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Assigned Students Card -->
            <div class="card border-0 shadow-sm rounded mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 f-14 font-weight-bold text-darkest-grey">
                        <i class="fa fa-user-graduate mr-2 text-primary"></i> Assigned Students
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover border-0 mb-0">
                            <thead>
                                <tr>
                                    <th class="f-12 text-dark-grey pl-20">Student Name</th>
                                    <th class="f-12 text-dark-grey">Student ID</th>
                                    <th class="f-12 text-dark-grey">Subject</th>
                                    <th class="f-12 text-dark-grey">Guardian</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tutor->students as $student)
                                    <tr>
                                        <td class="pl-20">
                                            <a href="{{ route('elpisview.students.show', $student) }}" class="f-13 text-darkest-grey font-weight-bold">
                                                {{ $student->full_name }}
                                            </a>
                                        </td>
                                        <td class="f-13 text-darkest-grey">{{ $student->student_id }}</td>
                                        <td class="f-13 text-darkest-grey">{{ $student->pivot->subject ?? '-' }}</td>
                                        <td class="f-13 text-darkest-grey">{{ $student->guardian->user->name ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-3">
                                            <p class="text-dark-grey mb-0 f-13">No students assigned.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pay Rates Card -->
            <div class="card border-0 shadow-sm rounded mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 f-14 font-weight-bold text-darkest-grey">
                        <i class="fa fa-money-bill-wave mr-2 text-primary"></i> Pay Rates
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover border-0 mb-0">
                            <thead>
                                <tr>
                                    <th class="f-12 text-dark-grey pl-20">Subject</th>
                                    <th class="f-12 text-dark-grey">Rate Per Hour</th>
                                    <th class="f-12 text-dark-grey">Effective From</th>
                                    <th class="f-12 text-dark-grey">Effective To</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tutor->payRates as $payRate)
                                    <tr>
                                        <td class="pl-20 f-13 text-darkest-grey">{{ $payRate->subject->name ?? '-' }}</td>
                                        <td class="f-13 text-darkest-grey font-weight-bold">{{ $payRate->currency ?? '$' }}{{ number_format($payRate->rate_per_hour, 2) }}</td>
                                        <td class="f-13 text-darkest-grey">{{ $payRate->effective_from ? \Carbon\Carbon::parse($payRate->effective_from)->format('M d, Y') : '-' }}</td>
                                        <td class="f-13 text-darkest-grey">{{ $payRate->effective_to ? \Carbon\Carbon::parse($payRate->effective_to)->format('M d, Y') : '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-3">
                                            <p class="text-dark-grey mb-0 f-13">No pay rates configured.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Attendance Card -->
            <div class="card border-0 shadow-sm rounded mb-4">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 f-14 font-weight-bold text-darkest-grey">
                        <i class="fa fa-clipboard-check mr-2 text-primary"></i> Recent Attendance
                    </h6>
                    <a href="{{ route('elpisview.attendance.index', ['tutor' => $tutor->id]) }}" class="f-12 text-primary">
                        View All <i class="fa fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover border-0 mb-0">
                            <thead>
                                <tr>
                                    <th class="f-12 text-dark-grey pl-20">Date</th>
                                    <th class="f-12 text-dark-grey">Student</th>
                                    <th class="f-12 text-dark-grey">Subject</th>
                                    <th class="f-12 text-dark-grey">Duration</th>
                                    <th class="f-12 text-dark-grey">Earnings</th>
                                    <th class="f-12 text-dark-grey">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tutor->classAttendances->take(5) as $attendance)
                                    <tr>
                                        <td class="pl-20 f-13 text-darkest-grey">{{ $attendance->class_date->format('M d, Y') }}</td>
                                        <td class="f-13 text-darkest-grey">{{ $attendance->student->full_name ?? '-' }}</td>
                                        <td class="f-13 text-darkest-grey">{{ $attendance->subject->name ?? '-' }}</td>
                                        <td class="f-13 text-darkest-grey">{{ $attendance->duration_hours }} hrs</td>
                                        <td class="f-13 text-success font-weight-bold">{{ $attendance->currency ?? '$' }}{{ number_format($attendance->amount_earned, 2) }}</td>
                                        <td>
                                            @if($attendance->status === 'approved')
                                                <span class="badge badge-success">Approved</span>
                                            @elseif($attendance->status === 'disputed')
                                                <span class="badge badge-danger">Rejected</span>
                                            @else
                                                <span class="badge badge-warning">{{ ucfirst($attendance->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-3">
                                            <p class="text-dark-grey mb-0 f-13">No attendance records found.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column -->
        <div class="col-lg-4">

            <!-- Earnings Card -->
            <div class="card border-0 shadow-sm rounded mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 f-14 font-weight-bold text-darkest-grey">
                        <i class="fa fa-wallet mr-2 text-primary"></i> Earnings
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <label class="f-11 text-lightest mb-1">This Month's Earnings</label>
                        <h3 class="f-22 font-weight-bold text-success mb-0">${{ number_format($currentMonthEarnings, 2) }}</h3>
                    </div>
                    <div class="d-flex justify-content-between align-items-center border-top pt-3">
                        <span class="f-13 text-dark-grey">Total Classes This Month</span>
                        <span class="f-15 font-weight-bold text-darkest-grey">
                            {{ $tutor->classAttendances->filter(function($a) { return $a->class_date->isCurrentMonth(); })->count() }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Quick Stats Card -->
            <div class="card border-0 shadow-sm rounded mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 f-14 font-weight-bold text-darkest-grey">Quick Stats</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="f-13 text-dark-grey">Active Students</span>
                        <span class="f-15 font-weight-bold text-darkest-grey">{{ $tutor->students->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="f-13 text-dark-grey">Total Pay Rates</span>
                        <span class="f-15 font-weight-bold text-darkest-grey">{{ $tutor->payRates->count() }}</span>
                    </div>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="card border-0 shadow-sm rounded mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 f-14 font-weight-bold text-darkest-grey">Actions</h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('elpisview.tutors.edit', $tutor) }}" class="btn btn-primary btn-sm btn-block rounded mb-2">
                        <i class="fa fa-edit mr-1"></i> Edit Tutor
                    </a>
                    <form action="{{ route('elpisview.tutors.destroy', $tutor) }}" method="POST" id="delete-tutor-form">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-danger btn-sm btn-block rounded mb-2" id="delete-tutor-btn">
                            <i class="fa fa-trash mr-1"></i> Delete Tutor
                        </button>
                    </form>
                    <a href="{{ route('elpisview.attendance.index', ['tutor' => $tutor->id]) }}" class="btn btn-outline-secondary btn-sm btn-block rounded">
                        <i class="fa fa-clipboard-check mr-1"></i> View Attendance
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#delete-tutor-btn').on('click', function () {
            Swal.fire({
                title: 'Are you sure?',
                text: 'This action cannot be undone. All tutor data will be permanently deleted.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel'
            }).then(function (result) {
                if (result.isConfirmed) {
                    $('#delete-tutor-form').submit();
                }
            });
        });
    });
</script>
@endpush
