@extends('layouts.app')

@push('datatable-styles')
    @include('sections.datatable_css')
@endpush

@push('styles')
    <style>
        .badge-draft { background-color: #6c757d; color: #fff; }
        .badge-submitted { background-color: #17a2b8; color: #fff; }
        .badge-pending_review { background-color: #ffc107; color: #212529; }
        .badge-adjustment_requested { background-color: #dc3545; color: #fff; }
        .badge-approved { background-color: #28a745; color: #fff; }
        .badge-delivered { background-color: #007bff; color: #fff; }
    </style>
@endpush

@section('filter-section')

    <x-filters.filter-box>

        <!-- TUTOR FILTER -->
        <div class="select-box d-flex py-2 pr-2 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">Tutor</p>
            <div class="select-status">
                <select class="form-control select-picker" name="tutor_id" id="tutor_id" data-live-search="true" data-size="8">
                    <option value="all">@lang('app.all')</option>
                    @foreach ($tutors as $tutor)
                        <option value="{{ $tutor->id }}">{{ $tutor->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- STUDENT FILTER -->
        <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">Student</p>
            <div class="select-status">
                <select class="form-control select-picker" name="student_id" id="student_id" data-live-search="true" data-size="8">
                    <option value="all">@lang('app.all')</option>
                    @foreach ($students as $student)
                        <option value="{{ $student->id }}">{{ $student->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- SUBJECT FILTER -->
        <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">Subject</p>
            <div class="select-status">
                <select class="form-control select-picker" name="subject_id" id="subject_id" data-live-search="true" data-size="8">
                    <option value="all">@lang('app.all')</option>
                    @foreach ($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- STATUS FILTER -->
        <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.status')</p>
            <div class="select-status">
                <select class="form-control select-picker" name="status" id="status" data-size="8">
                    <option value="all">@lang('app.all')</option>
                    <option value="draft">Draft</option>
                    <option value="submitted">Submitted</option>
                    <option value="pending_review">Pending Review</option>
                    <option value="adjustment_requested">Adjustment Requested</option>
                    <option value="approved">Approved</option>
                    <option value="delivered">Delivered</option>
                </select>
            </div>
        </div>

        <!-- MONTH FILTER -->
        <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.month')</p>
            <div class="select-status">
                <select class="form-control select-picker" name="month" id="month" data-size="8">
                    <option value="all">@lang('app.all')</option>
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" @if(isset($month) && $month == $m) selected @endif>
                            {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>
        </div>

        <!-- YEAR FILTER -->
        <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.year')</p>
            <div class="select-status">
                <select class="form-control select-picker" name="year" id="year" data-size="8">
                    @for ($y = now()->year; $y >= now()->year - 4; $y--)
                        <option value="{{ $y }}" @if(isset($year) && $year == $y) selected @endif>{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </div>

        <!-- RESET START -->
        <div class="select-box d-flex py-1 px-lg-2 px-md-2 px-0">
            <x-forms.button-secondary class="btn-xs d-none" id="reset-filters" icon="times-circle">
                @lang('app.clearFilters')
            </x-forms.button-secondary>
        </div>
        <!-- RESET END -->

    </x-filters.filter-box>

@endsection

@section('content')
    <!-- CONTENT WRAPPER START -->
    <div class="content-wrapper">

        <!-- ACTION BAR START -->
        <div class="d-grid d-lg-flex d-md-flex action-bar">
            <div id="table-actions" class="flex-grow-1 align-items-center">
                <a href="{{ route('elpisview.reports.create') }}" class="btn btn-primary mr-3 float-left">
                    <i class="fa fa-plus mr-1"></i> New Report
                </a>
            </div>
        </div>
        <!-- ACTION BAR END -->

        <!-- TABLE START -->
        <div class="d-flex flex-column w-tables rounded mt-3 bg-white">
            <div class="table-responsive">
                <table class="table table-hover border-0 w-100" id="reports-table">
                    <thead class="thead-light">
                        <tr>
                            <th>Student</th>
                            <th>Subject</th>
                            <th>Period</th>
                            <th>Tutor</th>
                            <th>Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reports as $report)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <h5 class="mb-0 f-13">{{ $report->student->name ?? '--' }}</h5>
                                            <p class="mb-0 f-12 text-lightest">{{ $report->student->region->name ?? '' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $report->subject->name ?? '--' }}</td>
                                <td>
                                    {{ \Carbon\Carbon::create()->month($report->report_month)->format('F') }} {{ $report->report_year }}
                                </td>
                                <td>{{ $report->tutor->name ?? '--' }}</td>
                                <td>
                                    @php
                                        $badgeClass = [
                                            'draft' => 'badge-draft',
                                            'submitted' => 'badge-submitted',
                                            'pending_review' => 'badge-pending_review',
                                            'adjustment_requested' => 'badge-adjustment_requested',
                                            'approved' => 'badge-approved',
                                            'delivered' => 'badge-delivered',
                                        ][$report->status] ?? 'badge-secondary';

                                        $statusLabel = [
                                            'draft' => 'Draft',
                                            'submitted' => 'Submitted',
                                            'pending_review' => 'Pending Review',
                                            'adjustment_requested' => 'Adjustment Requested',
                                            'approved' => 'Approved',
                                            'delivered' => 'Delivered',
                                        ][$report->status] ?? ucfirst($report->status);
                                    @endphp
                                    <span class="badge {{ $badgeClass }} f-12 px-2 py-1">{{ $statusLabel }}</span>
                                </td>
                                <td class="text-right">
                                    <div class="task_view">
                                        <a href="{{ route('elpisview.reports.show', $report->id) }}"
                                           class="taskView btn btn-sm btn-outline-primary mr-1" data-toggle="tooltip"
                                           title="View">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        @if (in_array($report->status, ['draft', 'adjustment_requested']))
                                            <a href="{{ route('elpisview.reports.edit', $report->id) }}"
                                               class="btn btn-sm btn-outline-info mr-1" data-toggle="tooltip"
                                               title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        @endif
                                        @if ($report->status === 'draft')
                                            <form action="{{ route('elpisview.reports.destroy', $report->id) }}"
                                                  method="POST" class="d-inline delete-report-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        data-toggle="tooltip" title="Delete">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-lightest">
                                    <i class="fa fa-file-alt f-20 mb-2 d-block"></i>
                                    No progress reports found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($reports instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="d-flex justify-content-center mt-3 mb-3">
                    {{ $reports->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
        <!-- TABLE END -->

    </div>
    <!-- CONTENT WRAPPER END -->
@endsection

@push('scripts')
    <script>
        // Filter change handlers
        $('#tutor_id, #student_id, #subject_id, #status, #month, #year').on('change', function () {
            var hasFilter = false;
            if ($('#tutor_id').val() != 'all') hasFilter = true;
            if ($('#student_id').val() != 'all') hasFilter = true;
            if ($('#subject_id').val() != 'all') hasFilter = true;
            if ($('#status').val() != 'all') hasFilter = true;
            if ($('#month').val() != 'all') hasFilter = true;

            if (hasFilter) {
                $('#reset-filters').removeClass('d-none');
            } else {
                $('#reset-filters').addClass('d-none');
            }
            applyFilters();
        });

        $('#reset-filters').click(function () {
            $('#filter-form')[0].reset();
            $('.select-picker').selectpicker('refresh');
            $('#reset-filters').addClass('d-none');
            applyFilters();
        });

        function applyFilters() {
            var url = new URL("{{ route('elpisview.reports.index') }}");
            var params = {
                tutor_id: $('#tutor_id').val(),
                student_id: $('#student_id').val(),
                subject_id: $('#subject_id').val(),
                status: $('#status').val(),
                month: $('#month').val(),
                year: $('#year').val()
            };

            Object.keys(params).forEach(function(key) {
                if (params[key] && params[key] !== 'all') {
                    url.searchParams.set(key, params[key]);
                }
            });

            window.location.href = url.toString();
        }

        // Delete confirmation
        $('.delete-report-form').on('submit', function (e) {
            e.preventDefault();
            var form = this;
            Swal.fire({
                title: "@lang('messages.sweetAlertTitle')",
                text: "Are you sure you want to delete this report?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: "@lang('messages.confirmDelete')",
                cancelButtonText: "@lang('app.cancel')",
                customClass: {
                    confirmButton: 'btn btn-primary mr-3',
                    cancelButton: 'btn btn-secondary'
                },
                showClass: {
                    popup: 'swal2-noanimation',
                    backdrop: 'swal2-noanimation'
                },
                buttonsStyling: false
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    </script>
@endpush
