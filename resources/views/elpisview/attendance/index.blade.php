@extends('layouts.app')

@push('styles')
<style>
    .ev-tab { display: inline-block; padding: 10px 20px; border-bottom: 3px solid transparent; font-weight: 500; color: #6b7280; text-decoration: none; transition: all 0.2s; }
    .ev-tab:hover { color: #4B49AC; text-decoration: none; }
    .ev-tab.active { color: #4B49AC; border-bottom-color: #4B49AC; font-weight: 600; }
    .ev-stat-card { transition: transform 0.2s ease; border-radius: 10px; }
    .ev-stat-card:hover { transform: translateY(-2px); }
    .ev-attendance-row { transition: background-color 0.15s; border-radius: 8px; padding: 12px; margin-bottom: 8px; }
    .ev-attendance-row:hover { background-color: rgba(75, 73, 172, 0.04); }
    .badge-standin { background-color: #dbeafe; color: #1d4ed8; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; }
    .badge-late { background-color: #fee2e2; color: #dc2626; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; }
    .badge-approved { background-color: #d1fae5; color: #059669; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; }
    .badge-pending-ev { background-color: #fef3c7; color: #d97706; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; }
    .badge-rejected { background-color: #fee2e2; color: #dc2626; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; }
</style>
@endpush

@section('filter-section')
<x-filters.filter-box>
    <div class="d-flex py-2 px-lg-3 px-0 w-100 justify-content-between align-items-center">
        <h4 class="mb-0 f-18 f-w-500 text-darkest-grey d-flex align-items-center">
            <i class="fa fa-clipboard-check mr-2 text-primary"></i> Class Attendance
        </h4>
        <div class="d-flex gap-2">
            <a href="{{ route('elpisview.attendance.create') }}" class="btn btn-primary btn-sm rounded mr-2">
                <i class="fa fa-plus mr-1"></i> Submit Attendance
            </a>
            <a href="{{ route('elpisview.attendance.create', ['standin' => 1]) }}" class="btn btn-outline-primary btn-sm rounded">
                <i class="fa fa-user-plus mr-1"></i> Stand-in Attendance
            </a>
        </div>
    </div>
</x-filters.filter-box>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="row">
        <!-- Stats Cards -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card ev-stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="f-12 text-lightest mb-1">This Month</p>
                    <h3 class="f-22 font-weight-bold text-darkest-grey mb-0">{{ $stats['total'] }}</h3>
                    <p class="f-11 text-lightest mb-0">Total submissions</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card ev-stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="f-12 text-lightest mb-1">Approved</p>
                    <h3 class="f-22 font-weight-bold text-success mb-0">{{ $stats['approved'] }}</h3>
                    <p class="f-11 text-lightest mb-0">This month</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card ev-stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="f-12 text-lightest mb-1">Pending</p>
                    <h3 class="f-22 font-weight-bold text-warning mb-0">{{ $stats['pending'] }}</h3>
                    <p class="f-11 text-lightest mb-0">Awaiting approval</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card ev-stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="f-12 text-lightest mb-1">Stand-in</p>
                    <h3 class="f-22 font-weight-bold text-info mb-0">{{ $stats['standin_count'] }}</h3>
                    <p class="f-11 text-lightest mb-0">This month</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs & Filters Card -->
    <div class="card border-0 shadow-sm rounded">
        <!-- Tabs -->
        <div class="card-header bg-white border-bottom d-flex">
            <a href="{{ route('elpisview.attendance.index', ['tab' => 'my-students', 'month' => $month, 'status' => $status]) }}"
               class="ev-tab {{ $tab === 'my-students' ? 'active' : '' }}">
                <i class="fa fa-users mr-1"></i> My Students
            </a>
            <a href="{{ route('elpisview.attendance.index', ['tab' => 'stand-in', 'month' => $month, 'status' => $status]) }}"
               class="ev-tab {{ $tab === 'stand-in' ? 'active' : '' }}">
                <i class="fa fa-user-plus mr-1"></i> Stand-in
                @if($stats['standin_count'] > 0)
                    <span class="badge badge-info ml-1">{{ $stats['standin_count'] }}</span>
                @endif
            </a>
        </div>

        <!-- Filters -->
        <div class="card-body bg-light border-bottom py-3">
            <form method="GET" class="d-flex flex-wrap align-items-center gap-3">
                <input type="hidden" name="tab" value="{{ $tab }}">

                <div class="d-flex align-items-center mr-3">
                    <label class="f-13 text-dark-grey mb-0 mr-2">Month:</label>
                    <input type="month" name="month" value="{{ $month }}" class="form-control form-control-sm" style="width: 160px;">
                </div>

                <div class="d-flex align-items-center mr-3">
                    <label class="f-13 text-dark-grey mb-0 mr-2">Status:</label>
                    <select name="status" class="form-control form-control-sm" style="width: 130px;">
                        <option value="">All Status</option>
                        <option value="submitted" {{ $status === 'submitted' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="disputed" {{ $status === 'disputed' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary btn-sm rounded">Filter</button>
                <a href="{{ route('elpisview.attendance.index', ['tab' => $tab]) }}" class="btn btn-light btn-sm rounded ml-1">Reset</a>
            </form>
        </div>

        @if($tab === 'stand-in')
            <div class="bg-light-blue border-bottom px-4 py-2">
                <small class="text-info"><i class="fa fa-info-circle mr-1"></i> Stand-in attendance is for students not assigned to the submitting tutor. This happens when covering for another tutor.</small>
            </div>
        @endif

        <!-- Attendance List -->
        <div class="card-body">
            @if($attendances->isEmpty())
                <div class="text-center py-5">
                    <i class="fa fa-clipboard f-30 text-lightest mb-3 d-block"></i>
                    <h5 class="text-dark-grey">No Attendance Records</h5>
                    <p class="text-lightest f-13">
                        @if($tab === 'stand-in')
                            No stand-in attendance records found for the selected filters.
                        @else
                            No attendance records found for the selected filters.
                        @endif
                    </p>
                    @if($tab === 'stand-in')
                        <a href="{{ route('elpisview.attendance.create', ['standin' => 1]) }}" class="btn btn-outline-primary btn-sm rounded">
                            <i class="fa fa-plus mr-1"></i> Submit Stand-in Attendance
                        </a>
                    @else
                        <a href="{{ route('elpisview.attendance.create') }}" class="btn btn-primary btn-sm rounded">
                            <i class="fa fa-plus mr-1"></i> Submit Attendance
                        </a>
                    @endif
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="f-12 text-dark-grey">Student</th>
                                <th class="f-12 text-dark-grey">Tutor</th>
                                <th class="f-12 text-dark-grey">Date</th>
                                <th class="f-12 text-dark-grey">Time</th>
                                <th class="f-12 text-dark-grey">Duration</th>
                                <th class="f-12 text-dark-grey">Subject</th>
                                <th class="f-12 text-dark-grey">Status</th>
                                <th class="f-12 text-dark-grey">Flags</th>
                                <th class="f-12 text-dark-grey">Monthly</th>
                                <th class="f-12 text-dark-grey text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendances as $attendance)
                                <tr>
                                    <td class="f-13">
                                        <strong>{{ $attendance->student->first_name }} {{ $attendance->student->last_name }}</strong>
                                    </td>
                                    <td class="f-13">{{ $attendance->tutor->name ?? '-' }}</td>
                                    <td class="f-13">{{ $attendance->class_date->format('M d, Y') }}</td>
                                    <td class="f-13">{{ $attendance->class_time_range ?? '-' }}</td>
                                    <td class="f-13">{{ $attendance->duration_hours }}h</td>
                                    <td class="f-13">{{ $attendance->subject->name ?? '-' }}</td>
                                    <td>
                                        @if($attendance->status === 'approved')
                                            <span class="badge-approved">Approved</span>
                                        @elseif($attendance->status === 'submitted')
                                            <span class="badge-pending-ev">Pending</span>
                                        @elseif($attendance->status === 'disputed')
                                            <span class="badge-rejected">Rejected</span>
                                        @else
                                            <span class="badge badge-secondary">{{ ucfirst($attendance->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($attendance->is_stand_in)
                                            <span class="badge-standin">Stand-in</span>
                                        @endif
                                        @if($attendance->is_late)
                                            <span class="badge-late">Late</span>
                                        @endif
                                        @if($attendance->is_rescheduled)
                                            <span class="badge badge-warning" style="font-size:11px">Rescheduled</span>
                                        @endif
                                    </td>
                                    <td class="f-13">
                                        @if($attendance->is_stand_in)
                                            <span class="text-info f-12">Stand-in</span>
                                        @elseif($attendance->monthly_position > 0)
                                            <span class="text-success f-12 font-weight-bold">{{ $attendance->monthly_position }}/{{ $attendance->monthly_total }}</span>
                                        @else
                                            <span class="text-lightest f-12">-</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('elpisview.attendance.show', $attendance) }}" class="btn btn-sm btn-outline-primary rounded" title="View">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($attendances->hasPages())
                    <div class="mt-3 px-3">
                        {{ $attendances->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
