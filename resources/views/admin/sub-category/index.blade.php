@extends('layouts.app')
@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <h4>{{ __('Sub Categories') }}</h4>

        @hasPermission('admin.subcategory.create')
        <a href="{{ route('admin.subcategory.create') }}" class="btn py-2 btn-primary">
            <i class="fa fa-plus-circle"></i> {{ __('Create New') }}
        </a>
        @endhasPermission
    </div>

    <div class="container-fluid mt-3">
        <!-- Search and Sort Form -->
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ __('Search') }}</label>
                    <input type="text" id="searchInput" class="form-control" 
                           placeholder="{{ __('Search by name...') }}" 
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ __('Sort By') }}</label>
                    <select id="sortSelect" class="form-control">
                        <option value="">{{ __('Select Sort Option') }}</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>
                            {{ __('Name (A-Z)') }}
                        </option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>
                            {{ __('Name (Z-A)') }}
                        </option>
                    </select>
                </div>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="button" onclick="applyFilters()" class="btn btn-primary me-2">
                    <i class="fa fa-search"></i> {{ __('Search') }}
                </button>
                <button type="button" onclick="clearFilters()" class="btn btn-secondary">
                    <i class="fa fa-times"></i> {{ __('Clear') }}
                </button>
            </div>
        </div>

        <div class="mb-3 card">
            <div class="card-body">
                <div class="cardTitleBox">
                    <h5 class="card-title chartTitle">
                        {{__('Sub Categories')}}
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-responsive-md">
                        <thead>
                            <tr>
                                <th class="text-center">{{ __('SL') }}</th>
                                <th>{{ __('Category') }}</th>
                                <th>{{ __('Name') }}</th>
                                @hasPermission('admin.subcategory.toggle')
                                <th>{{ __('Status') }}</th>
                                @endhasPermission
                                @hasPermission('admin.subcategory.edit')
                                <th class="text-center">{{ __('Action') }}</th>
                                @endhasPermission
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subCategories as $key => $subCategory)
                                @php
                                    $serial = $subCategories->firstItem() + $key;
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $serial }}</td>
                                    <td>
                                        @forelse ($subCategory->categories  as $category)
                                            <span class="badge rounded-pill text-bg-primary me-1">{{ $category->name }}</span>
                                        @empty
                                            N/A
                                        @endforelse
                                    </td>
                                    <td>{{ $subCategory->name }}</td>
                                    @hasPermission('admin.subcategory.toggle')
                                    <td>
                                        <label class="switch mb-0">
                                            <a href="{{ route('admin.subcategory.toggle', $subCategory->id) }}">
                                                <input type="checkbox" {{ $subCategory->is_active ? 'checked' : '' }}>
                                                <span class="slider round"></span>
                                            </a>
                                        </label>
                                    </td>
                                    @endhasPermission
                                    @hasPermission('admin.subcategory.edit')
                                    <td class="text-center">
                                        <div class="d-flex gap-3 justify-content-center">
                                            <a href="{{ route('admin.subcategory.edit', $subCategory->id) }}" class="btn btn-outline-primary circleIcon">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>
                                            <form action="{{ route('admin.subcategory.destroy', $subCategory->id) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('{{ __('Are you sure you want to delete this subcategory?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger circleIcon">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </form>
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
            {{ $subCategories->withQueryString()->links() }}
        </div>

    </div>
@endsection

@push('scripts')
<script>
function applyFilters() {
    const search = document.getElementById('searchInput').value;
    const sort = document.getElementById('sortSelect').value;
    
    let url = new URL(window.location.href);
    url.searchParams.set('search', search);
    if (sort) {
        url.searchParams.set('sort', sort);
    } else {
        url.searchParams.delete('sort');
    }
    
    window.location.href = url.toString();
}

function clearFilters() {
    window.location.href = '{{ route("admin.subcategory.index") }}';
}

// Add event listeners for immediate filtering
document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        applyFilters();
    }
});

document.getElementById('sortSelect').addEventListener('change', function() {
    applyFilters();
});
</script>
@endpush

@push('styles')
<style>
    .search-form {
        background-color: #fff;
        padding: 15px;
        border-radius: 5px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.12);
    }
    .search-form .form-control {
        border-radius: 4px;
    }
    .search-form .btn {
        padding: 8px 15px;
    }
    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
    }

    input:checked + .slider {
        background-color: #2196F3;
    }

    input:checked + .slider:before {
        transform: translateX(26px);
    }

    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
</style>
@endpush
