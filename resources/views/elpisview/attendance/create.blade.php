@extends('layouts.app')

@push('styles')
<style>
    .ev-form-card { border-radius: 12px; overflow: hidden; }
    .ev-form-header { background: linear-gradient(135deg, #4B49AC, #7978E9); padding: 16px 24px; }
    .ev-form-header.standin { background: linear-gradient(135deg, #7978E9, #98BDFF); }
    .ev-mode-btn { display: inline-block; padding: 8px 16px; border-radius: 8px; font-weight: 500; font-size: 13px; text-decoration: none; transition: all 0.2s; }
    .ev-mode-btn.active { background: linear-gradient(135deg, #4B49AC, #7978E9); color: #fff; box-shadow: 0 2px 8px rgba(75, 73, 172, 0.3); }
    .ev-mode-btn.active.standin { background: linear-gradient(135deg, #7978E9, #98BDFF); }
    .ev-mode-btn:not(.active) { background: #f1f5f9; color: #64748b; }
    .ev-mode-btn:not(.active):hover { background: #e2e8f0; color: #334155; text-decoration: none; }
    .ev-info-banner { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px; padding: 14px 18px; }
    .ev-reschedule-box { background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 14px 18px; }
    .ev-late-warning { background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 14px 18px; }
    .duration-btn { display: inline-block; padding: 6px 14px; background: #f1f5f9; color: #64748b; border-radius: 6px; border: none; font-size: 13px; font-weight: 500; cursor: pointer; transition: all 0.15s; }
    .duration-btn:hover { background: #e2e8f0; color: #334155; }
</style>
@endpush

@section('filter-section')
<x-filters.filter-box>
    <div class="d-flex py-2 px-lg-3 px-0 w-100 justify-content-between align-items-center">
        <h4 class="mb-0 f-18 f-w-500 text-darkest-grey d-flex align-items-center">
            <i class="fa fa-clipboard-check mr-2 text-primary"></i>
            {{ $isStandIn ? 'Submit Stand-in Attendance' : 'Submit Attendance' }}
        </h4>
        <a href="{{ route('elpisview.attendance.index') }}" class="btn btn-light btn-sm rounded">
            <i class="fa fa-arrow-left mr-1"></i> Back to History
        </a>
    </div>
</x-filters.filter-box>
@endsection

@section('content')
<div class="content-wrapper">
    <!-- Duplicate Warning Modal -->
    <div class="modal fade" id="duplicateModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content rounded">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title"><i class="fa fa-exclamation-triangle mr-2"></i> Duplicate Attendance Warning</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>You have already submitted attendance for <strong id="duplicateStudentName"></strong> on <strong id="duplicateDate"></strong>.</p>
                    <div id="duplicateDetails" class="bg-light rounded p-3 mb-3">
                        <p class="f-12 text-dark-grey mb-2 font-weight-bold">Previous submission(s):</p>
                        <div id="duplicateList"></div>
                    </div>
                    <p class="f-13 text-warning"><i class="fa fa-info-circle mr-1"></i> Are you sure you want to submit another attendance record for this student on the same day?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-sm rounded" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning btn-sm rounded" onclick="submitAnyway()">Yes, Submit Anyway</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <!-- Mode Toggle -->
            <div class="mb-3">
                <a href="{{ route('elpisview.attendance.create') }}"
                   class="ev-mode-btn {{ !$isStandIn ? 'active' : '' }}">
                    <i class="fa fa-users mr-1"></i> My Students
                </a>
                <a href="{{ route('elpisview.attendance.create', ['standin' => 1]) }}"
                   class="ev-mode-btn {{ $isStandIn ? 'active standin' : '' }}">
                    <i class="fa fa-user-plus mr-1"></i> Stand-in
                </a>
            </div>

            @if($isStandIn)
                <div class="ev-info-banner mb-3">
                    <div class="d-flex align-items-start">
                        <i class="fa fa-info-circle text-info mr-2 mt-1"></i>
                        <div>
                            <strong class="text-primary">Stand-in Attendance</strong>
                            <p class="f-13 text-dark-grey mb-0 mt-1">
                                Use this form when covering for another tutor. You must provide a reason for the stand-in.
                                The student's assigned tutor will be notified.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Attendance Form -->
            <div class="card ev-form-card border-0 shadow-sm">
                <div class="ev-form-header {{ $isStandIn ? 'standin' : '' }}">
                    <h5 class="text-white mb-0 f-15">
                        <i class="fa fa-clipboard-check mr-2"></i>
                        {{ $isStandIn ? 'Stand-in Details' : 'Attendance Details' }}
                    </h5>
                </div>

                <div class="card-body p-4">
                    <form id="attendanceForm" action="{{ route('elpisview.attendance.store') }}" method="POST" onsubmit="return handleFormSubmit(event)">
                        @csrf
                        <input type="hidden" name="is_stand_in" value="{{ $isStandIn ? '1' : '0' }}">

                        <!-- Tutor Selection -->
                        <div class="form-group">
                            <label class="f-13 font-weight-bold text-dark-grey">Tutor <span class="text-danger">*</span></label>
                            <select id="tutor_id" name="tutor_id" class="form-control select-picker" data-live-search="true" required>
                                <option value="">Select Tutor</option>
                                @foreach($tutors as $tutor)
                                    <option value="{{ $tutor->id }}" {{ old('tutor_id') == $tutor->id ? 'selected' : '' }}>
                                        {{ $tutor->name }} ({{ $tutor->staff_id }})
                                    </option>
                                @endforeach
                            </select>
                            @error('tutor_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Student Selection -->
                        <div class="form-group">
                            <label class="f-13 font-weight-bold text-dark-grey">Student <span class="text-danger">*</span></label>
                            @if($isStandIn)
                                <select id="student_id" name="student_id" class="form-control select-picker" data-live-search="true" required>
                                    <option value="">Select a student to cover</option>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                            {{ $student->first_name }} {{ $student->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <select id="student_id" name="student_id" class="form-control select-picker" data-live-search="true" required>
                                    <option value="">Select your student</option>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}" {{ old('student_id', request('student_id')) == $student->id ? 'selected' : '' }}>
                                            {{ $student->first_name }} {{ $student->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                            @error('student_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Subject Selection -->
                        <div class="form-group">
                            <label class="f-13 font-weight-bold text-dark-grey">Subject <span class="text-danger">*</span></label>
                            <select id="subject_id" name="subject_id" class="form-control select-picker" data-live-search="true" required>
                                <option value="">Select Subject</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('subject_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Stand-in Reason -->
                        @if($isStandIn)
                        <div class="form-group">
                            <label class="f-13 font-weight-bold text-dark-grey">Reason for Stand-in <span class="text-danger">*</span></label>
                            <select id="stand_in_reason" name="stand_in_reason" class="form-control" required>
                                <option value="">Select a reason</option>
                                <option value="tutor_sick" {{ old('stand_in_reason') === 'tutor_sick' ? 'selected' : '' }}>Assigned tutor is sick</option>
                                <option value="tutor_leave" {{ old('stand_in_reason') === 'tutor_leave' ? 'selected' : '' }}>Assigned tutor on leave</option>
                                <option value="tutor_emergency" {{ old('stand_in_reason') === 'tutor_emergency' ? 'selected' : '' }}>Assigned tutor has emergency</option>
                                <option value="schedule_conflict" {{ old('stand_in_reason') === 'schedule_conflict' ? 'selected' : '' }}>Schedule conflict</option>
                                <option value="manager_request" {{ old('stand_in_reason') === 'manager_request' ? 'selected' : '' }}>Manager requested cover</option>
                                <option value="other" {{ old('stand_in_reason') === 'other' ? 'selected' : '' }}>Other reason</option>
                            </select>
                            @error('stand_in_reason')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        @endif

                        <!-- Date & Time -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="f-13 font-weight-bold text-dark-grey">Class Date <span class="text-danger">*</span></label>
                                    <input type="date" id="class_date" name="class_date"
                                           value="{{ old('class_date', date('Y-m-d')) }}"
                                           max="{{ date('Y-m-d') }}"
                                           class="form-control" required>
                                    @error('class_date')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="f-13 font-weight-bold text-dark-grey">Start Time <span class="text-danger">*</span></label>
                                    <input type="time" id="start_time" name="start_time"
                                           value="{{ old('start_time') }}"
                                           class="form-control" required>
                                    @error('start_time')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="f-13 font-weight-bold text-dark-grey">End Time <span class="text-danger">*</span></label>
                                    <input type="time" id="end_time" name="end_time"
                                           value="{{ old('end_time') }}"
                                           class="form-control" required>
                                    @error('end_time')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Rescheduled Class Toggle -->
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="is_rescheduled" name="is_rescheduled" value="1"
                                       {{ old('is_rescheduled') ? 'checked' : '' }}>
                                <label class="custom-control-label f-13 text-dark-grey" for="is_rescheduled">
                                    This class was rescheduled from its originally scheduled time
                                </label>
                            </div>
                            <small class="text-lightest ml-4">Check this if you held the class at a different time than what was scheduled</small>
                        </div>

                        <!-- Rescheduled Details -->
                        <div id="reschedule-fields" class="ev-reschedule-box mb-3" style="display: {{ old('is_rescheduled') ? 'block' : 'none' }};">
                            <div class="d-flex align-items-start mb-2">
                                <i class="fa fa-clock text-warning mr-2 mt-1"></i>
                                <div>
                                    <strong class="f-13 text-dark">Rescheduled Class Details</strong>
                                    <p class="f-12 text-dark-grey mb-0">Provide the original scheduled time and reason for rescheduling.</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="f-12 font-weight-bold text-dark-grey">Original Scheduled Time <span class="text-danger">*</span></label>
                                        <input type="time" name="original_scheduled_time"
                                               value="{{ old('original_scheduled_time') }}"
                                               class="form-control form-control-sm">
                                        @error('original_scheduled_time')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="f-12 font-weight-bold text-dark-grey">Reason for Rescheduling <span class="text-danger">*</span></label>
                                        <select name="reschedule_reason" class="form-control form-control-sm">
                                            <option value="">Select a reason</option>
                                            <option value="student_request" {{ old('reschedule_reason') === 'student_request' ? 'selected' : '' }}>Student/Parent requested</option>
                                            <option value="tutor_schedule_conflict" {{ old('reschedule_reason') === 'tutor_schedule_conflict' ? 'selected' : '' }}>Tutor schedule conflict</option>
                                            <option value="technical_issues" {{ old('reschedule_reason') === 'technical_issues' ? 'selected' : '' }}>Technical issues earlier</option>
                                            <option value="student_late" {{ old('reschedule_reason') === 'student_late' ? 'selected' : '' }}>Student was late/unavailable</option>
                                            <option value="tutor_emergency" {{ old('reschedule_reason') === 'tutor_emergency' ? 'selected' : '' }}>Tutor emergency</option>
                                            <option value="power_internet_outage" {{ old('reschedule_reason') === 'power_internet_outage' ? 'selected' : '' }}>Power/Internet outage</option>
                                            <option value="manager_approved" {{ old('reschedule_reason') === 'manager_approved' ? 'selected' : '' }}>Pre-approved by Manager</option>
                                            <option value="other" {{ old('reschedule_reason') === 'other' ? 'selected' : '' }}>Other reason</option>
                                        </select>
                                        @error('reschedule_reason')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-0">
                                <label class="f-12 font-weight-bold text-dark-grey">Additional Details (optional)</label>
                                <input type="text" name="reschedule_notes" value="{{ old('reschedule_notes') }}"
                                       maxlength="255" placeholder="Any additional context for the reschedule..."
                                       class="form-control form-control-sm">
                            </div>
                        </div>

                        <!-- Topic -->
                        <div class="form-group">
                            <label class="f-13 font-weight-bold text-dark-grey">Topic Covered <span class="text-danger">*</span></label>
                            <input type="text" id="topics_covered" name="topics_covered"
                                   value="{{ old('topics_covered') }}"
                                   maxlength="255" required
                                   placeholder="e.g., Variables and Data Types, Creating First Animation"
                                   class="form-control">
                            <small class="text-lightest">Specific topic or lesson covered in this class</small>
                            @error('topics_covered')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="form-group">
                            <label class="f-13 font-weight-bold text-dark-grey">Notes <span class="text-danger">*</span></label>
                            <textarea id="class_notes" name="class_notes" rows="4" maxlength="2000" required
                                      placeholder="Any additional notes about the class, student progress, homework assigned..."
                                      class="form-control">{{ old('class_notes') }}</textarea>
                            @error('class_notes')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Late Submission Warning -->
                        <div id="late-warning" class="ev-late-warning mb-3" style="display: none;">
                            <div class="d-flex align-items-start">
                                <i class="fa fa-exclamation-triangle text-warning mr-2 mt-1"></i>
                                <div>
                                    <strong class="f-13 text-dark">Late Submission</strong>
                                    <p class="f-12 text-dark-grey mb-0">This attendance will be marked as late because the class date has passed.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Info Box -->
                        <div class="bg-light rounded p-3 mb-3">
                            <div class="d-flex align-items-start">
                                <i class="fa fa-info-circle text-lightest mr-2 mt-1"></i>
                                <small class="text-dark-grey">Attendance will be reviewed by an Admin before approval. You'll be notified once it's processed.</small>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-end border-top pt-3">
                            <a href="{{ route('elpisview.attendance.index') }}" class="btn btn-light btn-sm rounded mr-2">Cancel</a>
                            <button type="submit" class="btn {{ $isStandIn ? 'btn-info' : 'btn-primary' }} btn-sm rounded">
                                Submit {{ $isStandIn ? 'Stand-in ' : '' }}Attendance
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let skipDuplicateCheck = false;

    // Toggle reschedule fields
    document.getElementById('is_rescheduled').addEventListener('change', function() {
        document.getElementById('reschedule-fields').style.display = this.checked ? 'block' : 'none';
    });

    // Check for late submission
    document.getElementById('class_date').addEventListener('change', function() {
        const selectedDate = new Date(this.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        const warningEl = document.getElementById('late-warning');
        warningEl.style.display = selectedDate < today ? 'block' : 'none';
    });

    // Handle form submission with duplicate check
    async function handleFormSubmit(event) {
        if (skipDuplicateCheck) {
            skipDuplicateCheck = false;
            return true;
        }

        event.preventDefault();

        const studentId = document.getElementById('student_id').value;
        const classDate = document.getElementById('class_date').value;

        if (!studentId || !classDate) {
            document.getElementById('attendanceForm').submit();
            return false;
        }

        try {
            const response = await fetch('{{ route("elpisview.attendance.check-duplicate") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ student_id: studentId, class_date: classDate })
            });

            const data = await response.json();

            if (data.has_duplicate) {
                showDuplicateModal(data);
                return false;
            } else {
                document.getElementById('attendanceForm').submit();
            }
        } catch (error) {
            console.error('Error checking for duplicates:', error);
            document.getElementById('attendanceForm').submit();
        }

        return false;
    }

    function showDuplicateModal(data) {
        document.getElementById('duplicateStudentName').textContent = data.student_name;
        document.getElementById('duplicateDate').textContent = data.date;

        const listEl = document.getElementById('duplicateList');
        listEl.innerHTML = '';

        data.duplicates.forEach(dup => {
            const item = document.createElement('div');
            item.className = 'd-flex justify-content-between align-items-center p-2 bg-white rounded mb-1';
            item.innerHTML = `
                <div>
                    <span class="f-13 font-weight-bold">${dup.time}</span>
                    <span class="f-12 text-lightest ml-1">by ${dup.tutor}</span>
                </div>
                <span class="badge ${dup.status === 'Pending' || dup.status === 'Submitted' ? 'badge-warning' : dup.status === 'Approved' ? 'badge-success' : 'badge-danger'}">${dup.status}</span>
            `;
            listEl.appendChild(item);
        });

        $('#duplicateModal').modal('show');
    }

    function submitAnyway() {
        $('#duplicateModal').modal('hide');
        skipDuplicateCheck = true;
        document.getElementById('attendanceForm').submit();
    }

    // Trigger on page load
    document.addEventListener('DOMContentLoaded', function() {
        const classDateInput = document.getElementById('class_date');
        if (classDateInput.value) {
            classDateInput.dispatchEvent(new Event('change'));
        }
    });
</script>
@endpush
