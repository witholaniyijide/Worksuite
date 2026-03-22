@extends('layouts.app')

@push('styles')
<style>
    .ev-status-banner { border-radius: 10px; padding: 14px 18px; }
    .ev-status-banner.approved { background: #ecfdf5; border: 1px solid #a7f3d0; }
    .ev-status-banner.rejected { background: #fff1f2; border: 1px solid #fecdd3; }
    .ev-status-banner.pending { background: #fffbeb; border: 1px solid #fde68a; }
    .ev-detail-card { border-radius: 12px; overflow: hidden; }
    .ev-detail-header { background: linear-gradient(135deg, #4B49AC, #7978E9); padding: 14px 20px; }
    .ev-standin-reason { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px; padding: 12px 16px; }
    .ev-reschedule-info { background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 12px 16px; }
    .ev-timeline-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 10px; margin-top: 6px; }
</style>
@endpush

@section('filter-section')
<x-filters.filter-box>
    <div class="d-flex py-2 px-lg-3 px-0 w-100 justify-content-between align-items-center">
        <h4 class="mb-0 f-18 f-w-500 text-darkest-grey d-flex align-items-center">
            <i class="fa fa-clipboard-check mr-2 text-primary"></i> Attendance Details
        </h4>
        <a href="{{ route('elpisview.attendance.index') }}" class="btn btn-light btn-sm rounded">
            <i class="fa fa-arrow-left mr-1"></i> Back to History
        </a>
    </div>
</x-filters.filter-box>
@endsection

@section('content')
<div class="content-wrapper">
    <!-- Status Banner -->
    <div class="ev-status-banner mb-4 {{ $classAttendance->status === 'approved' ? 'approved' : ($classAttendance->status === 'disputed' ? 'rejected' : 'pending') }}">
        <div class="d-flex align-items-center">
            @if($classAttendance->status === 'approved')
                <span class="badge badge-success p-2 mr-3 rounded-circle"><i class="fa fa-check"></i></span>
                <div>
                    <h6 class="text-success mb-0 font-weight-bold">Approved</h6>
                    <small class="text-success">This attendance has been approved</small>
                </div>
            @elseif($classAttendance->status === 'disputed')
                <span class="badge badge-danger p-2 mr-3 rounded-circle"><i class="fa fa-times"></i></span>
                <div>
                    <h6 class="text-danger mb-0 font-weight-bold">Rejected</h6>
                    <small class="text-danger">This attendance was rejected</small>
                </div>
            @else
                <span class="badge badge-warning p-2 mr-3 rounded-circle"><i class="fa fa-clock"></i></span>
                <div>
                    <h6 class="text-warning mb-0 font-weight-bold">Pending Approval</h6>
                    <small class="text-dark-grey">Awaiting admin review</small>
                </div>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Main Details -->
        <div class="col-lg-8">
            <div class="card ev-detail-card border-0 shadow-sm mb-4">
                <div class="ev-detail-header">
                    <h6 class="text-white mb-0 f-14"><i class="fa fa-clipboard-check mr-2"></i> Class Information</h6>
                </div>
                <div class="card-body p-4">
                    <!-- Student Info -->
                    <div class="bg-light rounded p-3 mb-4 d-flex align-items-center">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3" style="width:48px;height:48px;font-weight:700;">
                            {{ strtoupper(substr($classAttendance->student->first_name, 0, 1)) }}{{ strtoupper(substr($classAttendance->student->last_name, 0, 1)) }}
                        </div>
                        <div>
                            <h6 class="mb-0 font-weight-bold text-darkest-grey">
                                {{ $classAttendance->student->first_name }} {{ $classAttendance->student->last_name }}
                            </h6>
                            <div class="d-flex align-items-center mt-1">
                                @if($classAttendance->is_stand_in)
                                    <span class="badge badge-info mr-2" style="font-size:11px;">Stand-in</span>
                                @endif
                                <small class="text-lightest">Tutor: {{ $classAttendance->tutor->name ?? '-' }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- Stand-in Reason -->
                    @if($classAttendance->is_stand_in && $classAttendance->stand_in_reason)
                        <div class="ev-standin-reason mb-4">
                            <label class="f-12 font-weight-bold text-info mb-1">Stand-in Reason</label>
                            <p class="f-13 text-primary mb-0">
                                @switch($classAttendance->stand_in_reason)
                                    @case('tutor_sick') Assigned tutor is sick @break
                                    @case('tutor_leave') Assigned tutor on leave @break
                                    @case('tutor_emergency') Assigned tutor has emergency @break
                                    @case('schedule_conflict') Schedule conflict @break
                                    @case('manager_request') Manager requested cover @break
                                    @default {{ ucfirst(str_replace('_', ' ', $classAttendance->stand_in_reason)) }}
                                @endswitch
                            </p>
                        </div>
                    @endif

                    <!-- Date, Time, Duration -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="f-11 text-lightest mb-1">Class Date</label>
                            <p class="f-15 font-weight-bold text-darkest-grey mb-0">{{ $classAttendance->class_date->format('F d, Y') }}</p>
                            <small class="text-lightest">{{ $classAttendance->class_date->format('l') }}</small>
                        </div>
                        <div class="col-md-4">
                            <label class="f-11 text-lightest mb-1">Class Time</label>
                            <p class="f-15 font-weight-bold text-darkest-grey mb-0">{{ $classAttendance->class_time_range ?? '-' }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="f-11 text-lightest mb-1">Duration</label>
                            <p class="f-15 font-weight-bold text-darkest-grey mb-0">{{ $classAttendance->duration_hours }} hours</p>
                        </div>
                    </div>

                    <!-- Subject & Earnings -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="f-11 text-lightest mb-1">Subject</label>
                            <p class="f-14 text-darkest-grey mb-0">{{ $classAttendance->subject->name ?? '-' }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="f-11 text-lightest mb-1">Rate Applied</label>
                            <p class="f-14 text-darkest-grey mb-0">{{ $classAttendance->currency }} {{ number_format($classAttendance->rate_applied, 2) }}/hr</p>
                        </div>
                        <div class="col-md-4">
                            <label class="f-11 text-lightest mb-1">Amount Earned</label>
                            <p class="f-15 font-weight-bold text-success mb-0">{{ $classAttendance->currency }} {{ number_format($classAttendance->amount_earned, 2) }}</p>
                        </div>
                    </div>

                    <!-- Rescheduled Info -->
                    @if($classAttendance->is_rescheduled)
                        <div class="ev-reschedule-info mb-4">
                            <div class="d-flex align-items-start mb-2">
                                <i class="fa fa-clock text-warning mr-2 mt-1"></i>
                                <strong class="f-13 text-dark">Rescheduled Class</strong>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-dark-grey">Original Time: <strong>{{ $classAttendance->original_scheduled_time ? \Carbon\Carbon::parse($classAttendance->original_scheduled_time)->format('g:i A') : '-' }}</strong></small>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-dark-grey">Reason:
                                        <strong>{{ ucfirst(str_replace('_', ' ', $classAttendance->reschedule_reason ?? '-')) }}</strong>
                                    </small>
                                </div>
                            </div>
                            @if($classAttendance->reschedule_notes)
                                <small class="text-dark-grey d-block mt-1">Notes: {{ $classAttendance->reschedule_notes }}</small>
                            @endif
                        </div>
                    @endif

                    <!-- Topic -->
                    @if($classAttendance->topics_covered)
                        <div class="mb-4">
                            <label class="f-11 text-lightest mb-1">Topic / Lesson Details</label>
                            <p class="f-13 text-darkest-grey mb-0">{{ $classAttendance->topics_covered }}</p>
                        </div>
                    @endif

                    <!-- Notes -->
                    @if($classAttendance->class_notes)
                        <div class="mb-4">
                            <label class="f-11 text-lightest mb-1">Notes</label>
                            <div class="bg-light rounded p-3">
                                <p class="f-13 text-dark-grey mb-0" style="white-space:pre-wrap;">{{ $classAttendance->class_notes }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Late Badge -->
                    @if($classAttendance->is_late)
                        <div class="bg-danger-light rounded p-3" style="background:#fff1f2;border:1px solid #fecdd3;">
                            <div class="d-flex align-items-center text-danger">
                                <i class="fa fa-exclamation-triangle mr-2"></i>
                                <span class="f-13 font-weight-bold">This attendance was submitted late</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status Card -->
            <div class="card border-0 shadow-sm rounded mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 f-14 font-weight-bold text-darkest-grey">Status Details</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="f-11 text-lightest mb-1">Current Status</label>
                        <div>
                            @if($classAttendance->status === 'approved')
                                <span class="badge badge-success px-3 py-1">Approved</span>
                            @elseif($classAttendance->status === 'disputed')
                                <span class="badge badge-danger px-3 py-1">Rejected</span>
                            @else
                                <span class="badge badge-warning px-3 py-1">{{ ucfirst($classAttendance->status) }}</span>
                            @endif
                        </div>
                    </div>

                    @if($classAttendance->approved_by)
                        <div class="mb-3">
                            <label class="f-11 text-lightest mb-1">
                                {{ $classAttendance->status === 'approved' ? 'Approved By' : 'Reviewed By' }}
                            </label>
                            <p class="f-13 text-darkest-grey mb-0">{{ $classAttendance->approvedByUser->name ?? 'Admin' }}</p>
                            @if($classAttendance->approved_at)
                                <small class="text-lightest">{{ $classAttendance->approved_at->format('M d, Y g:i A') }}</small>
                            @endif
                        </div>
                    @endif

                    @if($classAttendance->rejection_reason)
                        <div class="bg-danger-light rounded p-3" style="background:#fff1f2;">
                            <label class="f-11 text-danger mb-1 font-weight-bold">Rejection Reason</label>
                            <p class="f-12 text-danger mb-0">{{ $classAttendance->rejection_reason }}</p>
                        </div>
                    @endif

                    <!-- Approve / Reject Actions -->
                    @if($classAttendance->status === 'submitted')
                        <div class="border-top pt-3 mt-3">
                            <form action="{{ route('elpisview.attendance.approve', $classAttendance) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm rounded mr-1">
                                    <i class="fa fa-check mr-1"></i> Approve
                                </button>
                            </form>
                            <button type="button" class="btn btn-danger btn-sm rounded" data-toggle="modal" data-target="#rejectModal">
                                <i class="fa fa-times mr-1"></i> Reject
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Timeline Card -->
            <div class="card border-0 shadow-sm rounded mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 f-14 font-weight-bold text-darkest-grey">Timeline</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex mb-3">
                        <span class="ev-timeline-dot" style="background:#7978E9;"></span>
                        <div>
                            <p class="f-13 font-weight-bold text-darkest-grey mb-0">Submitted</p>
                            <small class="text-lightest">{{ $classAttendance->created_at->format('M d, Y g:i A') }}</small>
                        </div>
                    </div>
                    @if($classAttendance->updated_at != $classAttendance->created_at)
                        <div class="d-flex mb-3">
                            <span class="ev-timeline-dot" style="background:#9ca3af;"></span>
                            <div>
                                <p class="f-13 font-weight-bold text-darkest-grey mb-0">Last Updated</p>
                                <small class="text-lightest">{{ $classAttendance->updated_at->format('M d, Y g:i A') }}</small>
                            </div>
                        </div>
                    @endif
                    @if($classAttendance->approved_at)
                        <div class="d-flex">
                            <span class="ev-timeline-dot" style="background:{{ $classAttendance->status === 'approved' ? '#10b981' : '#ef4444' }};"></span>
                            <div>
                                <p class="f-13 font-weight-bold text-darkest-grey mb-0">
                                    {{ $classAttendance->status === 'approved' ? 'Approved' : 'Rejected' }}
                                </p>
                                <small class="text-lightest">{{ $classAttendance->approved_at->format('M d, Y g:i A') }}</small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Action Footer -->
    <div class="d-flex justify-content-between mt-3">
        <a href="{{ route('elpisview.attendance.index') }}" class="btn btn-light btn-sm rounded">
            <i class="fa fa-arrow-left mr-1"></i> Back to History
        </a>
        <a href="{{ route('elpisview.attendance.create') }}" class="btn btn-primary btn-sm rounded">
            <i class="fa fa-plus mr-1"></i> Submit Another
        </a>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content rounded">
            <form action="{{ route('elpisview.attendance.reject', $classAttendance) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title f-14"><i class="fa fa-times mr-2"></i> Reject Attendance</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="f-13 font-weight-bold text-dark-grey">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" rows="3" required maxlength="500"
                                  class="form-control" placeholder="Please provide a reason for rejecting this attendance..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-sm rounded" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm rounded">Reject Attendance</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
