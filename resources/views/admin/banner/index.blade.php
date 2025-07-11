@extends('layouts.app')
@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <h4>{{ __('Banner List') }}</h4>

        @hasPermission('admin.banner.create')
            <a href="{{ route('admin.banner.create') }}" class="btn py-2 btn-primary">
                <i class="fa fa-plus-circle"></i>
                {{ __('Create New') }}
            </a>
        @endhasPermission
    </div>

    <div class="container-fluid mt-3">

        <div class="my-3 card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table border table-responsive-lg">
                        <thead>
                            <tr>
                                <th>{{ __('Thumbnail') }}</th>
                                <th>{{ __('Title') }}</th>
                                <th>{{ __('Redirect Link') }}</th>
                                @hasPermission('admin.banner.toggle')
                                    <th class="text-center">{{ __('Status') }}</th>
                                @endhasPermission
                                <th class="text-center">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($banners as $banner)
                            <tr>
                                <td>
                                    <img src="{{ $banner->thumbnail }}" height="76">
                                </td>

                                <td>
                                    {{ $banner->title }}
                                    @if ($businessModel != 'single' && $banner->shop_id)
                                        <br>
                                        <span class="badge bg-primary">
                                            <i class="fa-solid fa-store"></i>
                                            {{ $banner->shop?->name }}
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <a href="{{ $banner->redirectLink }}" target="_blank">{{ $banner->redirectLink }}</a>
                                </td>

                                @hasPermission('admin.banner.toggle')
                                    <td class="text-center">
                                        <label class="switch mb-0" data-bs-toggle="tooltip" data-bs-placement="left"
                                            data-bs-title="Status Toggle">
                                            <a href="{{ route('admin.banner.toggle', $banner->id) }}">
                                                <input type="checkbox" {{ $banner->status ? 'checked' : '' }}>
                                                <span class="slider round"></span>
                                            </a>
                                        </label>
                                    </td>
                                @endhasPermission

                                <td class="text-center">
                                    <div class="d-flex gap-2 justify-content-center">
                                        @hasPermission('admin.banner.edit')
                                            <a href="{{ route('admin.banner.edit', $banner->id) }}"
                                                class="btn btn-outline-info btn-sm circleIcon">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>
                                        @endhasPermission

                                        @hasPermission('admin.banner.destroy')
                                            <a href="{{ route('admin.banner.destroy', $banner->id) }}"
                                                class="btn btn-outline-danger btn-sm deleteConfirm circleIcon">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                        @endhasPermission
                                    </div>
                                </td>
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
            {{ $banners->links() }}
        </div>

    </div>
@endsection