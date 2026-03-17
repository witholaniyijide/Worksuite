@extends('layouts.app')

@push('styles')
<style>
    .badge-standin { background-color: #dbeafe; color: #1d4ed8; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; }
    .badge-late { background-color: #fee2e2; color: #dc2626; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; }
</style>
@endpush

@section('filter-section')
<x-filters.filter-box>
    <div class="d-flex py-2 px-lg-3 px-0 w-100 justify-content-between align-items-center">
        <h4 class="mb-0 f-18 f-w-500 text-darkest-grey d-flex align-items-center">
            <i class="fa fa-clock mr-2 text-warning"></i> Pending Attendance Approval
        </h4>
        <a href="{{ route('elpisview.attendance.index') }}" class="btn btn-light btn-sm rounded">
            <i class="fa fa-arrow-left mr-1"></i> All Attendance
        </a>
    </div>
</x-filters.filter-box>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="card border-0 shadow-sm rounded">
        <!-- Filters -->
        <div class="card-body bg-light border-bottom py-3">
            <form method="GET" class="d-flex flex-wrap align-items-center gap-3">
                <div class="d-flex align-items-center mr-3">
                    <label class="f-13 text-dark-grey mb-0 mr-2">Tutor:</label>
                    <select name="tutor_id" class="form-control form-control-sm" style="width: 180px;">
                        <option value="">All Tutors</option>
                        @foreach($tutors as $tutor)
                            <option value="{{ $tutor->id }}" {{ request('tutor_id') == $tutor->id ? 'selected' : '' }}>
                                {{ $tutor->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-sm rounded">Filter</button>
                <a href="{{ route('elpisview.attendance.pending') }}" class="btn btn-light btn-sm rounded ml-1">Reset</a>
            </form>
        </div>

        <!-- Bulk Actions -->
        @if($attendances->isNotEmpty())
        <div class="card-body border-bottom py-2 bg-white">
            <form id="bulkApproveForm" action="{{ route('elpisview.attendance.bulk-approve') }}" method="POST" class="d-flex align-items-center">
                @csrf
                <div class="custom-control custom-checkbox mr-3">
                    <input type="checkbox" class="custom-control-input" id="selectAll">
                    <label class="custom-control-label f-12 text-dark-grey" for="selectAll">Select All</label>
                </div>
                <button type="submit" class="btn btn-success btn-sm rounded">
                    <i class="fa fa-check-double mr-1"></i> Bulk Approve Selected
                </button>
            </form>
        </div>
        @endif

        <!-- Pending List -->
        <div class="card-body">
            @if($attendances->isEmpty())
                <div class="text-center py-5">
                    <i class="fa fa-check-circle f-30 text-success mb-3 d-block"></i>
                    <h5 class="text-dark-grey">All Caught Up!</h5>
                    <p class="text-lightest f-13">No pending attendance records to review.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="f-12" style="width:30px;"></th>
                                <th class="f-12 text-dark-grey">Student</th>
                                <th class="f-12 text-dark-grey">Tutor</th>
                                <th class="f-12 text-dark-grey">Date</th>
                                <th class="f-12 text-dark-grey">Time</th>
                                <th class="f-12 text-dark-grey">Subject</th>
                                <th class="f-12 text-dark-grey">Flags</th>
                                <th class="f-12 text-dark-grey text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendances as $attendance)
                                <tr>
                                    <td>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input bulk-check" form="bulkApproveForm"
                                                   name="ids[]" value="{{ $attendance->id }}" id="check-{{ $attendance->id }}">
                                            <label class="custom-control-label" for="check-{{ $attendance->id }}"></label>
                                        </div>
                                    </td>
                                    <td class="f-13"><strong>{{ $attendance->student->full_name }}</strong></td>
                                    <td class="f-13">{{ $attendance->tutor->name ?? '-' }}</td>
                                    <td class="f-13">{{ $attendance->class_date->format('M d, Y') }}</td>
                                    <td class="f-13">{{ $attendance->class_time_range ?? '-' }}</td>
                                    <td class="f-13">{{ $attendance->subject->name ?? '-' }}</td>
                                    <td>
                                        @if($attendance->is_stand_in) <span class="badge-standin">Stand-in</span> @endif
                                        @if($attendance->is_late) <span class="badge-late">Late</span> @endif
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('elpisview.attendance.show', $attendance) }}" class="btn btn-sm btn-outline-primary rounded mr-1" title="View">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <form action="{{ route('elpisview.attendance.approve', $attendance) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success rounded" title="Approve">
                                                <i class="fa fa-check"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($attendances->hasPages())
                    <div class="mt-3">{{ $attendances->links() }}</div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('selectAll')?.addEventListener('change', function() {
        document.querySelectorAll('.bulk-check').forEach(cb => cb.checked = this.checked);
    });
</script>
@endpush
