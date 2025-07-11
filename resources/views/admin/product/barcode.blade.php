@extends('layouts.app')
@section('content')
    <div class="mb-3">
        <h4>{{ __('Product Barcode') }}</h4>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                @for ($i = 0; $i < $quantitys; $i++)
                    <div class="col-3 text-center p-2 border">
                        {!! DNS1D::getBarcodeHTML($product->code, 'C128') !!}
                        <p class="mt-2 mb-0">{{ $product->code }}</p>
                    </div>
                @endfor
            </div>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('admin.product.index') }}" class="btn btn-light">
            {{ __('Back to List') }}
        </a>
        <button onclick="window.print()" class="btn btn-primary">
            {{ __('Print Barcode') }}
        </button>
    </div>
@endsection 