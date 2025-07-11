<div class="modal fade" id="editWeightModal" tabindex="-1" aria-labelledby="editWeightModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editWeightModalLabel">{{ __('Edit Weight Range') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editWeightForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Min Weight') }} ({{ $weightUnit->name }})</label>
                        <input type="number" name="min_weight" id="edit_min_weight" class="form-control" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Max Weight') }} ({{ $weightUnit->name }})</label>
                        <input type="number" name="max_weight" id="edit_max_weight" class="form-control" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Delivery Price') }} {{ $generaleSetting->currency ?? '' }}</label>
                        <input type="number" name="price" id="edit_price" class="form-control" step="0.01" min="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function editWeight(weight) {
        const form = document.getElementById('editWeightForm');
        form.action = `/admin/delivery-weight/${weight.id}`;
        
        document.getElementById('edit_min_weight').value = weight.min_weight;
        document.getElementById('edit_max_weight').value = weight.max_weight;
        document.getElementById('edit_price').value = weight.price;
        
        new bootstrap.Modal(document.getElementById('editWeightModal')).show();
    }

    function deleteWeight(id) {
        if (confirm('{{ __("Are you sure you want to delete this weight range?") }}')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/delivery-weight/${id}`;
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }

    function toggleStatus(id) {
        // Add toggle status functionality if needed
    }
</script>
@endpush 