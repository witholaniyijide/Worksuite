@extends('layouts.app')

@section('content')

    <!-- CONTENT WRAPPER START -->
    <div class="content-wrapper">

        <div class="row">
            <div class="col-sm-12">
                <form id="save-tutor-form" method="POST" action="{{ route('elpisview.tutors.store') }}">
                    @csrf

                    <!-- TUTOR DETAILS START -->
                    <div class="bg-white rounded add-client">
                        <h4 class="p-20 mb-0 f-21 font-weight-normal border-bottom-grey">
                            <i class="fa fa-user-plus mr-2"></i>Add Tutor
                        </h4>

                        <div class="p-20 row">
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <label class="f-14 text-dark-grey mb-12" for="name">Name <sup class="text-danger">*</sup></label>
                                    <input type="text" class="form-control height-35 f-14 @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name') }}" placeholder="Full name" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <label class="f-14 text-dark-grey mb-12" for="email">Email <sup class="text-danger">*</sup></label>
                                    <input type="email" class="form-control height-35 f-14 @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email') }}" placeholder="Email address" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <label class="f-14 text-dark-grey mb-12" for="staff_id">Staff ID <sup class="text-danger">*</sup></label>
                                    <input type="text" class="form-control height-35 f-14 @error('staff_id') is-invalid @enderror"
                                        id="staff_id" name="staff_id" value="{{ old('staff_id') }}" placeholder="e.g. EV-001" required>
                                    @error('staff_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <label class="f-14 text-dark-grey mb-12" for="region_id">Region <sup class="text-danger">*</sup></label>
                                    <select class="form-control select-picker height-35 f-14 @error('region_id') is-invalid @enderror"
                                        id="region_id" name="region_id" data-live-search="true" data-size="8" required>
                                        <option value="">-- Select Region --</option>
                                        @isset($regions)
                                            @foreach ($regions as $region)
                                                <option value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}>
                                                    {{ $region->name }}
                                                </option>
                                            @endforeach
                                        @endisset
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
                                        id="phone" name="phone" value="{{ old('phone') }}" placeholder="Phone number">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <label class="f-14 text-dark-grey mb-12" for="hire_date">Hire Date</label>
                                    <input type="text" class="form-control height-35 f-14 datepicker @error('hire_date') is-invalid @enderror"
                                        id="hire_date" name="hire_date" value="{{ old('hire_date') }}" placeholder="Select date" autocomplete="off">
                                    @error('hire_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-12">
                                <div class="form-group">
                                    <label class="f-14 text-dark-grey mb-12" for="bio">Bio</label>
                                    <textarea class="form-control f-14 @error('bio') is-invalid @enderror"
                                        id="bio" name="bio" rows="3" placeholder="Short biography">{{ old('bio') }}</textarea>
                                    @error('bio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-12">
                                <div class="form-group">
                                    <label class="f-14 text-dark-grey mb-12" for="qualifications">Qualifications</label>
                                    <textarea class="form-control f-14 @error('qualifications') is-invalid @enderror"
                                        id="qualifications" name="qualifications" rows="3" placeholder="Qualifications and certifications">{{ old('qualifications') }}</textarea>
                                    @error('qualifications')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- TUTOR DETAILS END -->

                    <!-- PAY RATE SECTION START -->
                    <div class="bg-white rounded add-client mt-3">
                        <h4 class="p-20 mb-0 f-21 font-weight-normal border-bottom-grey">
                            <i class="fa fa-money-bill-wave mr-2"></i>Pay Rate
                        </h4>

                        <div class="p-20 row">
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label class="f-14 text-dark-grey mb-12" for="subject">Subject <sup class="text-danger">*</sup></label>
                                    <select class="form-control select-picker height-35 f-14 @error('subject') is-invalid @enderror"
                                        id="subject" name="subject" data-live-search="true" data-size="8" required>
                                        <option value="">-- Select Subject --</option>
                                        @isset($subjects)
                                            @foreach ($subjects as $subject)
                                                <option value="{{ $subject->id }}" {{ old('subject') == $subject->id ? 'selected' : '' }}>
                                                    {{ $subject->name }}
                                                </option>
                                            @endforeach
                                        @endisset
                                    </select>
                                    @error('subject')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label class="f-14 text-dark-grey mb-12" for="rate_per_hour">Rate Per Hour <sup class="text-danger">*</sup></label>
                                    <input type="number" step="0.01" min="0" class="form-control height-35 f-14 @error('rate_per_hour') is-invalid @enderror"
                                        id="rate_per_hour" name="rate_per_hour" value="{{ old('rate_per_hour') }}" placeholder="0.00" required>
                                    @error('rate_per_hour')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label class="f-14 text-dark-grey mb-12" for="currency">Currency</label>
                                    <select class="form-control select-picker height-35 f-14 @error('currency') is-invalid @enderror"
                                        id="currency" name="currency" data-size="8">
                                        @isset($currencies)
                                            @foreach ($currencies as $currency)
                                                <option value="{{ $currency->id }}" {{ old('currency', $defaultCurrency ?? '') == $currency->id ? 'selected' : '' }}>
                                                    {{ $currency->currency_symbol }} - {{ $currency->currency_code }}
                                                </option>
                                            @endforeach
                                        @endisset
                                    </select>
                                    @error('currency')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label class="f-14 text-dark-grey mb-12" for="effective_from">Effective From</label>
                                    <input type="text" class="form-control height-35 f-14 datepicker @error('effective_from') is-invalid @enderror"
                                        id="effective_from" name="effective_from" value="{{ old('effective_from') }}" placeholder="Select date" autocomplete="off">
                                    @error('effective_from')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- PAY RATE SECTION END -->

                    <!-- FORM ACTIONS START -->
                    <div class="w-100 border-top-grey mt-3">
                        <div class="d-flex justify-content-start p-20">
                            <x-forms.button-primary id="save-tutor-btn" class="mr-3" icon="check">
                                Save Tutor
                            </x-forms.button-primary>
                            <x-forms.button-cancel :link="route('elpisview.tutors.index')" class="border-0">
                                Cancel
                            </x-forms.button-cancel>
                        </div>
                    </div>
                    <!-- FORM ACTIONS END -->

                </form>
            </div>
        </div>

    </div>
    <!-- CONTENT WRAPPER END -->

@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            // Initialize datepickers
            $('.datepicker').datepicker({
                format: '{{ company()->date_format ?? "yyyy-mm-dd" }}',
                autoclose: true,
                todayHighlight: true
            });

            // Form submission
            $('#save-tutor-form').on('submit', function (e) {
                var btn = $('#save-tutor-btn');
                btn.attr('disabled', true);
                btn.find('span').text('Saving...');
            });
        });
    </script>
@endpush
