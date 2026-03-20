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
                <i class="fa fa-user-tie mr-2 text-primary"></i> Manager Dashboard &mdash; {{ $region->name }}
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
                <a href="{{ route('elpisview.attendance.pending') }}" class="btn btn-primary mr-2 mb-2 quick-action-btn">
                    <i class="fa fa-clipboard-check mr-1"></i> View Pending Attendance
                </a>
                <a href="{{ route('elpisview.reports.index') }}" class="btn btn-outline-primary mb-2 quick-action-btn">
                    <i class="fa fa-file-alt mr-1"></i> View Reports
                </a>
            </div>
        </div>
        <!-- QUICK ACTIONS END -->

        <!-- STATS WIDGETS ROW START -->
        <div class="row mb-3">
            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <x-cards.widget :title="'Regional Students'" :value="$activeStudents ?? 0" icon="user-graduate"
                    widgetId="regionalStudents" />
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <x-cards.widget :title="'Regional Tutors'" :value="$activeTutors ?? 0" icon="chalkboard-teacher"
                    widgetId="regionalTutors" />
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <x-cards.widget :title="'Pending Attendance'" :value="$pendingAttendance ?? 0" icon="clock"
                    widgetId="pendingAttendance" />
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <x-cards.widget :title="'Pending Reports'" :value="$pendingReports ?? 0" icon="file-alt"
                    widgetId="pendingReports" />
            </div>
        </div>
        <!-- STATS WIDGETS ROW END -->

        <div class="row">

            <!-- RECENT ATTENDANCE START -->
            <div class="col-12 mb-4">
                <x-cards.data :title="'Recent Attendance - ' . $region->name" otherClasses="h-scroll-table">
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
                                    <th class="f-13 text-dark-grey">Tutor</th>
                                    <th class="f-13 text-dark-grey">Subject</th>
                                    <th class="f-13 text-dark-grey">Duration</th>
                                    <th class="f-13 text-dark-grey">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentAttendance ?? [] as $attendance)
                                    <tr>
                                        <td class="f-14">{{ $attendance->class_date->format('d M Y') }}</td>
                                        <td class="f-14 text-darkest-grey">{{ $attendance->student->full_name ?? '--' }}</td>
                                        <td class="f-14">{{ $attendance->tutor->name ?? '--' }}</td>
                                        <td class="f-14">{{ $attendance->subject->name ?? '--' }}</td>
                                        <td class="f-14">{{ $attendance->duration_hours ?? 0 }} hrs</td>
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
                                        <td colspan="6" class="text-center text-lightest f-14">
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

        </div>

    </div>
    <!-- CONTENT WRAPPER END -->

@endsection
