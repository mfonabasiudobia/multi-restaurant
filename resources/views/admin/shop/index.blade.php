@extends('layouts.app')

@section('content')
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="w-100 page-title-heading d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    {{__('Shops')}}
                    <div class="page-title-subheading">
                        {{__('This is a shops list')}}
                    </div>
                </div>
                <div class="d-flex gap-2 align-items-center gap-md-4">
                    <div class="d-flex gap-2 gap-md-3">
                        <button class="gridBtn" id="gridView" data-bs-toggle="tooltip" data-bs-placement="top"
                        data-bs-title="{{__('Grid View')}}">
                            <i class="bi bi-grid-3x3-gap-fill"></i>
                        </button>
                        <button class="gridBtn" id="listView" data-bs-toggle="tooltip" data-bs-placement="top"
                            data-bs-title="{{__('List View')}}">
                            <i class="fa-solid fa-list-ul"></i>
                        </button>
                    </div>

                    @hasPermission('admin.shop.create')
                    <a href="{{ route('admin.shop.create') }}" class="btn py-2 btn-primary">
                        <i class="fa fa-plus-circle"></i>
                        {{__('Create New Shop')}}
                    </a>
                    @endhasPermission
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">

        <div class="row row-gap mb-4 d-none" id="gridItem">
            @foreach ($shops as $key => $shop)
                <div class="col-12 col-md-6 col-xl-4 col-xxl-3">
                    <div class="card shadow-sm rounded-12 show-card position-relative overflow-hidden">
                        <div class="card-body shop p-2">
                            <div class="banner">
                                <img class="img-fit" src="{{ $shop->banner }}" />
                            </div>
                            <div class="main-content">
                                <div class="logo">
                                    <img class="img-fit" src="{{ $shop->logo }}" />
                                </div>
                                <div class="personal">
                                    <span class="name">{{ $shop->name }}</span>
                                    <span class="email">{{ $shop->user?->email }}</span>
                                </div>
                            </div>
                            <div class="d-flex flex-column gap-2 px-3 mt-2">
                                <div class="item">
                                    <strong>{{ __('Status') }}</strong>
                                    <span class="badge {{ $shop->user->is_active ? 'bg-success' : ($shop->denial_message ? 'bg-danger' : 'bg-warning') }}">
                                        @if($shop->user->is_active)
                                            {{ __('Active') }}
                                        @elseif($shop->denial_message)
                                            {{ __('Denied') }}
                                            <i class="fa fa-info-circle ms-1" data-bs-toggle="tooltip" 
                                               title="{{ $shop->denial_message }}" style="cursor: pointer"></i>
                                        @else
                                            {{ __('Pending') }}
                                        @endif
                                    </span>
                                </div>
                              
              
                                @hasPermission('admin.shop.products')
                                <div class="item">
                                    <strong>{{ __('Products') }}</strong>
                                    <a href="{{ route('admin.shop.products', $shop->id) }}" class="btn btn-secondary btn-sm"
                                        data-bs-toggle="tooltip" data-bs-placement="left"
                                        data-bs-title="{{__('Click here to see products')}}">
                                        {{ $shop->products->count() }}
                                    </a>
                                </div>
                                @endhasPermission

                                @hasPermission('admin.shop.orders')
                                <div class="item">
                                    <strong>{{ __('Orders') }}</strong>
                                    <a href="{{ route('admin.shop.orders', $shop->id) }}" class="btn btn-primary btn-sm"
                                        data-bs-toggle="tooltip" data-bs-placement="left"
                                        data-bs-title="{{__('Click here to see orders')}}">
                                        {{ $shop->orders->count() }}
                                    </a>
                                </div>
                                @endhasPermission
                               
                                <div class="item">
                                    <strong>{{ __('Package Status') }}</strong>
                                    @if($shop->package)
                                        <span class="badge {{ $shop->package->is_paid ? 'bg-success' : 'bg-warning' }}">
                                            @if($shop->package->is_paid)
                                                {{ __('Active until') }} {{ $shop->package->expires_at->format('d M Y') }}
                                                <br>
                                                {{ $shop->package->products_used }}/{{ $shop->package->product_limit }} {{ __('products used') }}
                                            @else
                                                {{ __('Payment Pending') }}
                                            @endif
                                        </span>
                                    @else
                                        <span class="badge bg-danger">{{ __('No Package') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-2 justify-content-end">
                            @hasPermission('admin.shop.show')
                            <a class="btn btn-info btn-sm" href="{{ route('admin.shop.show', $shop->id) }}" 
                                data-bs-toggle="tooltip" data-bs-placement="top" title="View">
                                <i class="fa fa-eye"></i> View
                            </a>
                            @endhasPermission
                            
                            @hasPermission('admin.shop.edit')
                            <a class="btn btn-primary btn-sm" href="{{ route('admin.shop.edit', $shop->id) }}" 
                                data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                <i class="fa fa-pen"></i> Edit
                            </a>
                            @endhasPermission

                            @if(!$shop->user->is_active && !$shop->denial_message)
                                <button class="btn btn-success btn-sm" onclick="acceptShop({{ $shop->id }})" 
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Accept">
                                    <i class="fa fa-check"></i> Accept
                                </button>
                                
                                <button class="btn btn-danger btn-sm" onclick="denyShop({{ $shop->id }})" 
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Deny">
                                    <i class="fa fa-times"></i> Deny
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
                        <!-- Deny Shop Modal -->
                        <div class="modal fade" id="denyShopModal{{ $shop->id }}" tabindex="-1" aria-labelledby="denyShopModalLabel{{ $shop->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="denyShopModalLabel{{ $shop->id }}">{{ __('Deny Shop') }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="denyShopForm{{ $shop->id }}" method="POST" action="{{ route('admin.shop.deny', $shop->id) }}">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="denyMessage{{ $shop->id }}" class="form-label">{{ __('Message') }}</label>
                                                <textarea class="form-control" id="denyMessage{{ $shop->id }}" name="message" rows="3" required></textarea>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                                        <button type="submit" class="btn btn-danger" form="denyShopForm{{ $shop->id }}">{{ __('Deny') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>

        <div class="mb-4 d-none" id="listItem">
            <div class="table-responsive">

                <table class="table shopTable table-striped table-responsive-lg">
                    <thead>
                        <tr>
                            <th>{{ __('SL') }}</th>
                            <th>{{ __('Logo') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Phone') }}</th>
                            @hasPermission('admin.shop.status.toggle')
                            <th>{{ __('Status') }}</th>
                            @endhasPermission
                            @hasPermission('admin.shop.products')
                            <th class="text-center">{{ __('Products') }}</th>
                            @endhasPermission
                            @hasPermission('admin.shop.orders')
                            <th class="text-center">{{ __('Orders') }}</th>
                            @endhasPermission
                            <th class="text-center">{{ __('Action') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($shops as $key => $shop)
                            <tr>
                                <td>{{ ++$key }}</td>
                                <td>
                                    <div class="payment-image">
                                        <img class="img-fit" src="{{ $shop->logo }}" />
                                    </div>
                                </td>
                                <td>{{ $shop->name }}</td>
                                <td>{{ $shop->user->email }}</td>
                                <td>{{ $shop->phone }}</td>
                                <td>
                                    <span class="badge {{ $shop->user->is_active ? 'bg-success' : 'bg-warning' }}">
                                        {{ $shop->user->is_active ? __('Active') : __('Pending Approval') }}
                                    </span>
                                </td>
                                @hasPermission('admin.shop.products')
                                <td class="text-center">
                                    <a href="{{ route('admin.shop.products', $shop->id) }}" class="badge badge-square badge-primary" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="{{__('Click here to view total products')}}">
                                        {{ $shop->products->count() }}
                                    </a>
                                </td>
                                @endhasPermission
                                @hasPermission('admin.shop.orders')
                                <td class="text-center">
                                    <a href="{{ route('admin.shop.orders', $shop->id) }}"
                                        class="badge badge-square badge-info" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="{{__('Click here to view total orders')}}">
                                        {{ $shop->orders->count() }}
                                    </a>
                                </td>
                                @endhasPermission
                                <td class="text-center">
                                    @hasPermission('admin.shop.show')
                                    <a class="btn btn-outline-primary circleIcon"
                                        href="{{ route('admin.shop.show', $shop->id) }}" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="View">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    @endhasPermission
                                    @hasPermission('admin.shop.edit')
                                    <a href="{{ route('admin.shop.edit', $shop->id) }}"
                                        class="btn btn-outline-secondary circleIcon" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Edit">
                                        <i class="fa fa-pen"></i>
                                    </a>
                                    @endhasPermission
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>

        <div class="my-3">
            {{ $shops->links() }}
        </div>
    </div>
@endsection
<script>
    function acceptShop(shopId) {
        window.location.href = `{{ url('admin/shops') }}/${shopId}/status-toggle`;
    }

    function denyShop(shopId) {
        // Get the modal
        const modal = document.getElementById(`denyShopModal${shopId}`);
        const modalInstance = new bootstrap.Modal(modal);
        modalInstance.show();
    }
</script>

@foreach($shops as $shop)
    <div class="modal fade" id="denyShopModal{{ $shop->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.shop.deny', $shop->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Deny Shop') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Denial Reason') }}</label>
                            <textarea name="message" class="form-control" rows="3" required 
                                placeholder="{{ __('Enter reason for denial') }}"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="btn btn-danger">
                            {{ __('Deny Shop') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
