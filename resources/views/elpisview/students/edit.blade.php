@extends('layouts.app')

@section('filter-section')
<x-filters.filter-box>
    <div class="d-flex py-2 px-lg-3 px-0 w-100 justify-content-between align-items-center">
        <h4 class="mb-0 f-18 f-w-500 text-darkest-grey d-flex align-items-center">
            <i class="fa fa-user-edit mr-2 text-primary"></i> Edit Student
        </h4>
        <a href="{{ route('elpisview.students.show', $student) }}" class="btn btn-light btn-sm rounded">
            <i class="fa fa-arrow-left mr-1"></i> Back to Profile
        </a>
    </div>
</x-filters.filter-box>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-sm-12">
            <form id="update-student-form" method="POST" action="{{ route('elpisview.students.update', $student) }}">
                @csrf
                @method('PUT')

                <!-- Student Details -->
                <div class="bg-white rounded add-client">
                    <h4 class="p-20 mb-0 f-21 font-weight-normal border-bottom-grey">
                        <i class="fa fa-user-graduate mr-2"></i>Student Details
                    </h4>

                    <div class="p-20 row">
                        <div class="col-lg-4 col-md-6">
                            <div class="form-group">
                                <label class="f-14 text-dark-grey mb-12" for="first_name">First Name <sup class="text-danger">*</sup></label>
                                <input type="text" class="form-control height-35 f-14 @error('first_name') is-invalid @enderror"
                                    id="first_name" name="first_name" value="{{ old('first_name', $student->first_name) }}" placeholder="First name" required>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <div class="form-group">
                                <label class="f-14 text-dark-grey mb-12" for="last_name">Last Name <sup class="text-danger">*</sup></label>
                                <input type="text" class="form-control height-35 f-14 @error('last_name') is-invalid @enderror"
                                    id="last_name" name="last_name" value="{{ old('last_name', $student->last_name) }}" placeholder="Last name" required>
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <div class="form-group">
                                <label class="f-14 text-dark-grey mb-12" for="email">Email</label>
                                <input type="email" class="form-control height-35 f-14 @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email', $student->email) }}" placeholder="Email address">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <div class="form-group">
                                <label class="f-14 text-dark-grey mb-12" for="date_of_birth">Date of Birth</label>
                                <input type="date" class="form-control height-35 f-14 @error('date_of_birth') is-invalid @enderror"
                                    id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $student->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->format('Y-m-d') : '') }}">
                                @error('date_of_birth')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <div class="form-group">
                                <label class="f-14 text-dark-grey mb-12" for="region_id">Region</label>
                                <select class="form-control select-picker height-35 f-14 @error('region_id') is-invalid @enderror"
                                    id="region_id" name="region_id" data-live-search="true" data-size="8">
                                    <option value="">-- Select Region --</option>
                                    @foreach($regions as $region)
                                        <option value="{{ $region->id }}" {{ old('region_id', $student->region_id) == $region->id ? 'selected' : '' }}>
                                            {{ $region->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('region_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <div class="form-group">
                                <label class="f-14 text-dark-grey mb-12" for="guardian_id">Guardian</label>
                                <select class="form-control select-picker height-35 f-14 @error('guardian_id') is-invalid @enderror"
                                    id="guardian_id" name="guardian_id" data-live-search="true" data-size="8">
                                    <option value="">-- Select Guardian --</option>
                                    @foreach($guardians as $guardian)
                                        <option value="{{ $guardian->id }}" {{ old('guardian_id', $student->guardian_id) == $guardian->id ? 'selected' : '' }}>
                                            {{ $guardian->user->name ?? 'Guardian #' . $guardian->id }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('guardian_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <div class="form-group">
                                <label class="f-14 text-dark-grey mb-12" for="status">Status</label>
                                <select class="form-control select-picker height-35 f-14 @error('status') is-invalid @enderror"
                                    id="status" name="status">
                                    <option value="active" {{ old('status', $student->status) === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $student->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="graduated" {{ old('status', $student->status) === 'graduated' ? 'selected' : '' }}>Graduated</option>
                                    <option value="withdrawn" {{ old('status', $student->status) === 'withdrawn' ? 'selected' : '' }}>Withdrawn</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-8 col-md-12">
                            <div class="form-group">
                                <label class="f-14 text-dark-grey mb-12" for="notes">Notes</label>
                                <textarea class="form-control f-14 @error('notes') is-invalid @enderror"
                                    id="notes" name="notes" rows="4" placeholder="Additional notes about the student">{{ old('notes', $student->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="w-100 border-top-grey mt-3">
                    <div class="d-flex justify-content-start p-20">
                        <x-forms.button-primary id="update-student-btn" class="mr-3" icon="check">
                            Update Student
                        </x-forms.button-primary>
                        <x-forms.button-cancel :link="route('elpisview.students.show', $student)" class="border-0">
                            Cancel
                        </x-forms.button-cancel>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#update-student-form').on('submit', function (e) {
            var btn = $('#update-student-btn');
            btn.attr('disabled', true);
            btn.find('span').text('Updating...');
        });
    });
</script>
@endpush
