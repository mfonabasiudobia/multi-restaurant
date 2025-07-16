@extends('layouts.app')

@section('content')
<div class="page-title">
    <div class="d-flex gap-2 align-items-center">
        <i class="fa-solid fa-shop"></i> {{ __('Add New Product') }}
    </div>
</div>
{{-- Display Validation Errors --}}
@if ($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong><i class="fa fa-exclamation-triangle me-2"></i>{{ __('There were some problems with your input:')
        }}</strong>
    <ul class="mb-0 mt-2">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<form action="{{ route('admin.product.store') }}" method="POST" enctype="multipart/form-data" id="product_loader_form">
    @csrf
    @role('root')
    <div class="card mt-3">
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">
                    {{ __('Select Shop') }}
                    <span class="text-danger">*</span>
                </label>
                <select name="shop_id" class="form-control select2" style="width: 100%">
                    <option value="">{{ __('Select Shop') }}</option>
                    @foreach ($shops as $shop)
                    <option value="{{ $shop->id }}" {{ old('shop_id')==$shop->id ? 'selected' : '' }}>
                        {{ $shop->name }}
                    </option>
                    @endforeach
                </select>
                @error('shop_id')
                <p class="text text-danger m-0">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
    @endrole

    <div class="card mt-3">
        <div class="card-body">

            <div class="">
                <x-input label="Product Name" name="name" type="text" placeholder="Enter Product Name"
                    required="true" />
            </div>

            <div class="form-group">
                <label for="shortDescription">{{ __('Short Description') }}</label>
                <textarea name="shortDescription" id="shortDescription"
                    class="form-control">{{ old('shortDescription') }}</textarea>
            </div>

            <div class="form-group">
                <label for="description">{{ __('Description') }}</label>
                <textarea name="description" id="description" class="form-control">{{ old('description') }}</textarea>
            </div>
        </div>
    </div>

    <!--######## General Information ##########-->
    <div class="card mt-4">
        <div class="card-body">
            <div class="d-flex gap-2 border-bottom pb-2">
                <i class="fa-solid fa-box"></i>
                <h5>{{ __('Product Information') }}</h5>
            </div>

            <!-- Logistics Information -->
            <div class="row mt-3">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="bag_number">{{ __('Bag Number') }}</label>
                        <input type="text" name="bag_number" id="bag_number"
                            class="form-control @error('bag_number') is-invalid @enderror"
                            value="{{ old('bag_number') }}">
                        @error('bag_number')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="location">{{ __('Location (Box)') }}</label>
                        <input type="text" name="location" id="location"
                            class="form-control @error('location') is-invalid @enderror" value="{{ old('location') }}">
                        @error('location')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="row">{{ __('Row') }}</label>
                        <input type="number" name="row" id="row" class="form-control @error('row') is-invalid @enderror"
                            value="{{ old('row') }}">
                        @error('row')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Category, Subcategory, Brand -->
            <div class="row mt-3">
                <div class="col-md-4">
                    <label class="form-label">
                        {{ __('Select Category') }}
                        <span class="text-danger">*</span>
                    </label>
                    <select name="category" class="form-control select2" style="width: 100%">
                        <option value="" selected disabled>{{ __('Select Category') }}</option>
                        @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category')
                    <p class="text text-danger m-0">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">{{ __('Select Sub Categories') }}</label>
                    <select name="sub_category[]" data-placeholder="Select Sub Category" class="form-control select2"
                        multiple style="width: 100%">
                        <option value="" disabled>{{ __('Select Sub Category') }}</option>
                    </select>
                    @error('sub_category')
                    <p class="text text-danger m-0">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Season, Quality, Unit -->
            <div class="row mt-3">
                <div class="col-md-4">
                    <label for="season_id" class="form-label">
                        {{ __('Season') }}
                        <span class="text-danger">*</span>
                    </label>
                    <select name="season_id" id="season_id"
                        class="form-select select2 @error('season_id') is-invalid @enderror" required>
                        <option value="">{{ __('Select Season') }}</option>
                        @foreach($seasons as $season)
                        <option value="{{ $season->id }}" {{ old('season_id')==$season->id ? 'selected' : '' }}>
                            {{ $season->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('season_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="quality_id" class="form-label">
                        {{ __('Quality') }}
                        <span class="text-danger">*</span>
                    </label>
                    <select name="quality_id" id="quality_id"
                        class="form-select select2 @error('quality_id') is-invalid @enderror" required>
                        <option value="">{{ __('Select Quality') }}</option>
                        @foreach($qualities as $quality)
                        <option value="{{ $quality->id }}" {{ old('quality_id')==$quality->id ? 'selected' : '' }}>
                            {{ $quality->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('quality_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="unit_id" class="form-label">
                        {{ __('Unit') }}
                        <span class="text-danger">*</span>
                    </label>
                    <select name="unit_id" id="unit_id"
                        class="form-select select2 @error('unit_id') is-invalid @enderror" required>
                        <option value="">{{ __('Select Unit') }}</option>
                        @foreach($units as $unit)
                        <option value="{{ $unit->id }}" {{ old('unit_id')==$unit->id ? 'selected' : '' }}>
                            {{ $unit->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('unit_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Colors, Sizes, SKU -->
            <div class="row mt-3">
                <div class="col-md-4">
                    <label for="size" class="form-label">
                        {{ __('Select Size') }}
                        <span class="text-danger">*</span>
                    </label>
                    <select name="sizeIds[]" id="size" data-placeholder="Select Size" class="form-select select2"
                        multiple style="width: 100%" required>
                        <option value="">{{ __('Select Size') }}</option>
                        @foreach ($sizes as $size)
                        <option value="{{ $size->id }}" data-size="{{ $size->name }}" {{ (old('sizeIds') &&
                            in_array($size->id, old('sizeIds'))) ? 'selected' : '' }}>
                            {{ $size->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('sizeIds')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label d-flex align-items-center gap-2 justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span>
                                {{ __('Product SKU') }}
                                <span class="text-danger">*</span>
                            </span>
                            <span class="info" data-bs-toggle="tooltip" data-bs-placement="top"
                                data-bs-title="{{ __('Create a unique product code. This will be used generate barcode') }}">
                                <i class="bi bi-info"></i>
                            </span>
                        </div>
                        <span class="text-primary cursor-pointer" onclick="generateCode()">
                            {{ __('Generate Code') }}
                        </span>
                    </label>
                    <input type="text" id="barcode" name="code" placeholder="Ex: 134543" class="form-control"
                        value="{{ old('code') }}"
                        oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" />
                    @error('code')
                    <p class="text text-danger m-0">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!--######## Price Information ##########-->
    <div class="card mt-4 mb-4">
        <div class="card-body">
            <div class="d-flex gap-2 border-bottom pb-2">
                <i class="fa-solid fa-user"></i>
                <h5 class="mb-0">
                    {{ __('Price Information') }}
                </h5>
            </div>
            <div class="row mt-3">
                <div class="col-lg-4 col-md-6 mb-3">
                    <x-input type="text" name="price" label="Selling Price" placeholder="Selling Price" required="true"
                        onlyNumber="true" value="10" />
                </div>

                <div class="col-lg-4 col-md-6 mb-3">
                    <x-input type="text" name="discount_price" label="Discount Price" placeholder="Discount Price"
                        onlyNumber="true" value="0" />
                </div>

                <div class="col-lg-4 col-md-6 mb-3">
                    <x-input type="text" name="quantity" label="Current Stock Quantity"
                        placeholder="Current Stock Quantity" onlyNumber="true" required="true" />
                </div>

                <div class="col-md-6 mb-3">
                    <x-select name="taxs[]" label="Vat & Tax" placeholder="Select Vat & Tax" multiselect="true">
                        @foreach ($taxs as $tax)
                        <option value="{{ $tax->id }}">{{ $tax->name }} ({{ $tax->percentage }}%)</option>
                        @endforeach
                    </x-select>
                </div>

                <div class="col-lg-4 col-md-6 mb-3">
                    <x-input type="text" onlyNumber="true" name="min_order_quantity" label="Minimum Order Quantity"
                        placeholder="Minimum Order Quantity" value="1" />
                </div>
            </div>

            <!--######## color wise price table ##########-->
            <div class="border rounded p-0 position-relative overflow-hidden mt-4" id="colorBox" style="display: none">
                <p class="fw-bolder box-title mb-2">
                    {{ __('Color wise extra price') }}
                </p>
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Extra Price') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody id="selectedColorsTableBody"></tbody>
                </table>
            </div>

            <!--######## Size wise price table ##########-->
            <div class="border rounded p-0 position-relative overflow-hidden mt-4" id="sizeBox" style="display: none">
                <p class="fw-bold box-title mb-2">
                    {{ __('Size wise extra price') }}
                </p>
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('Size') }}</th>
                            <th>{{ __('Extra Price') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody id="selectedSizesTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!--######## Thumbnail Information ##########-->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card h-100">
                <div class="card-body">
                    <div class="mb-2">
                        <h5>
                            {{ __('Product Images') }}
                            <span class="text-primary">(Ratio 1:1 (500 x 500 px))</span>
                            <span class="text-danger">*</span>
                        </h5>
                        @error('additionThumbnail')
                        <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="d-flex flex-wrap gap-3" id="additionalElements">
                        <div id="addition">
                            <label for="additionThumbnail1" class="additionThumbnail">
                                <img src="https://placehold.co/500x500/f1f5f9/png" id="preview2" alt="" width="100%"
                                    height="100%">
                                <button onclick="removeThumbnail('addition')" id="removeThumbnail1" type="button"
                                    class="delete btn btn-sm btn-outline-danger" style="display: none"><i
                                        class="fa fa-trash"></i></button>
                            </label>
                            <input id="additionThumbnail1" accept="image/*" type="file" name="additionThumbnail[]"
                                class="d-none" onchange="previewAdditionalFile(event, 'preview2', 'removeThumbnail1')">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--######## Video Upload Section ##########-->
    <div class="card mt-4">
        <div class="card-body">
            <div class="d-flex gap-2 border-bottom pb-2">
                <i class="fa-solid fa-video"></i>
                <h5>
                    {{ __('Video Upload') }}
                </h5>
            </div>
            <div class="mt-3">
                <label for="video" class="form-label">
                    {{ __('Upload Product Videos') }}
                    <span class="text-primary">(Optional, multiple allowed)</span>
                </label>
                <input id="video" accept="video/*" type="file" name="videos[]" multiple class="form-control">
                @error('videos')
                <p class="text text-danger m-0">{{ $message }}</p>
                @enderror
            </div>
            <div class="mt-3" id="videoPreviewContainer"></div>
        </div>
    </div>
    <div class="d-flex gap-3 justify-content-end align-items-center mb-3">
        <button type="reset" class="btn btn-lg btn-outline-secondary rounded py-2">
            {{ __('Reset') }}
        </button>
        <button type="submit" class="btn btn-lg btn-primary rounded py-2 px-5">
            {{ __('Submit') }}
        </button>
    </div>

</form>
@endsection
@push('css')
<style>
    .box-title {
        background: #f1f5f9;
        padding: 6px 10px;
        font-size: 18px;
    }

    #colorBox,
    #sizeBox {
        margin-top: 20px;
    }

    .boxName {
        font-size: 16px;
        margin-bottom: 0;
    }

    .extraPriceForm {
        padding: 4px 6px;
        min-height: 34px;
    }

    #selectedSizesTableBody tr:last-child td,
    #selectedColorsTableBody tr:last-child td {
        border: 0 !important;
    }

    /* TinyMCE custom styles */
    .tox-tinymce {
        border: 1px solid #ced4da !important;
        border-radius: 4px !important;
    }
</style>
@endpush
@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize Select2 for all dropdowns
        $('.select2').select2({
            width: '100%',
            allowClear: true
        });

        // Log selected sizes for debugging
        $('select[name="sizeIds[]"]').on('change', function() {
            console.log('Selected sizes:', $(this).val());
        });

        $('#price').on('input', function() {
            var productPrice = $(this).val() ?? 0;
            var productDiscountPrice = $('#discount_price').val() ?? 0;
            var mainPrice = productDiscountPrice > 0 ? productDiscountPrice : productPrice;
            $('.mainProductPrice').text(mainPrice);
        });

        $('#discount_price').on('input', function() {
            var productPrice = $('#price').val() ?? 0;
            var productDiscountPrice = $(this).val() ?? 0;
            var mainPrice = productDiscountPrice > 0 ? productDiscountPrice : productPrice;
            $('.mainProductPrice').text(mainPrice);
        });

        // Add color wise extra price
        $('.colorSelect').on('change', function() {
            var selectedOptions = $(this).find(':selected');

            if (selectedOptions.length > 0) {
                $('#colorBox').show();
            } else {
                $('#colorBox').hide();
            }

            var productPrice = $('#price').val() ?? 0;
            var productDiscountPrice = $('#discount_price').val() ?? 0;
            var mainPrice = productDiscountPrice > 0 ? productDiscountPrice : productPrice;

            selectedOptions.each(function() {
                var colorName = $(this).data('name');
                var colorCode = $(this).data('color');
                var colorId = $(this).val();

                // Check if the row already exists
                if (!$(`#selectedColorRow_${colorId}`).length) {
                    $('#selectedColorsTableBody').append(`
                            <tr id="selectedColorRow_${colorId}" style="display: table-row !important">
                                <td>
                                    <h4 class="mb-0 boxName d-flex align-items-center gap-1">
                                        <span style="background-color:${colorCode};width:20px;height:19px;display:inline-block; border-radius:5px;"></span>
                                        ${colorName}
                                    </h4>
                                    <input type="hidden" name="color[${colorId}][name]" value="${colorName}">
                                    <input type="hidden" name="color[${colorId}][id]" value="${colorId}">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fw-bolder mainProductPrice">${mainPrice}</span>
                                        <span class="bg-light px-2 py-1 rounded">
                                            <i class="fa-solid fa-plus"></i>
                                        </span>
                                        <input type="text" class="form-control extraPriceForm" name="color[${colorId}][price]" value="0" style="width: 140px" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/^(\d*\.\d{0,2}|\d*)$/, '$1');">
                                    </div>
                                </td>
                                <td>
                                    <button class="btn circleIcon btn-outline-danger btn-sm" type="button"
                                        onclick="deleteColorRow(${colorId})">
                                        <i class="fa-solid fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                        `);
                }
            });

            // Remove the row from the table
            $(this).find(':not(:selected)').each(function() {
                var colorId = $(this).val();
                $(`#selectedColorRow_${colorId}`).remove();
            });
        });

        $('select[name="category"]').on('change', function() {
            var categoryId = $(this).val();

            if (categoryId) {
                $.ajax({
                    url: '/api/sub-categories?category_id=' + categoryId,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        var subCategorySelect = $('select[name="sub_category[]"]');
                        subCategorySelect.empty();

                        $.each(data.data.sub_categories, function(key, value) {
                            subCategorySelect.append('<option value="' + value.id +
                                '">' + value.name + '</option>');
                        });
                        subCategorySelect.trigger('change');
                    },
                    error: function() {
                        alert('Error retrieving subcategories. Please try again.');
                    }
                });
            } else {
                $('select[name="subCategory[]"]').empty();
            }
        });

        // form submit loader
        $('form').on('submit', function() {
            var submitButton = $(this).find('button[type="submit"]');

            submitButton.prop('disabled', true);
            submitButton.removeClass('px-5');

            submitButton.html(`<div class="d-flex align-items-center gap-1">
                    <div class="spinner-border" role="status"></div>
                    <span>Submitting...</span>
                </div>`)
        });
    });

    // remove color from price section
    function deleteColorRow(id) {
        $(`#selectedColorRow_${id}`).remove();

        $('.colorSelect option').each(function() {
            if ($(this).val() == id) {
                $(this).prop('selected', false);
            }
        });
        $('.colorSelect').trigger('change');
    }
</script>

<script>
    $(document).ready(function() {
        // Size dropdown change event
        $('select[name="sizeIds[]"]').on('change', function() {
            var productPrice = $('#price').val() ?? 0;
            var productDiscountPrice = $('#discount_price').val() ?? 0;
            var mainPrice = productDiscountPrice > 0 ? productDiscountPrice : productPrice;

            // Get the selected options
            var selectedOptions = $(this).find(':selected');

            // Check if there are selected options
            if (selectedOptions.length > 0) {
                $('#sizeBox').show();
            } else {
                $('#sizeBox').hide();
            }

            selectedOptions.each(function() {
                var sizeName = $(this).data('size');
                var sizeId = $(this).val();

                // Check if the row already exists
                if (!$(`#selectedSizeRow_${sizeId}`).length) {
                    $('#selectedSizesTableBody').append(`
                        <tr id="selectedSizeRow_${sizeId}" style="display: table-row !important">
                            <td>
                                <h4 class="mb-0 boxName">${sizeName}</h4>
                                <input type="hidden" name="size[${sizeId}][name]" value="${sizeName}">
                                <input type="hidden" name="size[${sizeId}][id]" value="${sizeId}">
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-bolder mainProductPrice">${mainPrice}</span>
                                    <span class="bg-light px-2 py-1 rounded">
                                        <i class="fa-solid fa-plus"></i>
                                    </span>
                                    <input type="text" class="form-control extraPriceForm" name="size[${sizeId}][price]" value="0" style="width: 140px" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/^(\d*\.\d{0,2}|\d*)$/, '$1');">
                                </div>
                            </td>
                            <td>
                                <button class="btn circleIcon btn-outline-danger btn-sm" type="button"
                                    onclick="deleteSizeRow(${sizeId})">
                                    <i class="fa-solid fa-times"></i>
                                </button>
                            </td>
                        </tr>
                    `);
                }
            });

            $(this).find(':not(:selected)').each(function() {
                var sizeId = $(this).val();
                $(`#selectedSizeRow_${sizeId}`).remove();
            });
        });
    });

    // remove size from price section
    function deleteSizeRow(id) {
        $(`#selectedSizeRow_${id}`).remove();

        $('select[name="sizeIds[]"] option').each(function() {
            if ($(this).val() == id) {
                $(this).prop('selected', false);
            }
        });
        $('select[name="sizeIds[]"]').trigger('change');
    }
</script>

<!-- additional thumbnail script -->
<script>
    var thumbnailCount = 1;

    const previewAdditionalFile = (event, id, removeId) => {
        var reader = new FileReader();
        reader.onload = function() {
            var output = document.getElementById(id);
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);

        // increment count
        thumbnailCount++;

        document.getElementById(removeId).style.display = 'block';

        // Create a new box dynamically
        const newThumbnailId = `additionThumbnail${thumbnailCount + 1}`;
        const newPreviewId = `preview${thumbnailCount + 1}`;
        const mainId = 'addition' + thumbnailCount + 1;

        // Add the new box
        const newThumbnailBox = document.createElement('div');
        newThumbnailBox.id = mainId;

        newThumbnailBox.innerHTML = `
            <label for="${newThumbnailId}" class="additionThumbnail">
                <img src="{{ asset('default/upload.png') }}" id="${newPreviewId}" alt="" width="100%" height="100%">
                <button onclick="removeThumbnail('${mainId}')" type="button" id="removeThumbnail${thumbnailCount + 1}" class="delete btn btn-sm btn-outline-danger" style="display: none"><i class="fa fa-trash"></i></button>
                <input id="${newThumbnailId}" accept="image/*" type="file" name="additionThumbnail[]" class="d-none" onchange="previewAdditionalFile(event, '${newPreviewId}', 'removeThumbnail${thumbnailCount +1 }')">
            </label>
        `;

        document.getElementById('additionalElements').appendChild(newThumbnailBox);

        // get current file
        var inputElement = event.target;
        var newOnchangeFunction = `previewFile(event, '${id}')`;
        // Set the new onchange attribute
        inputElement.setAttribute("onchange", newOnchangeFunction);

    }

    const removeThumbnail = (thumbnailId) => {
        const thumbnailToRemove = document.getElementById(thumbnailId);
        if (thumbnailToRemove) {
            thumbnailToRemove.parentNode.removeChild(thumbnailToRemove);
        }
    }

    const generateCode = () => {
        const code = document.getElementById('barcode');
        code.value = Math.floor(Math.random() * 900000) + 100000;
    }
</script>

<!-- color select2 script -->
<script>
    function formatState(state) {
        if (!state.id) {
            return state.text;
        }
        var $state = $(
            '<span class="d-flex align-items-center"> <span style="background-color:' + state.element.dataset
            .color +
            ';width:20px;height:20px;display:inline-block; border-radius:5px;margin-right:5px;"></span>' + state
            .text + '</span>'
        );
        return $state;
    };

    $(document).ready(function() {
        $('.colorSelect').select2({
            templateResult: formatState
        });
    });
</script>

<script>
    const quill = new Quill('#editor', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{
                    'header': [1, 2, 3, 4, 5, 6, false]
                }],
                [{
                    'font': []
                }],
                ['bold', 'italic', 'underline', 'strike', 'blockquote'],
                [{
                    'list': 'ordered'
                }, {
                    'list': 'bullet'
                }],
                [{
                    'align': []
                }],
                [{
                    'script': 'sub'
                }, {
                    'script': 'super'
                }],
                [{
                    'indent': '-1'
                }, {
                    'indent': '+1'
                }],
                [{
                    'direction': 'rtl'
                }],
                [{
                    'color': []
                }, {
                    'background': []
                }],
                ['link', 'image', 'video', 'formula']
            ]
        }
    });

    quill.on('text-change', function(delta, oldDelta, source) {
        document.getElementById('description').value = quill.root.innerHTML;
    });
</script>

<!-- video preview script -->
<script>
    document.getElementById('video').addEventListener('change', function(event) {
        const videoPreviewContainer = document.getElementById('videoPreviewContainer');
        videoPreviewContainer.innerHTML = ''; // Clear existing previews

        Array.from(event.target.files).forEach((file, index) => {
            const fileReader = new FileReader();

            fileReader.onload = function(e) {
                const videoWrapper = document.createElement('div');
                videoWrapper.classList.add('mb-3');

                const videoElement = document.createElement('video');
                videoElement.src = e.target.result;
                videoElement.controls = true;
                videoElement.width = 300;

                const removeButton = document.createElement('button');
                removeButton.type = 'button';
                removeButton.className = 'btn btn-sm btn-danger mt-1';
                removeButton.textContent = 'Remove';
                removeButton.onclick = () => {
                    videoWrapper.remove();
                    // Remove the file from the input
                    const dataTransfer = new DataTransfer();
                    const input = document.getElementById('video');
                    Array.from(input.files).forEach((f, i) => {
                        if (i !== index) {
                            dataTransfer.items.add(f);
                        }
                    });
                    input.files = dataTransfer.files;
                };

                videoWrapper.appendChild(videoElement);
                videoWrapper.appendChild(removeButton);
                videoPreviewContainer.appendChild(videoWrapper);
            };

            fileReader.readAsDataURL(file);
        });
    });
</script>

<!-- TinyMCE -->
<script src="https://cdn.tiny.cloud/1/404ticmbmer7eyfwwt48gxb8isw74kc374o8ae3p06vqow2v/tinymce/7/tinymce.min.js"
    referrerpolicy="origin"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize TinyMCE
        tinymce.init({
            selector: 'textarea',
            plugins: [
                'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'image', 'link', 'lists', 'media', 'searchreplace', 'table', 'visualblocks', 'wordcount',
                'checklist', 'mediaembed', 'casechange', 'export', 'formatpainter', 'pageembed', 'a11ychecker', 'tinymcespellchecker', 'permanentpen', 'powerpaste', 'advtable', 'advcode', 'editimage', 'advtemplate', 'ai', 'mentions', 'tinycomments', 'tableofcontents', 'footnotes', 'mergetags', 'autocorrect', 'typography', 'inlinecss', 'markdown', 'importword', 'exportword', 'exportpdf'
            ],
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
            tinycomments_mode: 'embedded',
            tinycomments_author: 'Author name',
            mergetags_list: [{
                    value: 'First.Name',
                    title: 'First Name'
                },
                {
                    value: 'Email',
                    title: 'Email'
                },
            ],
            ai_request: (request, respondWith) => respondWith.string(() => Promise.reject('See docs to implement AI Assistant')),
        });

        // Initialize Select2 for all dropdowns
        if ($.fn.select2) {
            $('.select2').select2({
                width: '100%',
                allowClear: true
            });

            // Special initialization for color select
            $('.colorSelect').select2({
                width: '100%',
                allowClear: true,
                closeOnSelect: false,
                templateResult: formatColorOption,
                templateSelection: formatColorOption
            });

            // Special initialization for size select
            $('.sizeSelector').select2({
                width: '100%',
                allowClear: true,
                closeOnSelect: false
            });
        }

         jQuery('#product_loader_form').on('submit', function(e) {
            if (this.checkValidity()) {
                e.preventDefault();
                const form = this;
                const formData = new FormData(form);
                showLoading(); // Start counter

                // Submit form via fetch (AJAX)
                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                })
                .then(response => response.text()) // or .json() if API returns JSON
                .then(data => {
                    Swal.close(); // Close after complete
                    // Optional: Redirect or show success
                   window.location.href = "/admin/products"; 
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire("Error", "Something went wrong", "error");
                });
            }
        });
        
    });

    // Format color options with color preview
    function formatColorOption(state) {
        if (!state.id) return state.text;

        const color = $(state.element).data('color');
        const name = $(state.element).data('name');

        return $(`<span class="d-flex align-items-center gap-2">
            <span style="background-color:${color};width:20px;height:20px;display:inline-block;border-radius:4px;"></span>
            <span>${name}</span>
        </span>`);
    }

    // Handle color selection
    $('.colorSelect').on('change', function(e) {
        const selectedColors = $(this).val();
        const colorBox = $('#colorBox');
        const tableBody = $('#selectedColorsTableBody');

        if (selectedColors && selectedColors.length > 0) {
            colorBox.show();
            updateColorRows(selectedColors);
        } else {
            colorBox.hide();
            tableBody.empty();
        }
    });

    // Handle size selection
    $('.sizeSelector').on('change', function(e) {
        const selectedSizes = $(this).val();
        const sizeBox = $('#sizeBox');
        const tableBody = $('#selectedSizesTableBody');

        if (selectedSizes && selectedSizes.length > 0) {
            sizeBox.show();
            updateSizeRows(selectedSizes);
        } else {
            sizeBox.hide();
            tableBody.empty();
        }
    });

    function generateCodegtgg() {
        document.getElementById('barcode').value = Math.floor(Math.random() * 900000) + 100000;
    }

     function showLoading() {
        let counter = 0;

        Swal.fire({
            icon: "info",
            title: "Progress",
            text: `0% video transcoding progress`,
            showCancelButton: false,
            showConfirmButton: false
        });

        const interval = setInterval(function() {
            counter += 1;
            // $('.swal2-html-container').text(`${counter}% video transcoding progress`);

             Swal.update({
                text: `${counter}% video transcoding progress`
            });

            if (counter >= 100) {
                clearInterval(interval);
                Swal.close(); 
            }
        }, 500);
    }
</script>
@endpush