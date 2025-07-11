@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h4>Package Payments</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Shop</th>
                        <th>Package</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Proof</th>
                        <th>Status</th>
                        <th>Expires At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($packagePayments as $packagePayment)
                    <tr>
                        <td>{{ $packagePayment->payment->id }}</td>
                        <td>{{ $packagePayment->shop->name ?? 'N/A' }}</td>
                        <td>{{ $packagePayment->package->name ?? 'N/A' }}</td>
                        <td>${{ $packagePayment->amount }}</td>
                        <td>{{ $packagePayment->payment->payment_method }}</td>
                        <td>
                            @if($packagePayment->payment && $packagePayment->payment->paymentProof)
                                <a href="{{ Storage::url($packagePayment->payment->paymentProof->file_path) }}" 
                                   target="_blank" 
                                   class="btn btn-sm btn-info">
                                    <i class="fas fa-file-alt"></i> View Proof
                                </a>
                            @elseif($packagePayment->payment && $packagePayment->payment->proof)
                                <a href="{{ Storage::url($packagePayment->payment->proof->src) }}" 
                                   target="_blank" 
                                   class="btn btn-sm btn-info">
                                    <i class="fas fa-file-alt"></i> View Legacy Proof
                                </a>
                            @else
                                <span class="text-muted">No proof</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $packagePayment->status === 'approved' ? 'success' : ($packagePayment->status === 'rejected' ? 'danger' : 'warning') }}">
                                {{ ucfirst($packagePayment->status) }}
                            </span>
                        </td>
                        <td>{{ $packagePayment->expires_at ? $packagePayment->expires_at->format('Y-m-d') : 'N/A' }}</td>
                        <td>
                            @if($packagePayment->status === 'pending')
                            <form action="{{ route('admin.package.payment.approve', $packagePayment->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">Approve</button>
                            </form>
                            <form action="{{ route('admin.package.payment.reject', $packagePayment->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $packagePayments->links() }}
        </div>
    </div>
</div>
@endsection 