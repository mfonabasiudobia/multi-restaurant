@extends('layouts.app')

@section('content')
    <div class="page-title">
        <div class="d-flex gap-2 align-items-center">
            <i class="fa-solid fa-border-all"></i> {{__('Edit Sub Category')}}
        </div>
    </div>
    <form action="{{ route('admin.subcategory.update', $subCategory->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-lg-8 m-auto">
                <div class="card mt-3">
                    <div class="card-body">

                        <div class="d-flex gap-2 border-bottom pb-2">
                            <i class="fa-solid fa-user"></i>
                            <h5>
                                {{ __('Sub Category Information') }}
                            </h5>
                        </div>

                        <div class="mt-3">
                            <label class="form-label">{{ __('Name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" value="{{ old('name', $subCategory->name) }}" required>
                        </div>

                        <div class="mt-3">
                            <label class="form-label">{{ __('Categories') }} <span class="text-danger">*</span></label>
                            <select name="categories[]" class="form-control select2" multiple required>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ in_array($category->id, $subCategory->categories->pluck('id')->toArray()) ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mt-5 d-flex gap-2 justify-content-between">
                            <a href="{{ route('admin.category.index') }}" class="btn btn-secondary py-2 px-4">
                                {{__('Back')}}
                            </a>

                            <button type="submit" class="btn btn-primary py-2 px-4">
                                {{__('Update') }}
                            </button>

                        </div>

                    </div>

                </div>
            </div>
        </div>

    </form>
@endsection
