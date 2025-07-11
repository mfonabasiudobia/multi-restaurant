@extends('layouts.app')

@section('content')
<div class="app-page-title">
    <div class="page-title-wrapper">
        <div class="page-title-heading">
            <div>
                {{ __('Shops') }}
                <div class="page-title-subheading">
                    {{ __('This is a shops list.') }}
                </div>
            </div>
        </div>
        <div class="page-title-actions">
            <div class="d-flex align-items-center gap-2">
                <button class="grid-view-btn active" onclick="toggleView('grid')">
                    <i class="fas fa-th-large"></i>
                </button>
                <button class="list-view-btn" onclick="toggleView('list')">
                    <i class="fas fa-list"></i>
                </button>
            </div>
            @hasPermission('admin.shop.create')
                <a href="{{ route('admin.shop.create') }}" class="btn btn-primary ms-3">
                    <i class="fa fa-plus me-1"></i>
                    {{ __('Create New Shop') }}
                </a>
            @endhasPermission
        </div>
    </div>
</div>

<div class="shops-container grid-view">
    <div class="row" id="shopsWrapper">
        @foreach($shops as $shop)
        <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
            <div class="shop-card">
                <div class="shop-logo">
                    <img src="{{ $shop->logo }}" alt="{{ $shop->name }}">
                </div>
                <div class="shop-info">
                    <h5>{{ $shop->name }}</h5>
                    <p class="text-muted">{{ $shop->email }}</p>
                </div>
                <div class="shop-stats">
                    <div class="stat">
                        <span class="label">{{ __('Products') }}</span>
                        <span class="value">{{ $shop->products_count }}</span>
                    </div>
                    <div class="stat">
                        <span class="label">{{ __('Orders') }}</span>
                        <span class="value">{{ $shop->orders_count }}</span>
                    </div>
                </div>
                <div class="shop-actions">
                    <button class="btn btn-sm btn-light" onclick="toggleStatus({{ $shop->id }})">
                        <span class="status-badge {{ $shop->is_active ? 'active' : '' }}"></span>
                        {{ $shop->is_active ? __('Active') : __('Inactive') }}
                    </button>
                    <div class="btn-group">
                        <a href="{{ route('admin.shop.show', $shop->id) }}" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.shop.edit', $shop->id) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Add List View Table -->
    <div class="table-responsive list-view-table" style="display: none;">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>{{ __('Shop') }}</th>
                    <th>{{ __('Owner') }}</th>
                    <th>{{ __('Products') }}</th>
                    <th>{{ __('Orders') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($shops as $shop)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <img src="{{ $shop->logo }}" alt="{{ $shop->name }}" class="shop-list-logo me-2">
                            <div>
                                <h6 class="mb-0">{{ $shop->name }}</h6>
                                <small class="text-muted">{{ $shop->email }}</small>
                            </div>
                        </div>
                    </td>
                    <td>{{ $shop->owner->name }}</td>
                    <td>{{ $shop->products_count }}</td>
                    <td>{{ $shop->orders_count }}</td>
                    <td>
                        <button class="btn btn-sm status-toggle" onclick="toggleStatus({{ $shop->id }})">
                            <span class="status-badge {{ $shop->is_active ? 'active' : '' }}"></span>
                            {{ $shop->is_active ? __('Active') : __('Inactive') }}
                        </button>
                    </td>
                    <td>
                        <div class="btn-group">
                            @if($shop->status === 'pending')
                            <button class="btn btn-sm btn-success" onclick="approveShop({{ $shop->id }})">
                                <i class="fas fa-check"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="denyShop({{ $shop->id }})">
                                <i class="fas fa-times"></i>
                            </button>
                            @endif
                            <a href="{{ route('admin.shop.show', $shop->id) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.shop.edit', $shop->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-sm btn-danger" onclick="deleteShop({{ $shop->id }})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
function toggleView(view) {
    const container = document.querySelector('.shops-container');
    const gridBtn = document.querySelector('.grid-view-btn');
    const listBtn = document.querySelector('.list-view-btn');
    const listTable = document.querySelector('.list-view-table');
    const gridWrapper = document.querySelector('#shopsWrapper');
    
    if (view === 'list') {
        listTable.style.display = 'block';
        gridWrapper.style.display = 'none';
        listBtn.classList.add('active');
        gridBtn.classList.remove('active');
    } else {
        listTable.style.display = 'none';
        gridWrapper.style.display = 'flex';
        gridBtn.classList.add('active');
        listBtn.classList.remove('active');
    }
    
    localStorage.setItem('shopsViewPreference', view);
}

function approveShop(shopId) {
    // Implement shop approval logic
    if(confirm('Are you sure you want to approve this shop?')) {
        axios.post(`/admin/shops/${shopId}/approve`)
            .then(response => {
                toastr.success('Shop approved successfully');
                location.reload();
            })
            .catch(error => {
                toastr.error('Error approving shop');
            });
    }
}

function denyShop(shopId) {
    // Implement shop denial logic
    if(confirm('Are you sure you want to deny this shop?')) {
        axios.post(`/admin/shops/${shopId}/deny`)
            .then(response => {
                toastr.success('Shop denied successfully');
                location.reload();
            })
            .catch(error => {
                toastr.error('Error denying shop');
            });
    }
}

function deleteShop(shopId) {
    // Implement shop deletion logic
    if(confirm('Are you sure you want to delete this shop?')) {
        axios.delete(`/admin/shops/${shopId}`)
            .then(response => {
                toastr.success('Shop deleted successfully');
                location.reload();
            })
            .catch(error => {
                toastr.error('Error deleting shop');
            });
    }
}

function toggleStatus(shopId) {
    // Your existing toggle status code
}
</script>
@endpush

@push('styles')
<style>
/* Additional custom styles if needed */
.status-badge {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    margin-right: 5px;
    background-color: #ddd;
}

.status-badge.active {
    background-color: #28a745;
}

.shop-card {
    height: 100%;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    transition: transform 0.2s;
}

.shop-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

/* Add styles for list view */
.shop-list-logo {
    width: 40px;
    height: 40px;
    border-radius: 4px;
    object-fit: cover;
}

.list-view-table {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.list-view-table th {
    background: #f8f9fa;
}

.status-toggle {
    min-width: 80px;
}
</style>
@endpush 