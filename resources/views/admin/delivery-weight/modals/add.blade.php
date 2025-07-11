<div class="modal fade" id="addWeightModal" tabindex="-1" aria-labelledby="addWeightModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addWeightModalLabel">{{ __('Add Weight Range') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.delivery-weight.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Min Weight') }} ({{ $weightUnit->name }})</label>
                        <input type="number" name="min_weight" class="form-control" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Max Weight') }} ({{ $weightUnit->name }})</label>
                        <input type="number" name="max_weight" class="form-control" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Delivery Price') }} {{ $generaleSetting->currency ?? '' }}</label>
                        <input type="number" name="price" class="form-control" step="0.01" min="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div> 