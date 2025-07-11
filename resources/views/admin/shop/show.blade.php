@extends('layouts.app')

@section('content')
    <div>
        <h4>
            {{ __('Shop Details') }}
        </h4>
    </div>

    @include('admin.shop.header-nav')

    <div class="card mt-3 shadow-sm">
        <div class="card-body">
            <div class="d-flex gap-3 flex-wrap">
                <div class="rounded overflow-hidden">
                    <img src="{{ $shop->logo }}" alt="" width="140">
                </div>

                <div class="flex-grow-1">
                    <div class="d-flex gap-3 align-items-center mb-2 justify-content-between">
                        <h3 class="m-0">{{ $shop->name }}</h3>
                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#ResetPasswordModal">
                            <i class="bi bi-shield-lock-fill"></i>
                            {{ __('Reset Password') }}
                        </button>
                    </div>

                    {{-- <div class="d-flex gap-3 align-items-center">
                        <div>
                            <i class="fa-solid fa-star text-warning"></i>
                            {{ $shop->averageRating }}
                        </div>

                        <div class="border" style="width: 0; height: 16px;"></div>

                        <div>
                            {{ $shop->reviews->count() }}
                            {{ __('Reviews') }}
                        </div>
                    </div> --}}

                    <a href="/shops/{{ $shop->id }}" target="blank" class="btn btn-outline-primary mt-3">
                        {{__('View Live')}}
                    </a>
                </div>
            </div>

            <div class="border-top my-3"></div>

            <div class="d-flex gap-4 flex-wrap">
                <div class="d-flex flex-column border gap-2 p-3">
                    <div>
                        <span>{{__('Total products')}}:</span> 
                        @php
                            $activeProducts = $shop->products()->where('is_active', true)->count();
                            $totalProducts = $shop->products()->count();
                        @endphp
                        {{ $activeProducts }} {{ __('active') }} / {{ $totalProducts }} {{ __('total') }}
                    </div>
                    <div>
                        <span>{{__('Total Orders')}}:</span> {{ $shop->orders->count() }}
                    </div>
                </div>

                <div>
                    <h5>{{__('Shop Information')}}</h5>
                    <table class="table mb-0">
                        <tr>
                            <td class="border-top">{{__('Name')}}</td>
                            <td class="border-top">{{ $shop->name }}</td>
                        </tr>
                        <tr>
                            <td>{{__('Estimated Delivery Time')}}</td>
                            <td>{{ $shop->estimated_delivery_time }}</td>
                        </tr>
                    </table>
                </div>

                <div class="ms-lg-4">
                    <h5>{{__('User Information')}}</h5>
                    <table class="table mb-0">
                        <tr>
                            <td class="border-top">{{__('Name')}}</td>
                            <td class="border-top">{{ $shop->user?->name }}</td>
                        </tr>
                        <tr>
                            <td>{{__('Phone number')}}</td>
                            <td>{{ $shop->user?->phone }}</td>
                        </tr>
                        <tr>
                            <td>{{__('Email')}}</td>
                            <td>{{ $shop->user?->email }}</td>
                        </tr>
                    </table>
                </div>

            </div>

            <div class="mt-3">
                <h5>{{__('Shop Description')}}</h5>
                <p>{!! $shop->description !!}</p>
            </div>

            <div class="mt-3">
                <h5>{{__('Business Information')}}</h5>
                @if($businessInfo)
                <table class="table mb-0">
                    <tr>
                        <td>{{__('VAT Number')}}</td>
                        <td>{{ $businessInfo->vat_number }}</td>
                    </tr>
                    <tr>
                        <td>{{__('Business Location')}}</td>
                        <td>{{ $businessInfo->business_location }}</td>
                    </tr>
                    <tr>
                        <td>{{__('Company Name')}}</td>
                        <td>{{ $businessInfo->company_name }}</td>
                    </tr>
                </table>
                @else
                <p>{{__('No business information available')}}</p>
                @endif
            </div>

            <div class="mt-3">
                <h5>{{__('Package Information')}}</h5>
                @if($package)
                <table class="table mb-0">
                    @php
                        // Get all approved package payments that are still valid
                        $approvedPayments = \App\Models\PackagePayment::where('shop_id', $shop->id)
                            ->where('status', 'approved')
                            ->whereNotNull('expires_at')
                            ->where('expires_at', '>=', now())
                            ->with('package')
                            ->get();
                            
                        // Calculate total product limit
                        $totalLimit = $approvedPayments->sum(function($payment) {
                            return $payment->package->product_limit ?? 0;
                        });
                    @endphp

                    <tr>
                        <td>{{__('Current Package')}}</td>
                        <td>{{ $package->package->name ?? 'N/A' }}</td>
                    </tr>
                    
                    <tr>
                        <td>{{__('Active Subscriptions')}}</td>
                        <td>{{ $approvedPayments->count() }}</td>
                    </tr>
                    
                    <tr>
                        <td>{{__('Total Product Limit')}}</td>
                        <td class="fw-bold">{{ $totalLimit }}</td>
                    </tr>
                    
                    <tr>
                        <td>{{__('Products Used')}}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                {{ $totalProducts }} / {{ $totalLimit }}
                                @if($totalProducts >= $totalLimit)
                                    <span class="badge bg-danger">{{ __('Limit Reached') }}</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Active Package Details -->
                    <tr>
                        <td colspan="2" class="bg-light">
                            <h6 class="mb-0 mt-2">{{__('Active Packages')}}</h6>
                        </td>
                    </tr>
                    
                    @foreach($approvedPayments as $payment)
                        <tr>
                            <td>
                                <div>
                                    <strong>{{ $payment->package->name }}</strong> ({{ $payment->package->product_limit }} products)
                                </div>
                                <small class="text-muted">
                                    {{__('Expires')}}: {{ $payment->expires_at->format('Y-m-d') }}
                                </small>
                            </td>
                            <td>{{ showCurrency($payment->amount) }}</td>
                        </tr>
                    @endforeach
                    
                </table>
                @else
                <p>{{__('No package information available')}}</p>
                @endif
            </div>

        </div>
    </div>

    <form action="{{ route('admin.shop.reset.password', $shop->id) }}" method="POST">
        @csrf
        <div class="modal fade" id="ResetPasswordModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title fs-5">{{__('Reset Password')}} ({{ $shop->name }})</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="password1" class="form-label">
                                {{__('Password')}}
                            </label>
                            <div class="position-relative passwordInput">
                                <input type="password" name="password" id="password1" class="form-control" required="true"
                                    placeholder="Enter Password">
                                <span class="eye" onclick="showHidePassword(1)">
                                    <i class="fa fa-eye-slash fa-eye" id="togglePassword1"></i>
                                </span>
                            </div>
                            @error('password')
                                <p class="text text-danger m-0">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password2" class="form-label">
                                {{__('Confirm Password')}}
                            </label>
                            <div class="position-relative passwordInput">
                                <input type="password" name="password_confirmation" id="password2" class="form-control"
                                    required="true" placeholder="Enter Password again">
                                <span class="eye" onclick="showHidePassword(2)">
                                    <i class="fa fa-eye-slash fa-eye" id="togglePassword2"></i>
                                </span>
                            </div>
                            <span id="passwordMatch" class="text text-danger d-none"></span>
                            @error('password_confirmation')
                                <p class="text text-danger m-0">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="submit" class="btn btn-primary">
                            {{__('Save changes')}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@push('scripts')
    <script>
        function showHidePassword(num) {
            const toggle = document.getElementById("togglePassword" + num);
            const password = document.getElementById("password" + num);

            // toggle the type attribute
            const type = password.getAttribute("type") === "password" ? "text" : "password";
            password.setAttribute("type", type);
            // toggle the icon
            toggle.classList.toggle("fa-eye");
        }

        document.getElementById('password2').addEventListener('keyup', function(e) {
            $password1 = document.getElementById('password1').value;
            $password2 = document.getElementById('password2').value;

            $message = document.getElementById('passwordMatch');

            if ($password1 == $password2) {
                document.getElementById('password2').classList.remove('is-invalid');
                $message.classList.add('d-none');
                document.getElementById('submit').disabled = false;
            } else {
                document.getElementById('password2').classList.add('is-invalid');
                $message.classList.remove('d-none');
                $message.innerHTML = "Password doesn't match";
                document.getElementById('submit').disabled = true;
            }
        });
    </script>
@endpush

