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
                <i class="fa fa-building mr-2 text-primary"></i> Director Dashboard
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
                <a href="{{ route('elpisview.reports.index') }}" class="btn btn-primary mr-2 mb-2 quick-action-btn">
                    <i class="fa fa-file-alt mr-1"></i> View All Reports
                </a>
                <a href="{{ route('elpisview.dashboard') }}" class="btn btn-outline-primary mb-2 quick-action-btn">
                    <i class="fa fa-chart-bar mr-1"></i> View Analytics
                </a>
            </div>
        </div>
        <!-- QUICK ACTIONS END -->

        <!-- STATS WIDGETS ROW START -->
        <div class="row mb-3">
            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <x-cards.widget :title="'Total Students'" :value="$totalStudents ?? 0" icon="user-graduate"
                    widgetId="totalStudents" />
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <x-cards.widget :title="'Total Tutors'" :value="$totalTutors ?? 0" icon="chalkboard-teacher"
                    widgetId="totalTutors" />
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <x-cards.widget :title="'Monthly Revenue'" :value="number_format($thisMonthRevenue ?? 0, 2)" icon="money-bill-wave"
                    widgetId="monthlyRevenue" />
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <x-cards.widget :title="'Attendance Rate'" :value="($attendanceRate ?? 0) . '%'" icon="chart-line"
                    widgetId="attendanceRate" />
            </div>
        </div>
        <!-- STATS WIDGETS ROW END -->

        <div class="row">

            <!-- REGION BREAKDOWN START -->
            <div class="col-xl-6 col-lg-12 mb-4">
                <x-cards.data :title="'Region Breakdown'" otherClasses="h-scroll-table">
                    <div class="table-responsive">
                        <table class="table table-hover border-0 w-100">
                            <thead>
                                <tr>
                                    <th class="f-13 text-dark-grey">Region</th>
                                    <th class="f-13 text-dark-grey">Active Students</th>
                                    <th class="f-13 text-dark-grey">Active Tutors</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($regions ?? [] as $region)
                                    <tr>
                                        <td class="f-14 text-darkest-grey">{{ $region->name }}</td>
                                        <td class="f-14">{{ $region->students_count ?? 0 }}</td>
                                        <td class="f-14">{{ $region->tutors_count ?? 0 }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-lightest f-14">
                                            No region data available.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-cards.data>
            </div>
            <!-- REGION BREAKDOWN END -->

            <!-- PENDING APPROVALS START -->
            <div class="col-xl-6 col-lg-12 mb-4">
                <x-cards.data :title="'Pending Approvals'" otherClasses="h-scroll-table">
                    <x-slot name="action">
                        <span class="badge badge-warning text-white f-12">{{ $pendingApprovals ?? 0 }} pending</span>
                    </x-slot>

                    <div class="table-responsive">
                        <table class="table table-hover border-0 w-100">
                            <thead>
                                <tr>
                                    <th class="f-13 text-dark-grey">Period</th>
                                    <th class="f-13 text-dark-grey">Student</th>
                                    <th class="f-13 text-dark-grey">Tutor</th>
                                    <th class="f-13 text-dark-grey">Subject</th>
                                    <th class="f-13 text-dark-grey">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pendingDirectorReports ?? [] as $report)
                                    <tr>
                                        <td class="f-14">{{ $report->period_label }}</td>
                                        <td class="f-14 text-darkest-grey">{{ $report->student->full_name ?? '--' }}</td>
                                        <td class="f-14">{{ $report->tutor->name ?? '--' }}</td>
                                        <td class="f-14">{{ $report->subject->name ?? '--' }}</td>
                                        <td>
                                            <span class="badge badge-info">Approved</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-lightest f-14">
                                            No pending approvals.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-cards.data>
            </div>
            <!-- PENDING APPROVALS END -->

        </div>

    </div>
    <!-- CONTENT WRAPPER END -->

@endsection
