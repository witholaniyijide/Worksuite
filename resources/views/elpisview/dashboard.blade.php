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
    </style>
@endpush

@section('filter-section')

    <x-filters.filter-box>
        <div class="d-flex py-2 px-lg-3 px-0">
            <h4 class="mb-0 f-18 f-w-500 text-darkest-grey d-flex align-items-center">
                <i class="fa fa-graduation-cap mr-2 text-primary"></i> Elpis View Educational Services &mdash; Dashboard
            </h4>
        </div>
    </x-filters.filter-box>

@endsection

@section('content')

    <!-- CONTENT WRAPPER START -->
    <div class="content-wrapper">

        <!-- STATS WIDGETS ROW 1 START -->
        <div class="row mb-3">
            <div class="col-xl-4 col-lg-6 col-md-6 mb-3">
                <x-cards.widget :title="'Total Students'" :value="$totalStudents ?? 0" icon="user-graduate"
                    widgetId="totalStudents" />
            </div>
            <div class="col-xl-4 col-lg-6 col-md-6 mb-3">
                <x-cards.widget :title="'Total Tutors'" :value="$totalTutors ?? 0" icon="chalkboard-teacher"
                    widgetId="totalTutors" />
            </div>
            <div class="col-xl-4 col-lg-6 col-md-6 mb-3">
                <x-cards.widget :title="'Monthly Classes'" :value="$monthlyClasses ?? 0" icon="calendar-check"
                    widgetId="monthlyClasses" />
            </div>
        </div>
        <!-- STATS WIDGETS ROW 1 END -->

        <!-- STATS WIDGETS ROW 2 START -->
        <div class="row mb-3">
            <div class="col-xl-4 col-lg-6 col-md-6 mb-3">
                <x-cards.widget :title="'Monthly Hours'" :value="$monthlyHours ?? 0" icon="clock"
                    widgetId="monthlyHours" />
            </div>
            <div class="col-xl-4 col-lg-6 col-md-6 mb-3">
                <x-cards.widget :title="'Pending Reports'" :value="$pendingReports ?? 0" icon="file-alt"
                    widgetId="pendingReports" />
            </div>
            <div class="col-xl-4 col-lg-6 col-md-6 mb-3">
                <x-cards.widget :title="'Pending Payrolls'" :value="$pendingPayrolls ?? 0" icon="money-check-alt"
                    widgetId="pendingPayrolls" />
            </div>
        </div>
        <!-- STATS WIDGETS ROW 2 END -->

        <div class="row">

            <!-- REGION BREAKDOWN START -->
            <div class="col-xl-6 col-lg-12 mb-4">
                <x-cards.data :title="'Region Breakdown'" otherClasses="h-scroll-table">
                    <x-slot name="action">
                        <a href="javascript:;" class="btn btn-sm btn-outline-primary" id="refresh-regions">
                            <i class="fa fa-sync-alt"></i>
                        </a>
                    </x-slot>

                    <div class="table-responsive">
                        <table class="table table-hover border-0 w-100" id="region-breakdown-table">
                            <thead>
                                <tr>
                                    <th class="f-13 text-dark-grey">Region</th>
                                    <th class="f-13 text-dark-grey">Students</th>
                                    <th class="f-13 text-dark-grey">Tutors</th>
                                    <th class="f-13 text-dark-grey">Active Classes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($regions ?? [] as $region)
                                    <tr>
                                        <td class="f-14 text-darkest-grey">{{ $region->name }}</td>
                                        <td class="f-14">{{ $region->students_count ?? 0 }}</td>
                                        <td class="f-14">{{ $region->tutors_count ?? 0 }}</td>
                                        <td class="f-14">{{ $region->active_classes_count ?? 0 }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-lightest f-14">
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

            <!-- RECENT ATTENDANCE START -->
            <div class="col-xl-6 col-lg-12 mb-4">
                <x-cards.data :title="'Recent Attendance'" otherClasses="h-scroll-table">
                    <x-slot name="action">
                        <a href="{{ route('elpisview.attendance.index') }}" class="f-13 text-primary">
                            View All <i class="fa fa-arrow-right ml-1"></i>
                        </a>
                    </x-slot>

                    <div class="table-responsive">
                        <table class="table table-hover border-0 w-100" id="recent-attendance-table">
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
                                @forelse ($recentAttendance ?? [] as $attendance)
                                    <tr>
                                        <td class="f-14">{{ \Carbon\Carbon::parse($attendance->date)->format('d M Y') }}</td>
                                        <td class="f-14 text-darkest-grey">{{ $attendance->student->full_name ?? '--' }}</td>
                                        <td class="f-14">{{ $attendance->tutor->full_name ?? '--' }}</td>
                                        <td class="f-14">{{ $attendance->subject ?? '--' }}</td>
                                        <td>
                                            @if ($attendance->status === 'present')
                                                <span class="badge badge-success">Present</span>
                                            @elseif ($attendance->status === 'absent')
                                                <span class="badge badge-danger">Absent</span>
                                            @elseif ($attendance->status === 'late')
                                                <span class="badge badge-warning text-white">Late</span>
                                            @else
                                                <span class="badge badge-secondary">{{ ucfirst($attendance->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-lightest f-14">
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

@push('scripts')
    <script>
        $(document).ready(function () {
            // Refresh region breakdown
            $('#refresh-regions').on('click', function () {
                var $btn = $(this);
                $btn.find('i').addClass('fa-spin');

                $.easyAjax({
                    url: "{{ route('elpisview.dashboard.regions') }}",
                    type: 'GET',
                    success: function (response) {
                        if (response.status === 'success') {
                            var tbody = $('#region-breakdown-table tbody');
                            tbody.html('');
                            $.each(response.data, function (i, region) {
                                tbody.append(
                                    '<tr>' +
                                        '<td class="f-14 text-darkest-grey">' + region.name + '</td>' +
                                        '<td class="f-14">' + region.students_count + '</td>' +
                                        '<td class="f-14">' + region.tutors_count + '</td>' +
                                        '<td class="f-14">' + region.active_classes_count + '</td>' +
                                    '</tr>'
                                );
                            });
                        }
                        $btn.find('i').removeClass('fa-spin');
                    }
                });
            });
        });
    </script>
@endpush
