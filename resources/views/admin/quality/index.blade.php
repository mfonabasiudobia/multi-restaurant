@extends('layouts.app')
@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">

        <h4>
            {{ __('Quality List') }}
        </h4>

        @hasPermission('admin.quality.create')
            <button type="button" data-bs-toggle="modal" data-bs-target="#createQuality" class="btn py-2 btn-primary">
                <i class="fa fa-plus-circle"></i>
                {{ __('Create New') }}
            </button>
        @endhasPermission
    </div>

    <div class="container-fluid mt-3">

        <div class="mb-3 card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-responsive-md">
                        <thead>
                            <tr>
                                <th class="text-center">{{ __('SL') }}</th>
                                <th>{{ __('Name') }}</th>
                                @hasPermission('admin.quality.toggle')
                                    <th>{{ __('Status') }}</th>
                                @endhasPermission
                                @hasPermission('admin.quality.edit')
                                    <th class="text-center">{{ __('Action') }}</th>
                                @endhasPermission
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($qualities as $key => $quality)
                                @php
                                    $serial = $qualities->firstItem() + $key;
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $serial }}</td>
                                    <td>{{ $quality->name }}</td>

                                    @hasPermission('admin.quality.toggle')
                                        <td>
                                            <label class="switch mb-0">
                                                <a href="{{ route('admin.quality.toggle', $quality->id) }}">
                                                    <input type="checkbox" {{ $quality->is_active ? 'checked' : '' }}>
                                                    <span class="slider round"></span>
                                                </a>
                                            </label>
                                        </td>
                                    @endhasPermission

                                    @hasPermission('admin.quality.edit')
                                        <td class="text-center">
                                            <div class="d-flex gap-3 justify-content-center">
                                                <button type="button" class="btn btn-outline-primary btn-sm circleIcon"
                                                    onclick="openQualityUpdateModal({{ $quality }})">
                                                    <i class="fa-solid fa-pen"></i>
                                                </button>
                                            </div>
                                        </td>
                                    @endhasPermission
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-center" colspan="100%">{{ __('No Data Found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="my-3">
            {{ $qualities->withQueryString()->links() }}
        </div>

    </div>

    <!--=== Create Quality Modal ===-->
    <form action="{{ route('admin.quality.store') }}" method="POST">
        @csrf
        <div class="modal fade" id="createQuality" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ __('Create Quality') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <div class="mb-3">
                            <label for="name" class="form-label">
                                {{ __('Name') }}
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="{{ __('Name') }}" required />
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('Close') }}
                        </button>
                        <button type="submit" class="btn btn-primary">
                            {{ __('Submit') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!--=== Update Quality Modal ===-->
    <form action="" id="updateQuality" method="POST">
        @csrf
        @method('PUT')
        <div class="modal fade" id="updateQualityModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ __('Update Quality') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <div class="mb-3">
                            <label for="updateName" class="form-label">
                                {{ __('Name') }}
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="updateName" name="name"
                                placeholder="{{ __('Name') }}" required />
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('Close') }}
                        </button>
                        <button type="submit" class="btn btn-primary">
                            {{ __('Update') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        const openQualityUpdateModal = (quality) => {
            $("#updateName").val(quality.name);
            $("#updateQuality").attr('action', `{{ route('admin.quality.update', ':id') }}`.replace(':id', quality.id));
            $("#updateQualityModal").modal('show');
        }
    </script>
@endpush