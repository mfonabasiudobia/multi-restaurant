@extends('layouts.app')
@section('title', __('Weight Based Delivery'))
@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <h3>{{ __('Weight Based Delivery Charges') }}</h3>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addWeightModal">
                    {{ __('Add New') }}
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('Min Weight') }} ({{ $weightUnit->name }})</th>
                            <th>{{ __('Max Weight') }} ({{ $weightUnit->name }})</th>
                            <th>{{ __('Price') }} {{ $generaleSetting->currency ?? '' }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($weights as $weight)
                        <tr>
                            <td>{{ $weight->min_weight }}</td>
                            <td>{{ $weight->max_weight }}</td>
                            <td>{{ $generaleSetting->currency ?? '' }} {{ number_format($weight->price, 2) }}</td>
                            <td>
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" 
                                        {{ $weight->is_active ? 'checked' : '' }}
                                        onchange="toggleStatus({{ $weight->id }})">
                                </div>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="editWeight({{ $weight }})">
                                    {{ __('Edit') }}
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteWeight({{ $weight->id }})">
                                    {{ __('Delete') }}
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    @include('admin.delivery-weight.modals.add')
    
    <!-- Edit Modal -->
    @include('admin.delivery-weight.modals.edit')
@endsection 