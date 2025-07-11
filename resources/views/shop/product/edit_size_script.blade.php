@push('scripts')
<script>
jQuery(document).ready(function() {
    // Show/hide sizeBox and populate table on change
    jQuery('select[name="sizeIds[]"]').on('change', function() {
        var productPrice = jQuery('#price').val() ?? 0;
        var productDiscountPrice = jQuery('#discount_price').val() ?? 0;
        var mainPrice = productDiscountPrice > 0 ? productDiscountPrice : productPrice;
        var selectedOptions = jQuery(this).find(':selected');
        if (selectedOptions.length > 0) {
            jQuery('#sizeBox').show();
        } else {
            jQuery('#sizeBox').hide();
        }
        jQuery('#selectedSizesTableBody').empty();
        selectedOptions.each(function() {
            var sizeName = jQuery(this).data('size');
            var sizeId = jQuery(this).val();
            var existingPrice = jQuery(this).data('price') || 0;
            jQuery('#selectedSizesTableBody').append(`
                <tr id="size_row_${sizeId}" style="display: table-row !important">
                    <td>
                        <h4 class="mb-0 boxName">${sizeName}</h4>
                        <input type="hidden" name="size[${sizeId}][name]" value="${sizeName}">
                        <input type="hidden" name="size[${sizeId}][id]" value="${sizeId}">
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bolder mainProductPrice">${mainPrice}</span>
                            <span class="bg-light px-2 py-1 rounded"><i class="fa-solid fa-plus"></i></span>
                            <input type="text" class="form-control extraPriceForm" name="size[${sizeId}][price]" value="${existingPrice}" style="width: 140px" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/^(\\d*\\.\\d{0,2}|\\d*)$/, '$1');">
                        </div>
                    </td>
                    <td>
                        <button class="btn circleIcon btn-outline-danger btn-sm" type="button" onclick="deleteSizeRow(${sizeId})">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </td>
                </tr>
            `);
        });
    });
    // Initialize on page load
    function initializeExistingSizes() {
        var productPrice = parseFloat(jQuery('#price').val()) || 0;
        var productDiscountPrice = parseFloat(jQuery('#discount_price').val()) || 0;
        var mainPrice = productDiscountPrice > 0 ? productDiscountPrice : productPrice;
        var selectedOptions = jQuery('select[name="sizeIds[]"] option:selected');
        if (selectedOptions.length > 0) {
            jQuery('#sizeBox').show();
        } else {
            jQuery('#sizeBox').hide();
        }
        jQuery('#selectedSizesTableBody').empty();
        selectedOptions.each(function() {
            var sizeId = jQuery(this).val();
            var sizeName = jQuery(this).text();
            var existingPrice = jQuery(this).data('price') || 0;
            jQuery('#selectedSizesTableBody').append(`
                <tr id="size_row_${sizeId}" style="display: table-row !important">
                    <td>
                        <h4 class="mb-0 boxName">${sizeName}</h4>
                        <input type="hidden" name="size[${sizeId}][name]" value="${sizeName}">
                        <input type="hidden" name="size[${sizeId}][id]" value="${sizeId}">
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bolder mainProductPrice">${mainPrice}</span>
                            <span class="bg-light px-2 py-1 rounded"><i class="fa-solid fa-plus"></i></span>
                            <input type="text" class="form-control extraPriceForm" name="size[${sizeId}][price]" value="${existingPrice}" style="width: 140px" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/^(\\d*\\.\\d{0,2}|\\d*)$/, '$1');">
                        </div>
                    </td>
                    <td>
                        <button class="btn circleIcon btn-outline-danger btn-sm" type="button" onclick="deleteSizeRow(${sizeId})">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </td>
                </tr>
            `);
        });
    }
    initializeExistingSizes();
    // Price update events
    jQuery('#price, #discount_price').on('input', function() {
        var productPrice = parseFloat(jQuery('#price').val()) || 0;
        var productDiscountPrice = parseFloat(jQuery('#discount_price').val()) || 0;
        var mainPrice = productDiscountPrice > 0 ? productDiscountPrice : productPrice;
        jQuery('.mainProductPrice').text(mainPrice);
    });
    window.deleteSizeRow = function(sizeId) {
        jQuery(`#size_row_${sizeId}`).remove();
        jQuery(`select[name="sizeIds[]"] option[value="${sizeId}"]`).prop('selected', false);
        jQuery('select[name="sizeIds[]"]').trigger('change');
    };
});
</script>
@endpush