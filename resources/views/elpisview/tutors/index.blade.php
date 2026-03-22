@extends('layouts.app')

@push('datatable-styles')
    @include('sections.datatable_css')
@endpush

@section('filter-section')

    <x-filters.filter-box>

        <!-- REGION FILTER START -->
        <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">Region</p>
            <div class="select-status">
                <select class="form-control select-picker" name="region" id="filter-region" data-live-search="true" data-size="8">
                    <option value="all">All</option>
                    @isset($regions)
                        @foreach ($regions as $region)
                            <option value="{{ $region->id }}">{{ $region->name }}</option>
                        @endforeach
                    @endisset
                </select>
            </div>
        </div>
        <!-- REGION FILTER END -->

        <!-- STATUS FILTER START -->
        <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">Status</p>
            <div class="select-status">
                <select class="form-control select-picker" name="status" id="filter-status" data-size="8">
                    <option value="all">All</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>
        <!-- STATUS FILTER END -->

        <!-- SEARCH START -->
        <div class="task-search d-flex py-1 px-lg-3 px-0 border-right-grey align-items-center">
            <form class="w-100 mr-1 mr-lg-0 mr-md-1 ml-md-1 ml-0 ml-lg-0">
                <div class="input-group bg-grey rounded">
                    <div class="input-group-prepend">
                        <span class="input-group-text border-0 bg-additional-grey">
                            <i class="fa fa-search f-13 text-dark-grey"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control f-14 p-1 border-additional-grey" id="search-text-field"
                        placeholder="Start typing...">
                </div>
            </form>
        </div>
        <!-- SEARCH END -->

        <!-- RESET START -->
        <div class="select-box d-flex py-1 px-lg-2 px-md-2 px-0">
            <x-forms.button-secondary class="btn-xs d-none" id="reset-filters" icon="times-circle">
                Clear Filters
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
                <x-forms.link-primary :link="route('elpisview.tutors.create')" class="mr-3 float-left mb-2 mb-lg-0 mb-md-0" icon="plus">
                    Add Tutor
                </x-forms.link-primary>
            </div>
        </div>
        <!-- ACTION BAR END -->

        <!-- TUTORS TABLE START -->
        <div class="d-flex flex-column w-tables rounded mt-3 bg-white">
            <div class="table-responsive">
                <table class="table table-hover border-0 w-100" id="tutors-table">
                    <thead>
                        <tr>
                            <th class="pl-20">#</th>
                            <th>Staff ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Region</th>
                            <th>Status</th>
                            <th class="text-right pr-20">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tutors as $tutor)
                            <tr>
                                <td class="pl-20">{{ $loop->iteration + ($tutors instanceof \Illuminate\Pagination\LengthAwarePaginator ? ($tutors->currentPage() - 1) * $tutors->perPage() : 0) }}</td>
                                <td>{{ $tutor->staff_id }}</td>
                                <td>
                                    <a href="{{ route('elpisview.tutors.show', $tutor->id) }}" class="text-darkest-grey font-weight-bold">
                                        {{ $tutor->name }}
                                    </a>
                                </td>
                                <td>{{ $tutor->email }}</td>
                                <td>{{ $tutor->region->name ?? '-' }}</td>
                                <td>
                                    @if ($tutor->status === 'active')
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-right pr-20">
                                    <div class="task_view">
                                        <a href="{{ route('elpisview.tutors.show', $tutor->id) }}" class="task_view_more d-flex align-items-center justify-content-center" title="View">
                                            <i class="fa fa-eye icons mr-2"></i>
                                        </a>
                                        <a href="{{ route('elpisview.tutors.edit', $tutor->id) }}" class="task_view_more d-flex align-items-center justify-content-center" title="Edit">
                                            <i class="fa fa-edit icons mr-2"></i>
                                        </a>
                                        <a href="javascript:;" class="task_view_more d-flex align-items-center justify-content-center delete-tutor" data-tutor-id="{{ $tutor->id }}" title="Delete">
                                            <i class="fa fa-trash icons"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <p class="text-dark-grey mb-0">No tutors found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($tutors instanceof \Illuminate\Pagination\LengthAwarePaginator && $tutors->hasPages())
                <div class="d-flex justify-content-center mt-3 mb-3">
                    {{ $tutors->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
        <!-- TUTORS TABLE END -->

    </div>
    <!-- CONTENT WRAPPER END -->

@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            // Filter change handlers
            $('#filter-region, #filter-status').on('change', function () {
                applyFilters();
                $('#reset-filters').removeClass('d-none');
            });

            $('#search-text-field').on('keyup', function () {
                if ($(this).val() !== '') {
                    $('#reset-filters').removeClass('d-none');
                }
                applyFilters();
            });

            // Reset filters
            $('#reset-filters').on('click', function () {
                $('#filter-region').val('all');
                $('#filter-status').val('all');
                $('#search-text-field').val('');
                $('.select-picker').selectpicker('refresh');
                $(this).addClass('d-none');
                applyFilters();
            });

            function applyFilters() {
                var region = $('#filter-region').val();
                var status = $('#filter-status').val();
                var search = $('#search-text-field').val();
                var url = "{{ route('elpisview.tutors.index') }}";
                var params = [];

                if (region && region !== 'all') params.push('region=' + region);
                if (status && status !== 'all') params.push('status=' + status);
                if (search) params.push('search=' + encodeURIComponent(search));

                if (params.length > 0) {
                    url += '?' + params.join('&');
                }

                window.location.href = url;
            }

            // Delete tutor
            $('body').on('click', '.delete-tutor', function () {
                var id = $(this).data('tutor-id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete',
                    cancelButtonText: 'Cancel'
                }).then(function (result) {
                    if (result.isConfirmed) {
                        var url = "{{ route('elpisview.tutors.destroy', ':id') }}";
                        url = url.replace(':id', id);

                        $.ajax({
                            url: url,
                            type: 'DELETE',
                            data: { _token: '{{ csrf_token() }}' },
                            success: function (response) {
                                if (response.status === 'success') {
                                    window.location.reload();
                                }
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
