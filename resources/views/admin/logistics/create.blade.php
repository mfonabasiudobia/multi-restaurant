@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Add New Logistics Entry') }}</h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.logistics.index') }}" class="btn btn-primary">
                                {{ __('Back to List') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.logistics.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="article_name">{{ __('Article Name') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="article_name" id="article_name" class="form-control @error('article_name') is-invalid @enderror" value="{{ old('article_name') }}" required>
                                        @error('article_name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="bag_number">{{ __('Bag Number') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="bag_number" id="bag_number" class="form-control @error('bag_number') is-invalid @enderror" value="{{ old('bag_number') }}" required>
                                        @error('bag_number')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="location">{{ __('Location') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="location" id="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location') }}" required>
                                        @error('location')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="row">{{ __('Row') }} <span class="text-danger">*</span></label>
                                        <input type="number" name="row" id="row" class="form-control @error('row') is-invalid @enderror" value="{{ old('row') }}" required>
                                        @error('row')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">{{ __('Create Entry') }}</button>
                                    <a href="{{ route('admin.logistics.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 