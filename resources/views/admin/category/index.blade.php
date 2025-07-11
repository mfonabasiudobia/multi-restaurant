@extends('layouts.app')
@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">

        <h4>
            {{ __('Category List') }}
        </h4>

        @hasPermission('admin.category.create')
        <a href="{{ route('admin.category.create') }}" class="btn py-2 btn-primary">
            <i class="fa fa-plus-circle"></i>
            {{__('Create New')}}
        </a>
        @endhasPermission
    </div>

    <div class="container-fluid mt-3">

        <div class="mb-3 card">
            <div class="card-body">
                <div class="cardTitleBox">
                    <h5 class="card-title chartTitle">
                        {{__('Categories')}}
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-responsive-md">
                        <thead>
                            <tr>
                                <th class="text-center">{{ __('SL') }}</th>
                                <th>{{ __('Thumbnail') }}</th>
                                <th>{{ __('Name') }}</th>
                                @hasPermission('admin.category.toggle')
                                <th>{{ __('Status') }}</th>
                                @endhasPermission
                                @hasPermission('admin.category.edit')
                                <th class="text-center">{{ __('Action') }}</th>
                                @endhasPermission
                            </tr>
                        </thead>
                        @forelse($categories as $key => $category)
                            @php
                                $serial = $categories->firstItem() + $key;
                            @endphp
                            <tr>
                                <td class="text-center">{{ $serial }}</td>

                                <td>
                                    <img src="{{ $category->thumbnail }}" width="50">
                                </td>

                                <td>{{ $category->name }}</td>

                                @hasPermission('admin.category.toggle')
                                <td>
                                    <label class="switch mb-0">
                                        <a href="{{ route('admin.category.toggle', $category->id) }}">
                                            <input type="checkbox" {{ $category->status ? 'checked' : '' }}>
                                            <span class="slider round"></span>
                                        </a>
                                    </label>
                                </td>
                                @endhasPermission
                                @hasPermission('admin.category.edit')
                                <td class="text-center">
                                    <div class="d-flex gap-3 justify-content-center">
                                        <a href="{{ route('admin.category.edit', $category->id) }}" class="btn btn-outline-primary circleIcon">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <form action="{{ route('admin.category.destroy', $category->id) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('{{ __('Are you sure you want to delete this category?') }}')">
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
            {{ $categories->withQueryString()->links() }}
        </div>

    </div>
@endsection

@push('scripts')
<script>
    function viewCategory(categoryId) {
        axios.get(`/admin/category/${categoryId}`)
            .then(response => {
                const category = response.data.category;
                let html = `
                    <div class="modal fade" id="viewCategoryModal" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">${category.name}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <img src="${category.thumbnail}" class="img-fluid mb-3" style="max-height: 200px;">
                                    <p><strong>{{ __('Status') }}:</strong> ${category.status ? '{{ __('Active') }}' : '{{ __('Inactive') }}'}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                const existingModal = document.getElementById('viewCategoryModal');
                if (existingModal) {
                    existingModal.remove();
                }
                
                document.body.insertAdjacentHTML('beforeend', html);
                new bootstrap.Modal(document.getElementById('viewCategoryModal')).show();
            })
            .catch(error => {
                console.error('Error fetching category:', error);
                alert('Error fetching category details');
            });
    }
</script>
@endpush
