<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'exists:students,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'class_date' => 'required|date|before_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'topics_covered' => 'required|string|max:255',
            'class_notes' => 'required|string|max:2000',
            'is_stand_in' => 'nullable',
            'stand_in_reason' => 'nullable|required_if:is_stand_in,1,true|string|max:100',
            'is_rescheduled' => 'nullable',
            'original_scheduled_time' => 'nullable|required_if:is_rescheduled,1,true|date_format:H:i',
            'reschedule_reason' => 'nullable|required_if:is_rescheduled,1,true|string|max:100',
            'reschedule_notes' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'student_id.required' => 'Please select a student.',
            'student_id.exists' => 'The selected student is invalid.',
            'subject_id.required' => 'Please select a subject.',
            'class_date.required' => 'Class date is required.',
            'class_date.before_or_equal' => 'Class date cannot be in the future.',
            'start_time.required' => 'Start time is required.',
            'end_time.required' => 'End time is required.',
            'end_time.after' => 'End time must be after start time.',
            'topics_covered.required' => 'Please enter the topic covered in this class.',
            'class_notes.required' => 'Please add notes about this class.',
            'stand_in_reason.required_if' => 'Reason for stand-in is required.',
            'original_scheduled_time.required_if' => 'Original scheduled time is required for rescheduled classes.',
            'reschedule_reason.required_if' => 'Reason for rescheduling is required.',
        ];
    }
}
