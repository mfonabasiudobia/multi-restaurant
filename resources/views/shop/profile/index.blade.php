@extends('layouts.app')

@section('content')
    <div>
        <h4>
            {{ __('Profile Details') }}
        </h4>
    </div>

    <div class="card mt-3 shadow-sm">
        <div class="card-body">
            <div class="d-flex gap-3">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                @if($shop->user->media)
                                    <img src="{{ Storage::disk('s3')->url($shop->user->media->src) }}" alt="Profile" class="img-fluid rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                                @else
                                    <img src="{{ asset('default/profile.jpg') }}" alt="Profile" class="img-fluid rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                                @endif
                            </div>
                            <h5 class="card-title">{{ $shop->user->name }}</h5>
                            <p class="card-text">{{ $shop->user->email }}</p>
                            <p class="card-text">{{ $shop->user->phone }}</p>
                            
                            <div class="mt-3">
                                <a href="{{ route('shop.profile.edit') }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-pencil"></i> {{ __('Edit Profile') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex-grow-1">
                    <div class="d-flex gap-2 justify-content-between ">
                        <h3 class="mb-2 pb-1">{{ $shop->name }}</h3>

                        <div>
                            <a href="{{ route('shop.profile.edit') }}" class="btn btn-primary btn-sm">
                                <i class="fa-solid fa-pen me-1"></i> {{ __('Edit') }}
                            </a>
                        </div>
                    </div>

                    <div class="d-flex gap-3 align-items-center">
                        <div>
                            <i class="fa-solid fa-star text-warning"></i>
                            {{ $shop->averageRating }}
                        </div>

                        <div class="border" style="width: 0; height: 16px;"></div>

                        <div>
                            {{ $shop->reviews->count() }}
                            {{ __('Reviews') }}
                        </div>
                    </div>

                    <a href="/shops/{{ $shop->id }}" target="_blank" class="btn btn-outline-primary mt-3">
                        {{ __('View Live') }}
                    </a>
                </div>
            </div>

            <div class="border-top my-3"></div>

            <div class="d-flex gap-4 flex-wrap">
                <div class="d-flex flex-column border gap-2 p-3">
                    <div>
                        <span> {{ __('Total products') }}:</span> {{ $shop->products->count() }}
                    </div>
                    <div>
                        <span>{{ __('Total Orders') }}:</span> {{ $shop->orders->count() }}
                    </div>
                </div>

                <div>
                    <h5> {{ __('Shop Information') }}</h5>
                    <table class="table mb-0">
                        <tr>
                            <td class="border-top">{{ __('Name') }}</td>
                            <td class="border-top">{{ $shop->name }}</td>
                        </tr>

                        <tr>
                            <td>{{ __('Estimated Delivery Time') }}</td>
                            <td>{{ $shop->estimated_delivery_time }}</td>
                        </tr>
                    </table>
                </div>

                <div class="ms-lg-4">
                    <h5> {{ __('User Information') }}</h5>
                    <table class="table mb-0">
                        <tr>
                            <td class="border-top">{{ __('Name') }}</td>
                            <td class="border-top">{{ $shop->user?->name }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('Phone number') }}</td>
                            <td>{{ $shop->user?->phone }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('Email') }}</td>
                            <td>{{ $shop->user?->email }}</td>
                        </tr>
                    </table>
                </div>

            </div>

        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <div class="d-flex gap-2 border-bottom pb-2">
                <i class="fa-solid fa-shop"></i>
                <h5>{{ __('Shop Information') }}</h5>
            </div>

            <!-- Business Information -->
            <div class="mt-4">
                <h6 class="border-bottom pb-2">{{ __('Business Information') }}</h6>
                <div class="row mt-3">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">{{ __('VAT Number') }}</label>
                            <p>{{ $businessInfo?->vat_number ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Business Location') }}</label>
                            <p>{{ $businessInfo?->business_location ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Company Name') }}</label>
                            <p>{{ $businessInfo?->company_name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Existing shop information -->
        </div>
    </div>
@endsection
