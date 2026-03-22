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
                <i class="fa fa-cogs mr-2 text-primary"></i> Admin Dashboard
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
                <a href="{{ route('elpisview.students.index') }}" class="btn btn-primary mr-2 mb-2 quick-action-btn">
                    <i class="fa fa-user-plus mr-1"></i> Add Student
                </a>
                <a href="{{ route('elpisview.tutors.create') }}" class="btn btn-outline-primary mr-2 mb-2 quick-action-btn">
                    <i class="fa fa-user-plus mr-1"></i> Add Tutor
                </a>
                <a href="{{ route('elpisview.dashboard') }}" class="btn btn-outline-primary mb-2 quick-action-btn">
                    <i class="fa fa-bullhorn mr-1"></i> View Notices
                </a>
            </div>
        </div>
        <!-- QUICK ACTIONS END -->

        <!-- STATS WIDGETS ROW 1 - STUDENTS START -->
        <div class="row mb-3">
            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <x-cards.widget :title="'Active Students'" :value="$studentCounts['active'] ?? 0" icon="user-graduate"
                    widgetId="activeStudents" />
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <x-cards.widget :title="'Inactive Students'" :value="$studentCounts['inactive'] ?? 0" icon="user-slash"
                    widgetId="inactiveStudents" />
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <x-cards.widget :title="'Graduated'" :value="$studentCounts['graduated'] ?? 0" icon="graduation-cap"
                    widgetId="graduatedStudents" />
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <x-cards.widget :title="'Withdrawn'" :value="$studentCounts['withdrawn'] ?? 0" icon="user-minus"
                    widgetId="withdrawnStudents" />
            </div>
        </div>
        <!-- STATS WIDGETS ROW 1 END -->

        <!-- STATS WIDGETS ROW 2 - TUTORS & PENDING START -->
        <div class="row mb-3">
            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <x-cards.widget :title="'Active Tutors'" :value="$tutorCounts['active'] ?? 0" icon="chalkboard-teacher"
                    widgetId="activeTutors" />
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <x-cards.widget :title="'Tutors On Leave'" :value="$tutorCounts['on_leave'] ?? 0" icon="user-clock"
                    widgetId="onLeaveTutors" />
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

        <!-- TODAY'S CLASSES WIDGET -->
        <div class="row mb-3">
            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <x-cards.widget :title="'Today\'s Classes'" :value="$todaysClasses ?? 0" icon="calendar-day"
                    widgetId="todaysClasses" />
            </div>
        </div>
        <!-- STATS WIDGETS ROW 2 END -->

        <div class="row">

            <!-- RECENT ACTIVITY START -->
            <div class="col-xl-6 col-lg-12 mb-4">
                <x-cards.data :title="'Recent Activity'" otherClasses="h-scroll-table">
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
                                    <th class="f-13 text-dark-grey">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentActivity ?? [] as $attendance)
                                    <tr>
                                        <td class="f-14">{{ $attendance->class_date->format('d M Y') }}</td>
                                        <td class="f-14 text-darkest-grey">{{ $attendance->student->full_name ?? '--' }}</td>
                                        <td class="f-14">{{ $attendance->tutor->name ?? '--' }}</td>
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
                                        <td colspan="5" class="text-center text-lightest f-14">
                                            No recent activity found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-cards.data>
            </div>
            <!-- RECENT ACTIVITY END -->

            <!-- LATEST STUDENTS & TUTORS START -->
            <div class="col-xl-6 col-lg-12 mb-4">

                <!-- LATEST STUDENTS -->
                <x-cards.data :title="'Latest Students'" otherClasses="mb-4">
                    <x-slot name="action">
                        <a href="{{ route('elpisview.students.index') }}" class="f-13 text-primary">
                            View All <i class="fa fa-arrow-right ml-1"></i>
                        </a>
                    </x-slot>

                    <div class="table-responsive">
                        <table class="table table-hover border-0 w-100">
                            <thead>
                                <tr>
                                    <th class="f-13 text-dark-grey">ID</th>
                                    <th class="f-13 text-dark-grey">Name</th>
                                    <th class="f-13 text-dark-grey">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($latestStudents ?? [] as $student)
                                    <tr>
                                        <td class="f-14">{{ $student->student_id }}</td>
                                        <td class="f-14 text-darkest-grey">{{ $student->full_name }}</td>
                                        <td>
                                            @if ($student->status === 'active')
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-secondary">{{ ucfirst($student->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-lightest f-14">
                                            No students found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-cards.data>

                <!-- LATEST TUTORS -->
                <x-cards.data :title="'Latest Tutors'">
                    <x-slot name="action">
                        <a href="{{ route('elpisview.tutors.index') }}" class="f-13 text-primary">
                            View All <i class="fa fa-arrow-right ml-1"></i>
                        </a>
                    </x-slot>

                    <div class="table-responsive">
                        <table class="table table-hover border-0 w-100">
                            <thead>
                                <tr>
                                    <th class="f-13 text-dark-grey">Name</th>
                                    <th class="f-13 text-dark-grey">Status</th>
                                    <th class="f-13 text-dark-grey">Joined</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($latestTutors ?? [] as $tutor)
                                    <tr>
                                        <td class="f-14 text-darkest-grey">{{ $tutor->name }}</td>
                                        <td>
                                            @if ($tutor->status === 'active')
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-secondary">{{ ucfirst($tutor->status) }}</span>
                                            @endif
                                        </td>
                                        <td class="f-14">{{ $tutor->created_at->format('d M Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-lightest f-14">
                                            No tutors found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-cards.data>

            </div>
            <!-- LATEST STUDENTS & TUTORS END -->

        </div>

    </div>
    <!-- CONTENT WRAPPER END -->

@endsection
