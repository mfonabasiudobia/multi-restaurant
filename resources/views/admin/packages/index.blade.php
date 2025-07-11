@extends('layouts.app')

@section('content')
<div class="app-page-title">
    <div class="page-title-wrapper">
        <div class="page-title-heading">
            <div>{{ __('Package Management') }}
                <div class="page-title-subheading">{{ __('Manage seller packages') }}</div>
            </div>
        </div>
        <div class="page-title-actions">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPackageModal">
                <i class="fa fa-plus"></i> {{ __('Create Package') }}
            </button>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Product Limit') }}</th>
                        <th>{{ __('Price') }}</th>
                        <th>{{ __('Duration') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($packages as $package)
                    <tr>
                        <td>{{ $package->name }}</td>
                        <td>{{ $package->product_limit }}</td>
                        <td>{{ showCurrency($package->price) }}</td>
                        <td>{{ $package->duration_days }} {{ __('days') }}</td>
                        <td>
                            <span class="badge {{ $package->is_active ? 'bg-success' : 'bg-danger' }}">
                                {{ $package->is_active ? __('Active') : __('Inactive') }}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="editPackage({{ $package->id }})">
                                <i class="fa fa-edit"></i>
                            </button>
                            <a href="{{ route('admin.packages.toggle', $package->id) }}" class="btn btn-sm btn-warning">
                                <i class="fa fa-sync"></i>
                            </a>
                            <form action="{{ route('admin.packages.destroy', $package->id) }}" method="POST" class="d-inline" 
                                  onsubmit="return confirm('{{ __('Are you sure you want to delete this package?') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Package Modal -->
@include('admin.packages.create-modal')

<!-- Edit Package Modal -->
@include('admin.packages.edit-modal')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    function editPackage(packageId) {
        // Get package data
        axios.get(`/admin/packages/${packageId}/edit`)
            .then(response => {
                const package = response.data;
                
                // Fill the form
                const form = document.getElementById('editPackageForm');
                form.action = `/admin/packages/${packageId}`;
                form.querySelector('input[name="name"]').value = package.name;
                form.querySelector('textarea[name="description"]').value = package.description || '';
                form.querySelector('input[name="product_limit"]').value = package.product_limit;
                form.querySelector('input[name="price"]').value = package.price;
                form.querySelector('input[name="duration_days"]').value = package.duration_days;
                
                // Show modal
                new bootstrap.Modal(document.getElementById('editPackageModal')).show();
            })
            .catch(error => {
                console.error('Error fetching package:', error);
                alert('Error fetching package details');
            });
    }

    // Handle form submission
    document.getElementById('editPackageForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = e.target;
        const formData = new FormData(form);
        
        // Add _method field for PUT request
        formData.append('_method', 'PUT');
        
        axios.post(form.action, formData)
            .then(response => {
                // Close modal
                bootstrap.Modal.getInstance(document.getElementById('editPackageModal')).hide();
                // Reload page to show updated data
                window.location.reload();
            })
            .catch(error => {
                console.error('Error updating package:', error);
                alert('Error updating package');
            });
    });
</script>
@endpush
@endsection