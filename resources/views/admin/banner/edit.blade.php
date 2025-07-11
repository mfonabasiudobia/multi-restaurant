@extends('layouts.app')

@section('content')
    <div class="page-title">
        <div class="d-flex gap-2 align-items-center">
            <i class="fa-solid fa-image"></i> {{__('Edit Banner')}}
        </div>
    </div>
    <form action="{{ route('admin.banner.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">

            <div class="col-md-6">
                <div class="card mt-3 h-100">
                    <div class="card-body">
                        <div class="">
                            <x-input label="Title" name="title" type="text" placeholder="Enter Short Title" :value="$banner->title" />
                        </div>

                        <div class="mt-4">
                            <x-input label="Redirect Link" name="redirectLink" type="url" placeholder="Enter Redirect URL" :value="$banner->redirectLink" />
                        </div>

                        <div class="mt-4">
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <div class="ratio4x1">
                                    <img src="{{ $banner->thumbnail ?? asset('default/default.jpg') }}" id="banner" alt="" width="100%">
                                </div>
                            </div>
                            <x-file name="banner" label="Banner Ratio 4:1 (2000 x 500 px)" preview="banner" />
                        </div>

                        @if ($businessModel != 'single')
                            <div class="mt-4 border d-inline-flex align-items-center justify-content-center gap-2 p-2 rounded">
                                <label for="forShop" class="form-label mb-0 fw-bold">
                                    {{__('This Banner For Own Shop')}}
                                </label>
                                <input type="checkbox" name="for_shop" id="forShop" style="width: 20px; height: 20px" {{ $banner->shop_id ? 'checked' : '' }} class="form-check-input m-0" />
                            </div>
                        @endif

                        <div class="col-12 d-flex justify-content-end mt-4">
                            <button class="btn btn-primary py-2 px-5">
                                {{__('Submit')}}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </form>
@endsection