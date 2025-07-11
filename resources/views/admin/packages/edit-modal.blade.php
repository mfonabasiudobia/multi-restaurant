<div class="modal fade" id="editPackageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Edit Package') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editPackageForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Name') }}</label>
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
                        <input type="number" name="duration_days" class="form-control" required min="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Update Package') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editPackage(packageId) {
    // Get package data via AJAX
    axios.get(`/admin/packages/${packageId}/edit`)
        .then(response => {
            const package = response.data.package; // Update to match your API response structure
            
            // Fill the form
            document.getElementById('edit_name').value = package.name;
            document.getElementById('edit_description').value = package.description;
            document.getElementById('edit_product_limit').value = package.product_limit;
            document.getElementById('edit_price').value = package.price;
            document.getElementById('edit_duration_days').value = package.duration_days;
            
            // Set form action
            document.getElementById('editPackageForm').action = `/admin/packages/${packageId}`;
            
            // Show modal
            new bootstrap.Modal(document.getElementById('editPackageModal')).show();
        })
        .catch(error => {
            console.error('Error fetching package:', error);
            alert('Error fetching package details');
        });
}

// Add event listener for form submission
document.getElementById('editPackageForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    
    axios.post(form.action, formData)
        .then(response => {
            window.location.reload();
        })
        .catch(error => {
            console.error('Error updating package:', error);
            alert('Error updating package');
        });
});
</script> 