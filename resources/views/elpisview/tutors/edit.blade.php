@extends('layouts.app')

@section('filter-section')
<x-filters.filter-box>
    <div class="d-flex py-2 px-lg-3 px-0 w-100 justify-content-between align-items-center">
        <h4 class="mb-0 f-18 f-w-500 text-darkest-grey d-flex align-items-center">
            <i class="fa fa-user-edit mr-2 text-primary"></i> Edit Tutor
        </h4>
        <a href="{{ route('elpisview.tutors.show', $tutor) }}" class="btn btn-light btn-sm rounded">
            <i class="fa fa-arrow-left mr-1"></i> Back to Profile
        </a>
    </div>
</x-filters.filter-box>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-sm-12">
            <form id="update-tutor-form" method="POST" action="{{ route('elpisview.tutors.update', $tutor) }}">
                @csrf
                @method('PUT')

                <!-- Tutor Details -->
                <div class="bg-white rounded add-client">
                    <h4 class="p-20 mb-0 f-21 font-weight-normal border-bottom-grey">
                        <i class="fa fa-chalkboard-teacher mr-2"></i>Tutor Details
                    </h4>

                    <div class="p-20 row">
                        <div class="col-lg-4 col-md-6">
                            <div class="form-group">
                                <label class="f-14 text-dark-grey mb-12" for="name">Name <sup class="text-danger">*</sup></label>
                                <input type="text" class="form-control height-35 f-14 @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $tutor->user->name ?? $tutor->name) }}" placeholder="Full name" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <div class="form-group">
                                <label class="f-14 text-dark-grey mb-12" for="email">Email <sup class="text-danger">*</sup></label>
                                <input type="email" class="form-control height-35 f-14 @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email', $tutor->user->email ?? $tutor->email) }}" placeholder="Email address" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <div class="form-group">
                                <label class="f-14 text-dark-grey mb-12" for="staff_id">Staff ID</label>
                                <input type="text" class="form-control height-35 f-14 @error('staff_id') is-invalid @enderror"
                                    id="staff_id" name="staff_id" value="{{ old('staff_id', $tutor->staff_id) }}" placeholder="e.g. EV-001">
                                @error('staff_id')
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
                                        <option value="{{ $region->id }}" {{ old('region_id', $tutor->region_id) == $region->id ? 'selected' : '' }}>
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
                                <label class="f-14 text-dark-grey mb-12" for="phone">Phone</label>
                                <input type="text" class="form-control height-35 f-14 @error('phone') is-invalid @enderror"
                                    id="phone" name="phone" value="{{ old('phone', $tutor->phone) }}" placeholder="Phone number">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <div class="form-group">
                                <label class="f-14 text-dark-grey mb-12" for="hire_date">Hire Date</label>
                                <input type="date" class="form-control height-35 f-14 @error('hire_date') is-invalid @enderror"
                                    id="hire_date" name="hire_date" value="{{ old('hire_date', $tutor->hire_date ? \Carbon\Carbon::parse($tutor->hire_date)->format('Y-m-d') : '') }}">
                                @error('hire_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <div class="form-group">
                                <label class="f-14 text-dark-grey mb-12" for="status">Status</label>
                                <select class="form-control select-picker height-35 f-14 @error('status') is-invalid @enderror"
                                    id="status" name="status">
                                    <option value="active" {{ old('status', $tutor->status) === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $tutor->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="on_leave" {{ old('status', $tutor->status) === 'on_leave' ? 'selected' : '' }}>On Leave</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-12">
                            <div class="form-group">
                                <label class="f-14 text-dark-grey mb-12" for="bio">Bio</label>
                                <textarea class="form-control f-14 @error('bio') is-invalid @enderror"
                                    id="bio" name="bio" rows="3" placeholder="Short biography">{{ old('bio', $tutor->bio) }}</textarea>
                                @error('bio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-12">
                            <div class="form-group">
                                <label class="f-14 text-dark-grey mb-12" for="qualifications">Qualifications</label>
                                <textarea class="form-control f-14 @error('qualifications') is-invalid @enderror"
                                    id="qualifications" name="qualifications" rows="3" placeholder="Qualifications and certifications">{{ old('qualifications', $tutor->qualifications) }}</textarea>
                                @error('qualifications')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="w-100 border-top-grey mt-3">
                    <div class="d-flex justify-content-start p-20">
                        <x-forms.button-primary id="update-tutor-btn" class="mr-3" icon="check">
                            Update Tutor
                        </x-forms.button-primary>
                        <x-forms.button-cancel :link="route('elpisview.tutors.show', $tutor)" class="border-0">
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
        $('#update-tutor-form').on('submit', function (e) {
            var btn = $('#update-tutor-btn');
            btn.attr('disabled', true);
            btn.find('span').text('Updating...');
        });
    });
</script>
@endpush
