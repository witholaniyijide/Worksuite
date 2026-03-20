@extends('layouts.app')

@push('styles')
    <style>
        .ev-stat-card {
            transition: transform 0.2s ease;
        }

        .ev-stat-card:hover {
            transform: translateY(-2px);
        }

        .h-scroll-table {
            max-height: 420px;
            overflow-y: auto;
        }

        .quick-action-btn {
            min-width: 160px;
        }
    </style>
@endpush

@section('filter-section')

    <x-filters.filter-box>
        <div class="d-flex py-2 px-lg-3 px-0">
            <h4 class="mb-0 f-18 f-w-500 text-darkest-grey d-flex align-items-center">
                <i class="fa fa-chalkboard-teacher mr-2 text-primary"></i> Tutor Dashboard &mdash; {{ $tutor->name }}
            </h4>
        </div>
    </x-filters.filter-box>

@endsection

@section('content')

    <!-- CONTENT WRAPPER START -->
    <div class="content-wrapper">

        <!-- QUICK ACTIONS START -->
        <div class="row mb-3">
            <div class="col-12 d-flex flex-wrap">
                <a href="{{ route('elpisview.attendance.create') }}" class="btn btn-primary mr-2 mb-2 quick-action-btn">
                    <i class="fa fa-plus-circle mr-1"></i> Submit Attendance
                </a>
                <a href="{{ route('elpisview.attendance.create', ['stand_in' => 1]) }}" class="btn btn-outline-primary mr-2 mb-2 quick-action-btn">
                    <i class="fa fa-user-friends mr-1"></i> Submit Stand-in
                </a>
                <a href="{{ route('elpisview.reports.create') }}" class="btn btn-outline-primary mb-2 quick-action-btn">
                    <i class="fa fa-file-alt mr-1"></i> Create Report
                </a>
            </div>
        </div>
        <!-- QUICK ACTIONS END -->

        <!-- STATS WIDGETS ROW START -->
        <div class="row mb-3">
            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <x-cards.widget :title="'Active Students'" :value="$activeStudents ?? 0" icon="user-graduate"
                    widgetId="activeStudents" />
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <x-cards.widget :title="'Pending Attendance'" :value="$pendingAttendance ?? 0" icon="clock"
                    widgetId="pendingAttendance" />
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <x-cards.widget :title="'This Month Classes'" :value="$thisMonthClasses ?? 0" icon="calendar-check"
                    widgetId="thisMonthClasses" />
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <x-cards.widget :title="'This Month Earnings'" :value="number_format($thisMonthEarnings ?? 0, 2)" icon="money-bill-wave"
                    widgetId="thisMonthEarnings" />
            </div>
        </div>
        <!-- STATS WIDGETS ROW END -->

        <div class="row">

            <!-- RECENT ATTENDANCE START -->
            <div class="col-xl-6 col-lg-12 mb-4">
                <x-cards.data :title="'Recent Attendance'" otherClasses="h-scroll-table">
                    <x-slot name="action">
                        <a href="{{ route('elpisview.attendance.index') }}" class="f-13 text-primary">
                            View All <i class="fa fa-arrow-right ml-1"></i>
                        </a>
                    </x-slot>

                    <div class="table-responsive">
                        <table class="table table-hover border-0 w-100">
                            <thead>
                                <tr>
                                    <th class="f-13 text-dark-grey">Date</th>
                                    <th class="f-13 text-dark-grey">Student</th>
                                    <th class="f-13 text-dark-grey">Subject</th>
                                    <th class="f-13 text-dark-grey">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentAttendance ?? [] as $attendance)
                                    <tr>
                                        <td class="f-14">{{ $attendance->class_date->format('d M Y') }}</td>
                                        <td class="f-14 text-darkest-grey">{{ $attendance->student->full_name ?? '--' }}</td>
                                        <td class="f-14">{{ $attendance->subject->name ?? '--' }}</td>
                                        <td>
                                            @if ($attendance->status === 'approved')
                                                <span class="badge badge-success">Approved</span>
                                            @elseif ($attendance->status === 'submitted')
                                                <span class="badge badge-warning text-white">Pending</span>
                                            @elseif ($attendance->status === 'rejected')
                                                <span class="badge badge-danger">Rejected</span>
                                            @else
                                                <span class="badge badge-secondary">{{ ucfirst($attendance->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-lightest f-14">
                                            No recent attendance records found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-cards.data>
            </div>
            <!-- RECENT ATTENDANCE END -->

            <!-- RECENT REPORTS START -->
            <div class="col-xl-6 col-lg-12 mb-4">
                <x-cards.data :title="'Recent Reports'" otherClasses="h-scroll-table">
                    <x-slot name="action">
                        <a href="{{ route('elpisview.reports.index') }}" class="f-13 text-primary">
                            View All <i class="fa fa-arrow-right ml-1"></i>
                        </a>
                    </x-slot>

                    <div class="table-responsive">
                        <table class="table table-hover border-0 w-100">
                            <thead>
                                <tr>
                                    <th class="f-13 text-dark-grey">Period</th>
                                    <th class="f-13 text-dark-grey">Student</th>
                                    <th class="f-13 text-dark-grey">Subject</th>
                                    <th class="f-13 text-dark-grey">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentReports ?? [] as $report)
                                    <tr>
                                        <td class="f-14">{{ $report->period_label }}</td>
                                        <td class="f-14 text-darkest-grey">{{ $report->student->full_name ?? '--' }}</td>
                                        <td class="f-14">{{ $report->subject->name ?? '--' }}</td>
                                        <td>
                                            @if ($report->status === 'delivered')
                                                <span class="badge badge-success">Delivered</span>
                                            @elseif ($report->status === 'approved')
                                                <span class="badge badge-info">Approved</span>
                                            @elseif ($report->status === 'submitted')
                                                <span class="badge badge-warning text-white">Pending</span>
                                            @elseif ($report->status === 'adjustment_requested')
                                                <span class="badge badge-danger">Adjust</span>
                                            @else
                                                <span class="badge badge-secondary">{{ ucfirst($report->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-lightest f-14">
                                            No recent reports found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-cards.data>
            </div>
            <!-- RECENT REPORTS END -->

        </div>

    </div>
    <!-- CONTENT WRAPPER END -->

@endsection
