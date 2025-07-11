@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Shop Registration') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('shop.register') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Package Selection -->
                        <div class="mb-4">
                            <h4>{{ __('Select Package') }}</h4>
                            <div class="row">
                                @foreach($packages as $package)
                                    <div class="col-md-4">
                                        <div class="card package-card {{ old('package_id') == $package->id ? 'selected' : '' }}">
                                            <div class="card-body">
                                                <h5 class="card-title">{{ $package->name }}</h5>
                                                <p class="card-text">
                                                    <strong>{{ __('Price') }}:</strong> {{ $package->price }}<br>
                                                    <strong>{{ __('Products') }}:</strong> {{ $package->product_limit }}<br>
                                                    <strong>{{ __('Duration') }}:</strong> {{ $package->duration_days }} {{ __('days') }}
                                                </p>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="package_id" 
                                                           value="{{ $package->id }}" 
                                                           {{ old('package_id') == $package->id ? 'checked' : '' }}
                                                           required>
                                                    <label class="form-check-label">
                                                        {{ __('Select Package') }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('package_id')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Phone Number with Format Guide -->
                        <div class="mb-3">
                            <label class="form-label">{{ __('Phone Number') }}</label>
                            <input type="tel" name="phone" 
                                   class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone') }}" 
                                   placeholder="+40 7xx xxx xxx"
                                   required>
                            <small class="form-text text-muted">
                                {{ __('Format: +40 7xx xxx xxx (Romanian) or +[country code][number] (International)') }}
                            </small>
                            @error('phone')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Existing Fields -->
                        <div class="mb-3">
                            <label class="form-label">{{ __('Shop Name') }}</label>
                            <input type="text" name="shop_name" class="form-control @error('shop_name') is-invalid @enderror" 
                                   value="{{ old('shop_name') }}" required>
                            @error('shop_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.package-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.package-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.package-card.selected {
    border: 2px solid #4CAF50;
    background-color: #f8fff8;
}
</style>

<script>
document.querySelectorAll('.package-card').forEach(card => {
    card.addEventListener('click', () => {
        // Remove selected class from all cards
        document.querySelectorAll('.package-card').forEach(c => c.classList.remove('selected'));
        
        // Add selected class to clicked card
        card.classList.add('selected');
        
        // Check the radio button
        card.querySelector('input[type="radio"]').checked = true;
    });
});
</script>
@endsection 