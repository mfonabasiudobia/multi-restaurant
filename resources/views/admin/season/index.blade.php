@extends('layouts.app')
@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">

        <h4>
            {{ __('Season List') }}
        </h4>

        @hasPermission('admin.season.create')
            <button type="button" data-bs-toggle="modal" data-bs-target="#createSeason" class="btn py-2 btn-primary">
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
                                <th>{{ __('Description') }}</th>
                                @hasPermission('admin.season.toggle')
                                    <th>{{ __('Status') }}</th>
                                @endhasPermission
                                @hasPermission('admin.season.edit')
                                    <th class="text-center">{{ __('Action') }}</th>
                                @endhasPermission
                            </tr>
                        </thead>
                        @forelse($seasons as $key => $season)
                            @php
                                $serial = $seasons->firstItem() + $key;
                            @endphp
                            <tr>
                                <td class="text-center">{{ $serial }}</td>
                                <td>{{ $season->name }}</td>
                                <td>{{ $season->description }}</td>

                                @hasPermission('admin.season.toggle')
                                    <td>
                                        <label class="switch mb-0">
                                            <a href="{{ route('admin.season.toggle', $season->id) }}">
                                                <input type="checkbox" {{ $season->is_active ? 'checked' : '' }}>
                                                <span class="slider round"></span>
                                            </a>
                                        </label>
                                    </td>
                                @endhasPermission

                                @hasPermission('admin.season.edit')
                                    <td class="text-center">
                                        <div class="d-flex gap-3 justify-content-center">
                                            <button type="button" class="btn btn-outline-primary btn-sm circleIcon"
                                                onclick="openSeasonUpdateModal({{ $season }})">
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
            {{ $seasons->withQueryString()->links() }}
        </div>

    </div>

    <!--=== Create Season Modal ===-->
    <form action="{{ route('admin.season.store') }}" method="POST">
        @csrf
        <div class="modal fade" id="createSeason" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ __('Create Season') }}
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

                        <div class="mb-3">
                            <label for="description" class="form-label">
                                {{ __('Description') }}
                            </label>
                            <textarea class="form-control" id="description" name="description" rows="3"
                                placeholder="{{ __('Description') }}"></textarea>
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

    <!--=== Update Season Modal ===-->
    <form action="" id="updateSeason" method="POST">
        @csrf
        @method('PUT')
        <div class="modal fade" id="updateSeasonModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ __('Update Season') }}
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

                        <div class="mb-3">
                            <label for="updateDescription" class="form-label">
                                {{ __('Description') }}
                            </label>
                            <textarea class="form-control" id="updateDescription" name="description" rows="3"
                                placeholder="{{ __('Description') }}"></textarea>
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
        const openSeasonUpdateModal = (season) => {
            $("#updateName").val(season.name);
            $("#updateDescription").val(season.description);
            $("#updateSeason").attr('action', `{{ route('admin.season.update', ':id') }}`.replace(':id', season.id));
            $("#updateSeasonModal").modal('show');
        }
    </script>
@endpush