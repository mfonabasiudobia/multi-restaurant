@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <!-- Left Side - Package Selection -->
        <div class="col-md-8">
            <!-- Current Package Info -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Current Package</h5>
                </div>
                <div class="card-body">
                    @if($currentPackage)
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Package:</strong> {{ $currentPackage->package->name }}</p>
                                <p><strong>Products:</strong> {{ $currentPackage->product_limit }}</p>
                            </div>
                            <div class="col-md-6">
                                <p>
                                    <strong>Status:</strong>
                                    <span class="badge bg-{{ $currentPackage->is_paid ? 'success' : 'warning' }}">
                                        {{ $currentPackage->is_paid ? 'Active' : 'Pending Payment' }}
                                    </span>
                                </p>
                                <p><strong>Expires:</strong> {{ $currentPackage->expires_at ? $currentPackage->expires_at->format('Y-m-d') : 'N/A' }}</p>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            You don't have any active package. Please select a package below.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Package Selection -->
            <div class="row mb-4">
                @foreach($packages as $package)
                <div class="col-md-4 mb-3">
                    <div class="card h-100 package-card {{ $package->is_popular ? 'border-primary' : '' }}" 
                         data-package-id="{{ $package->id }}"
                         data-package-price="{{ $package->price }}">
                        @if($package->is_popular)
                        <div class="card-header text-center text-white bg-primary">
                            Popular Choice
                        </div>
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $package->name }}</h5>
                            <h6 class="card-subtitle mb-3 text-primary">${{ $package->price }}</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success me-2"></i>{{ $package->product_limit }} Products</li>
                                <li><i class="fas fa-check text-success me-2"></i>{{ $package->duration_days }} Days</li>
                                @if($package->features)
                                    @foreach($package->features as $feature)
                                    <li><i class="fas fa-check text-success me-2"></i>{{ $feature }}</li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                        <div class="card-footer text-center bg-light">
                            <button class="btn btn-primary select-package w-100">
                                Select Plan
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Right Side - Payment Form -->
        <div class="col-md-4">
            <div id="paymentForm" class="card mb-4 d-none">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Payment Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('shop.package.process-payment') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="package_id" id="selectedPackageId">
                        
                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <select name="payment_method" class="form-control" id="paymentMethod" required>
                                @if($bankDetails)
                                    <option value="BANK" selected>Bank Transfer</option>
                                @endif
                                @foreach($paymentGateways as $gateway)
                                    <option value="{{ strtoupper($gateway->alias) }}">{{ $gateway->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        @if($bankDetails)
                            <div id="bankDetails" class="alert alert-info mb-3">
                                <h6 class="alert-heading">Bank Transfer Details</h6>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Bank:</strong> {{ $bankDetails->bank_name }}</p>
                                        <p><strong>Account Name:</strong> {{ $bankDetails->company_name }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>IBAN:</strong> {{ $bankDetails->iban }}</p>
                                        <p><strong>SWIFT/BIC:</strong> {{ $bankDetails->swift_bic }}</p>
                                    </div>
                                </div>
                                
                                <div class="mt-3">
                                    <label class="form-label">Upload Payment Proof</label>
                                    <input type="file" name="payment_proof" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="text-muted">Accepted formats: PDF, JPG, PNG. Max size: 2MB</small>
                                </div>
                            </div>
                        @else
                            <div id="bankDetails" class="alert alert-warning mb-3 d-none">
                                <p class="mb-0">Bank transfer details are not configured. Please choose another payment method.</p>
                            </div>
                        @endif

                        <button type="submit" class="btn btn-primary w-100">Process Payment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment History at the bottom -->
    <div class="card mt-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Payment History</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Package</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Proof</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($paymentHistory as $payment)
                        <tr>
                            <td>{{ $payment->created_at->format('Y-m-d') }}</td>
                            <td>{{ $payment->package->name }}</td>
                            <td>${{ $payment->amount }}</td>
                            <td>{{ $payment->payment_method }}</td>
                            <td>
                                <span class="badge bg-{{ $payment->status === 'approved' ? 'success' : ($payment->status === 'rejected' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td>
                                @if($payment->payment && $payment->payment->paymentProof)
                                    <a href="{{ Storage::url($payment->payment->paymentProof->file_path) }}" 
                                       target="_blank" 
                                       class="btn btn-sm btn-info">
                                        <i class="fas fa-file-alt"></i> View Proof
                                    </a>
                                @elseif($payment->payment && $payment->payment->proof)
                                    <a href="{{ Storage::url($payment->payment->proof->src) }}" 
                                       target="_blank" 
                                       class="btn btn-sm btn-info">
                                        <i class="fas fa-file-alt"></i> View Legacy Proof
                                    </a>
                                @else
                                    <span class="text-muted">No proof</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('styles')
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
    border: 2px solid #0d6efd;
    box-shadow: 0 4px 15px rgba(13,110,253,0.2);
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Only show bank details by default if bank option is selected
    const defaultMethod = $('#paymentMethod').val();
    if (defaultMethod === 'BANK') {
        $('#bankDetails').removeClass('d-none');
    }

    $('.package-card').click(function() {
        $('.package-card').removeClass('selected');
        $(this).addClass('selected');
        
        const packageId = $(this).data('package-id');
        $('#selectedPackageId').val(packageId);
        $('#paymentForm').removeClass('d-none');
    });

    $('#paymentMethod').change(function() {
        const method = $(this).val().toUpperCase();
        if (method === 'BANK' && @json($bankDetails !== null)) {
            $('#bankDetails').removeClass('d-none');
        } else {
            $('#bankDetails').addClass('d-none');
        }
    });

    $('form').on('submit', function(e) {
        const paymentMethod = $('#paymentMethod').val().toUpperCase();
        
        if (paymentMethod === 'STRIPE') {
            e.preventDefault();
            
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log('Stripe response:', response);
                    if (response.url) {
                        window.location.href = response.url;
                    }
                },
                error: function(xhr) {
                    console.error('Stripe error:', xhr);
                    alert('Payment initialization failed: ' + (xhr.responseJSON?.error || 'Please try again.'));
                }
            });
        }
    });
});
</script>
@endpush
@endsection 