<div class="modal fade" id="createPackageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.packages.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Create Package') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Package Name') }}</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Description') }}</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Product Limit') }}</label>
                        <input type="number" name="product_limit" class="form-control" required min="1">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Price') }}</label>
                        <input type="number" name="price" class="form-control" required min="0" step="0.01">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Duration (Days)') }}</label>
                        <input type="number" name="duration_days" class="form-control" required min="1" value="365">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        {{ __('Create Package') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div> 