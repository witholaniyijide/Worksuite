@extends('layouts.app')

@section('filter-section')
<x-filters.filter-box>
    <div class="d-flex py-2 px-lg-3 px-0 w-100 justify-content-between align-items-center">
        <h4 class="mb-0 f-18 f-w-500 text-darkest-grey d-flex align-items-center">
            <i class="fa fa-user-graduate mr-2 text-primary"></i> Student Profile
        </h4>
        <div>
            <a href="{{ route('elpisview.students.index') }}" class="btn btn-light btn-sm rounded mr-2">
                <i class="fa fa-arrow-left mr-1"></i> Back to Students
            </a>
            <a href="{{ route('elpisview.students.edit', $student) }}" class="btn btn-primary btn-sm rounded">
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
                            {{ strtoupper(substr($student->first_name, 0, 1)) }}{{ strtoupper(substr($student->last_name, 0, 1)) }}
                        </div>
                        <div>
                            <h4 class="mb-1 f-22 font-weight-bold text-darkest-grey">{{ $student->full_name }}</h4>
                            <div class="d-flex align-items-center">
                                <span class="f-13 text-dark-grey mr-3">{{ $student->student_id }}</span>
                                @if($student->status === 'active')
                                    <span class="badge badge-success px-3 py-1">Active</span>
                                @elseif($student->status === 'inactive')
                                    <span class="badge badge-secondary px-3 py-1">Inactive</span>
                                @elseif($student->status === 'graduated')
                                    <span class="badge badge-info px-3 py-1">Graduated</span>
                                @elseif($student->status === 'withdrawn')
                                    <span class="badge badge-warning px-3 py-1">Withdrawn</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="f-11 text-lightest mb-1">Email</label>
                            <p class="f-14 text-darkest-grey mb-0">{{ $student->email ?? '-' }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="f-11 text-lightest mb-1">Date of Birth</label>
                            <p class="f-14 text-darkest-grey mb-0">{{ $student->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->format('F d, Y') : '-' }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="f-11 text-lightest mb-1">Region</label>
                            <p class="f-14 text-darkest-grey mb-0">{{ $student->region->name ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Guardian Information Card -->
            <div class="card border-0 shadow-sm rounded mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 f-14 font-weight-bold text-darkest-grey">
                        <i class="fa fa-user-shield mr-2 text-primary"></i> Guardian Information
                    </h6>
                </div>
                <div class="card-body">
                    @if($student->guardian)
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="f-11 text-lightest mb-1">Name</label>
                                <p class="f-14 text-darkest-grey mb-0">{{ $student->guardian->user->name ?? '-' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="f-11 text-lightest mb-1">Email</label>
                                <p class="f-14 text-darkest-grey mb-0">{{ $student->guardian->user->email ?? '-' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="f-11 text-lightest mb-1">Phone</label>
                                <p class="f-14 text-darkest-grey mb-0">{{ $student->guardian->phone ?? '-' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="f-11 text-lightest mb-1">Relationship</label>
                                <p class="f-14 text-darkest-grey mb-0">{{ ucfirst($student->guardian->relationship ?? '-') }}</p>
                            </div>
                        </div>
                    @else
                        <p class="text-dark-grey mb-0 f-13">No guardian assigned.</p>
                    @endif
                </div>
            </div>

            <!-- Assigned Tutors Card -->
            <div class="card border-0 shadow-sm rounded mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 f-14 font-weight-bold text-darkest-grey">
                        <i class="fa fa-chalkboard-teacher mr-2 text-primary"></i> Assigned Tutors
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover border-0 mb-0">
                            <thead>
                                <tr>
                                    <th class="f-12 text-dark-grey pl-20">Tutor Name</th>
                                    <th class="f-12 text-dark-grey">Subject</th>
                                    <th class="f-12 text-dark-grey">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($student->tutors as $tutor)
                                    <tr>
                                        <td class="pl-20 f-13 text-darkest-grey">{{ $tutor->user->name ?? $tutor->name ?? '-' }}</td>
                                        <td class="f-13 text-darkest-grey">{{ $tutor->pivot->subject ?? '-' }}</td>
                                        <td>
                                            @if($tutor->status === 'active')
                                                <span class="badge badge-success">Active</span>
                                            @elseif($tutor->status === 'inactive')
                                                <span class="badge badge-secondary">Inactive</span>
                                            @elseif($tutor->status === 'on_leave')
                                                <span class="badge badge-warning">On Leave</span>
                                            @else
                                                <span class="badge badge-secondary">{{ ucfirst($tutor->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-3">
                                            <p class="text-dark-grey mb-0 f-13">No tutors assigned.</p>
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
                    <a href="{{ route('elpisview.attendance.index', ['student' => $student->id]) }}" class="f-12 text-primary">
                        View All <i class="fa fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover border-0 mb-0">
                            <thead>
                                <tr>
                                    <th class="f-12 text-dark-grey pl-20">Date</th>
                                    <th class="f-12 text-dark-grey">Time</th>
                                    <th class="f-12 text-dark-grey">Subject</th>
                                    <th class="f-12 text-dark-grey">Duration</th>
                                    <th class="f-12 text-dark-grey">Status</th>
                                    <th class="f-12 text-dark-grey">Flags</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($student->classAttendances->take(5) as $attendance)
                                    <tr>
                                        <td class="pl-20 f-13 text-darkest-grey">{{ $attendance->class_date->format('M d, Y') }}</td>
                                        <td class="f-13 text-darkest-grey">{{ $attendance->class_time_range ?? '-' }}</td>
                                        <td class="f-13 text-darkest-grey">{{ $attendance->subject->name ?? '-' }}</td>
                                        <td class="f-13 text-darkest-grey">{{ $attendance->duration_hours }} hrs</td>
                                        <td>
                                            @if($attendance->status === 'approved')
                                                <span class="badge badge-success">Approved</span>
                                            @elseif($attendance->status === 'disputed')
                                                <span class="badge badge-danger">Rejected</span>
                                            @else
                                                <span class="badge badge-warning">{{ ucfirst($attendance->status) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($attendance->is_stand_in)
                                                <span class="badge badge-info f-11">Stand-in</span>
                                            @endif
                                            @if($attendance->is_late)
                                                <span class="badge badge-danger f-11">Late</span>
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

            <!-- Recent Progress Reports Card -->
            <div class="card border-0 shadow-sm rounded mb-4">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 f-14 font-weight-bold text-darkest-grey">
                        <i class="fa fa-chart-line mr-2 text-primary"></i> Recent Progress Reports
                    </h6>
                    <a href="{{ route('elpisview.reports.index', ['student' => $student->id]) }}" class="f-12 text-primary">
                        View All <i class="fa fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover border-0 mb-0">
                            <thead>
                                <tr>
                                    <th class="f-12 text-dark-grey pl-20">Period</th>
                                    <th class="f-12 text-dark-grey">Subject</th>
                                    <th class="f-12 text-dark-grey">Overall Performance</th>
                                    <th class="f-12 text-dark-grey">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($student->progressReports->take(5) as $report)
                                    <tr>
                                        <td class="pl-20 f-13 text-darkest-grey">{{ $report->period ?? '-' }}</td>
                                        <td class="f-13 text-darkest-grey">{{ $report->subject->name ?? '-' }}</td>
                                        <td class="f-13 text-darkest-grey">{{ $report->overall_performance ?? '-' }}</td>
                                        <td>
                                            @if($report->status === 'published')
                                                <span class="badge badge-success">Published</span>
                                            @elseif($report->status === 'draft')
                                                <span class="badge badge-secondary">Draft</span>
                                            @else
                                                <span class="badge badge-warning">{{ ucfirst($report->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-3">
                                            <p class="text-dark-grey mb-0 f-13">No progress reports found.</p>
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

            <!-- Quick Stats Card -->
            <div class="card border-0 shadow-sm rounded mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 f-14 font-weight-bold text-darkest-grey">Quick Stats</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="f-13 text-dark-grey">Total Classes Attended</span>
                        <span class="f-15 font-weight-bold text-darkest-grey">{{ $student->classAttendances->where('status', 'approved')->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="f-13 text-dark-grey">Assigned Tutors</span>
                        <span class="f-15 font-weight-bold text-darkest-grey">{{ $student->tutors->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="f-13 text-dark-grey">Progress Reports</span>
                        <span class="f-15 font-weight-bold text-darkest-grey">{{ $student->progressReports->count() }}</span>
                    </div>
                </div>
            </div>

            <!-- Notes Card -->
            <div class="card border-0 shadow-sm rounded mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 f-14 font-weight-bold text-darkest-grey">
                        <i class="fa fa-sticky-note mr-2 text-primary"></i> Notes
                    </h6>
                </div>
                <div class="card-body">
                    @if($student->notes)
                        <p class="f-13 text-dark-grey mb-0" style="white-space:pre-wrap;">{{ $student->notes }}</p>
                    @else
                        <p class="f-13 text-dark-grey mb-0">No notes available.</p>
                    @endif
                </div>
            </div>

            <!-- Actions Card -->
            <div class="card border-0 shadow-sm rounded mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 f-14 font-weight-bold text-darkest-grey">Actions</h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('elpisview.students.edit', $student) }}" class="btn btn-primary btn-sm btn-block rounded mb-2">
                        <i class="fa fa-edit mr-1"></i> Edit Student
                    </a>
                    <form action="{{ route('elpisview.students.destroy', $student) }}" method="POST" id="delete-student-form">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-danger btn-sm btn-block rounded mb-2" id="delete-student-btn">
                            <i class="fa fa-trash mr-1"></i> Delete Student
                        </button>
                    </form>
                    <a href="{{ route('elpisview.attendance.index', ['student' => $student->id]) }}" class="btn btn-outline-secondary btn-sm btn-block rounded mb-2">
                        <i class="fa fa-clipboard-check mr-1"></i> View Attendance
                    </a>
                    <a href="{{ route('elpisview.reports.index', ['student' => $student->id]) }}" class="btn btn-outline-secondary btn-sm btn-block rounded">
                        <i class="fa fa-chart-line mr-1"></i> View Reports
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
        $('#delete-student-btn').on('click', function () {
            Swal.fire({
                title: 'Are you sure?',
                text: 'This action cannot be undone. All student data will be permanently deleted.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel'
            }).then(function (result) {
                if (result.isConfirmed) {
                    $('#delete-student-form').submit();
                }
            });
        });
    });
</script>
@endpush
