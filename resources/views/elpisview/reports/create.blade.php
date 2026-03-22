@extends('layouts.app')

@push('styles')
    <style>
        .week-section {
            border: 1px solid #e8eef3;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background: #fafbfc;
        }
        .week-section .week-header {
            font-size: 15px;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e8eef3;
        }
        .week-section .week-header .badge {
            font-size: 12px;
            vertical-align: middle;
        }
    </style>
@endpush

@section('content')
    <!-- CONTENT WRAPPER START -->
    <div class="content-wrapper">

        <div class="d-flex mb-3">
            <a href="{{ route('elpisview.reports.index') }}" class="btn btn-secondary f-14">
                <i class="fa fa-arrow-left mr-1"></i> Back to Reports
            </a>
        </div>

        <div class="card bg-white border-0 b-shadow-4">
            <div class="card-header bg-white border-bottom-grey p-20">
                <h3 class="heading-h1 mb-0">Create Progress Report</h3>
            </div>
            <div class="card-body p-20">
                <form id="create-report-form" method="POST" action="{{ route('elpisview.reports.store') }}">
                    @csrf

                    {{-- REPORT INFO SECTION --}}
                    <div class="row mb-4">
                        <div class="col-12">
                            <h4 class="f-16 f-w-500 text-dark mb-3">Report Information</h4>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="f-14 text-dark-grey mb-12" for="tutor_id">Tutor <sup class="text-danger">*</sup></label>
                                <select class="form-control select-picker" name="tutor_id" id="tutor_id"
                                        data-live-search="true" data-size="8" required>
                                    <option value="">-- Select Tutor --</option>
                                    @foreach ($tutors as $tutor)
                                        <option value="{{ $tutor->id }}" {{ old('tutor_id') == $tutor->id ? 'selected' : '' }}>
                                            {{ $tutor->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tutor_id')
                                    <span class="text-danger f-12">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="f-14 text-dark-grey mb-12" for="student_id">Student <sup class="text-danger">*</sup></label>
                                <select class="form-control select-picker" name="student_id" id="student_id"
                                        data-live-search="true" data-size="8" required>
                                    <option value="">-- Select Student --</option>
                                    @foreach ($students as $student)
                                        <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                            {{ $student->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('student_id')
                                    <span class="text-danger f-12">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="f-14 text-dark-grey mb-12" for="subject_id">Subject <sup class="text-danger">*</sup></label>
                                <select class="form-control select-picker" name="subject_id" id="subject_id"
                                        data-live-search="true" data-size="8" required>
                                    <option value="">-- Select Subject --</option>
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                            {{ $subject->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('subject_id')
                                    <span class="text-danger f-12">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="f-14 text-dark-grey mb-12" for="report_month">Month <sup class="text-danger">*</sup></label>
                                <select class="form-control select-picker" name="report_month" id="report_month" data-size="8" required>
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ (old('report_month', now()->month) == $m) ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                        </option>
                                    @endfor
                                </select>
                                @error('report_month')
                                    <span class="text-danger f-12">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="f-14 text-dark-grey mb-12" for="report_year">Year <sup class="text-danger">*</sup></label>
                                <select class="form-control select-picker" name="report_year" id="report_year" data-size="8" required>
                                    @for ($y = now()->year; $y >= now()->year - 2; $y--)
                                        <option value="{{ $y }}" {{ (old('report_year', now()->year) == $y) ? 'selected' : '' }}>
                                            {{ $y }}
                                        </option>
                                    @endfor
                                </select>
                                @error('report_year')
                                    <span class="text-danger f-12">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="mb-4">

                    {{-- OVERALL ASSESSMENT SECTION --}}
                    <div class="row mb-4">
                        <div class="col-12">
                            <h4 class="f-16 f-w-500 text-dark mb-3">Overall Assessment</h4>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="f-14 text-dark-grey mb-12" for="overall_performance">Overall Performance</label>
                                <textarea class="form-control" name="overall_performance" id="overall_performance"
                                          rows="4" placeholder="Describe the student's overall performance this month...">{{ old('overall_performance') }}</textarea>
                                @error('overall_performance')
                                    <span class="text-danger f-12">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="f-14 text-dark-grey mb-12" for="strengths">Strengths</label>
                                <textarea class="form-control" name="strengths" id="strengths"
                                          rows="3" placeholder="Key strengths observed...">{{ old('strengths') }}</textarea>
                                @error('strengths')
                                    <span class="text-danger f-12">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="f-14 text-dark-grey mb-12" for="areas_for_improvement">Areas for Improvement</label>
                                <textarea class="form-control" name="areas_for_improvement" id="areas_for_improvement"
                                          rows="3" placeholder="Areas where the student can improve...">{{ old('areas_for_improvement') }}</textarea>
                                @error('areas_for_improvement')
                                    <span class="text-danger f-12">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="f-14 text-dark-grey mb-12" for="tutor_comments">Tutor Comments</label>
                                <textarea class="form-control" name="tutor_comments" id="tutor_comments"
                                          rows="3" placeholder="Additional comments for the reviewing manager...">{{ old('tutor_comments') }}</textarea>
                                @error('tutor_comments')
                                    <span class="text-danger f-12">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="mb-4">

                    {{-- WEEKLY EVALUATIONS SECTION --}}
                    <div class="row mb-4">
                        <div class="col-12">
                            <h4 class="f-16 f-w-500 text-dark mb-3">Weekly Evaluations</h4>
                        </div>

                        <div class="col-12">
                            @for ($week = 1; $week <= 5; $week++)
                                <div class="week-section">
                                    <div class="week-header">
                                        <i class="fa fa-calendar-week mr-1"></i> Week {{ $week }}
                                        <span class="badge badge-light">{{ $week <= 4 ? 'Required' : 'Optional (5th week)' }}</span>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="f-14 text-dark-grey mb-12">Topics Covered</label>
                                                <textarea class="form-control" name="weeks[{{ $week }}][topics_covered]"
                                                          rows="2" placeholder="Topics covered in week {{ $week }}...">{{ old("weeks.{$week}.topics_covered") }}</textarea>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="f-14 text-dark-grey mb-12">Evaluation</label>
                                                <textarea class="form-control" name="weeks[{{ $week }}][evaluation]"
                                                          rows="2" placeholder="Weekly evaluation notes...">{{ old("weeks.{$week}.evaluation") }}</textarea>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="f-14 text-dark-grey mb-12">Performance Rating</label>
                                                <select class="form-control select-picker" name="weeks[{{ $week }}][performance_rating]" data-size="5">
                                                    <option value="">-- Select Rating --</option>
                                                    <option value="excellent" {{ old("weeks.{$week}.performance_rating") == 'excellent' ? 'selected' : '' }}>Excellent</option>
                                                    <option value="good" {{ old("weeks.{$week}.performance_rating") == 'good' ? 'selected' : '' }}>Good</option>
                                                    <option value="satisfactory" {{ old("weeks.{$week}.performance_rating") == 'satisfactory' ? 'selected' : '' }}>Satisfactory</option>
                                                    <option value="needs_improvement" {{ old("weeks.{$week}.performance_rating") == 'needs_improvement' ? 'selected' : '' }}>Needs Improvement</option>
                                                    <option value="poor" {{ old("weeks.{$week}.performance_rating") == 'poor' ? 'selected' : '' }}>Poor</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="f-14 text-dark-grey mb-12">Attendance This Week</label>
                                                <input type="number" class="form-control" name="weeks[{{ $week }}][attendance_this_week]"
                                                       min="0" max="7" placeholder="e.g., 5"
                                                       value="{{ old("weeks.{$week}.attendance_this_week") }}">
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="f-14 text-dark-grey mb-12">Comments</label>
                                                <input type="text" class="form-control" name="weeks[{{ $week }}][comments]"
                                                       placeholder="Brief comment..."
                                                       value="{{ old("weeks.{$week}.comments") }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>

                    {{-- FORM ACTIONS --}}
                    <hr>
                    <div class="d-flex justify-content-end py-3">
                        <a href="{{ route('elpisview.reports.index') }}" class="btn btn-secondary mr-3">
                            @lang('app.cancel')
                        </a>
                        <button type="submit" name="action" value="draft" class="btn btn-secondary mr-3">
                            <i class="fa fa-save mr-1"></i> Save as Draft
                        </button>
                        <button type="submit" name="action" value="submit" class="btn btn-primary">
                            <i class="fa fa-paper-plane mr-1"></i> Submit for Review
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
    <!-- CONTENT WRAPPER END -->
@endsection

@push('scripts')
    <script>
        // Form validation
        $('#create-report-form').on('submit', function (e) {
            var action = $('button[type="submit"][name="action"]:focus').val() || $('input[name="action"]').val();

            if (action === 'submit') {
                // Validate required fields for submission
                var requiredFields = ['#tutor_id', '#student_id', '#subject_id', '#overall_performance'];
                var isValid = true;

                requiredFields.forEach(function (field) {
                    if (!$(field).val()) {
                        $(field).addClass('is-invalid');
                        isValid = false;
                    } else {
                        $(field).removeClass('is-invalid');
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Validation Error',
                        text: 'Please fill in all required fields before submitting for review.',
                        icon: 'warning',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                }
            }
        });
    </script>
@endpush
