@extends('layouts.app')

@php
// Define product price variables for use in the view
$productPrice = $product->discount_price > 0 ? $product->discount_price : $product->price;
@endphp

@section('content')




<div class="page-title">
    <div class="d-flex gap-2 align-items-center">
        <i class="fa-solid fa-shop"></i> {{ __('Edit Product') }}
    </div>
</div>
{{-- Display Validation Errors --}}
@if ($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong><i class="fa fa-exclamation-triangle me-2"></i>{{ __('There were some problems with your input:') }}</strong>
    <ul class="mb-0 mt-2">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<form action="{{ route('admin.product.update', $product->id) }}" method="POST" enctype="multipart/form-data" id="product_loader_form">
    {{-- CSRF Token --}}
    @csrf
    @method('PUT')
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
                    <option value="{{ $shop->id }}" {{ $product->shop_id == $shop->id ? 'selected' : '' }}>
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
                <x-input label="Product Name" name="name" type="text" placeholder="Product Name" required="true"
                    value="{{ $product->name }}" />
            </div>

            <div class="col-md-12 mt-4">
                <label for="shortDescription" class="form-label">{{ __('Short Description') }}</label>
                <textarea class="form-control @error('shortDescription') is-invalid @enderror"
                    id="shortDescription"
                    name="shortDescription"
                    rows="3">{{ old('shortDescription', $product->short_description) }}</textarea>
                @error('shortDescription')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-12 mt-4">
                <label for="description" class="form-label">{{ __('Description') }}</label>
                <textarea class="form-control @error('description') is-invalid @enderror"
                    id="description"
                    name="description"
                    rows="5">{{ old('description', $product->description) }}</textarea>
                @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <!--######## General Information ##########-->
    <div class="card mt-4">
        <div class="card-body">
            <div class="d-flex gap-2 border-bottom pb-2">
                <i class="fa-solid fa-box"></i>
                <h5>
                    {{ __('Logistics Information') }}
                </h5>
            </div>
            <div class="row mt-3">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="bag_number">{{ __('Bag Number') }}</label>
                        <input type="text" name="bag_number" id="bag_number" class="form-control @error('bag_number') is-invalid @enderror" value="{{ old('bag_number', $product->bag_number) }}">
                        @error('bag_number')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="location">{{ __('Location (Box)') }}</label>
                        <input type="text" name="location" id="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location', $product->location) }}">
                        @error('location')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="row">{{ __('Row') }}</label>
                        <input type="number" name="row" id="row" class="form-control @error('row') is-invalid @enderror" value="{{ old('row', $product->row) }}">
                        @error('row')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--######## General Information ##########-->
    <div class="card mt-4">
        <div class="card-body">

            <div class="d-flex gap-2 border-bottom pb-2">
                <i class="fa-solid fa-user"></i>
                <h5>
                    {{ __('Generale Information') }}
                </h5>
            </div>

            <div class="row mt-3">

                <div class="col-md-6 col-lg-4">
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
                        value="{{ $product->code }}"
                        oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" />
                    @error('code')
                    <p class="text text-danger m-0">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-md-6 col-lg-4 mt-3 mt-md-0">
                    <label class="form-label">
                        {{ __('Select Category') }}
                        <span class="text-danger">*</span>
                    </label>
                    <select name="category" class="form-control select2" style="width: 100%">
                        <option value="" disabled>
                            {{ __('Select Category') }}
                        </option>
                        @foreach ($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ in_array($category->id, $product->categories?->pluck('id')->toArray()) ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('category')
                    <p class="text text-danger m-0">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-md-6 col-lg-4 mt-3 mt-md-0">
                    <label class="form-label">
                        {{ __('Select Sub Categories') }}
                    </label>
                    <select name="sub_category[]" class="form-control select2" multiple style="width: 100%"
                        data-placeholder="Select Sub Category">
                        @foreach ($subCategories as $subCategory)
                        <option value="{{ $subCategory->id }}"
                            {{ in_array($subCategory->id, $product->subcategories?->pluck('id')->toArray()) ? 'selected' : '' }}>
                            {{ $subCategory->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('sub_category')
                    <p class="text text-danger m-0">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-md-6 col-lg-4 mt-4">
                    <label for="season_id" class="form-label">
                        {{ __('Season') }}
                        <span class="text-danger">*</span>
                    </label>
                    <select name="season_id" id="season_id" class="form-select select2 @error('season_id') is-invalid @enderror" required>
                        <option value="">{{ __('Select Season') }}</option>
                        @foreach($seasons as $season)
                        <option value="{{ $season->id }}" {{ $product->season_id == $season->id ? 'selected' : '' }}>
                            {{ $season->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('season_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 col-lg-4 mt-4">
                    <label for="quality_id" class="form-label">
                        {{ __('Quality') }}
                        <span class="text-danger">*</span>
                    </label>
                    <select name="quality_id" id="quality_id" class="form-select select2 @error('quality_id') is-invalid @enderror" required>
                        <option value="">{{ __('Select Quality') }}</option>
                        @foreach($qualities as $quality)
                        <option value="{{ $quality->id }}" {{ $product->quality_id == $quality->id ? 'selected' : '' }}>
                            {{ $quality->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('quality_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 col-lg-4 mt-4">
                    <label for="unit_id" class="form-label">
                        {{ __('Select Unit') }}
                        <span class="text-danger">*</span>
                    </label>
                    <select name="unit_id" id="unit_id" class="form-select select2 @error('unit_id') is-invalid @enderror" required>
                        <option value="">{{ __('Select Unit') }}</option>
                        @foreach($units as $unit)
                        <option value="{{ $unit->id }}" {{ $unit->id == $product->unit_id ? 'selected' : '' }}>
                            {{ $unit->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('unit_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 col-lg-4 mt-4">
                    <label for="sizeIds">{{ __('Sizes') }}</label>
                    <select class="form-control select2" name="sizeIds[]" id="sizeIds" multiple>
                        @foreach($sizes as $size)
                        @php
                        $existingSize = $product->sizes->find($size->id);
                        $existingPrice = $existingSize ? $existingSize->pivot->price : 0;
                        @endphp
                        <option value="{{ $size->id }}"
                            data-size="{{ $size->name }}"
                            data-price="{{ $existingPrice }}"
                            {{ $product->sizes->contains($size->id) ? 'selected' : '' }}>
                            {{ $size->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

            </div>
        </div>
    </div>

    <!--######## Price Information ##########-->
    <div class="card mt-4 mb-4">
        <div class="card-body">

            <div class="d-flex gap-2 border-bottom pb-2">
                <i class="fa-solid fa-user"></i>
                <h5>
                    {{ __('Price Information') }}
                </h5>
            </div>
            <div class="row mt-3">
                {{-- <div class="col-lg-3 col-md-6">
                        <x-input type="text" name="buy_price" label="Buying Price" placeholder="Buying Price"
                            required="true" onlyNumber="true" :value="$product->buy_price" />
                    </div> --}}

                <div class="col-lg-3 col-md-6">
                    <x-input type="text" name="price" label="Selling Price" placeholder="Selling Price"
                        required="true" onlyNumber="true" :value="$product->price" />
                </div>

                <div class="col-lg-3 col-md-6 mt-3 mt-md-0">
                    <x-input type="text" name="discount_price" label="Discount Price"
                        placeholder="Discount Price" onlyNumber="true" :value="$product->discount_price" />
                </div>

                <div class="col-lg-3 col-md-6 mt-3 mt-lg-0">
                    <x-input type="text" name="quantity" label="Current Stock Quantity"
                        placeholder="Current Stock Quantity" onlyNumber="true" required="true" :value="$product->quantity" />
                </div>

                <div class="col-md-6 mt-3">
                    <x-select name="taxs[]" label="Vat & Tax" placeholder="Select Vat & Tax" multiselect="true">
                        @foreach ($taxs as $tax)
                        <option value="{{ $tax->id }}" {{ in_array($tax->id, $product->vatTaxes?->pluck('id')->toArray()) ? 'selected' : '' }}>
                            {{ $tax->name }} ({{ $tax->percentage }}%)
                        </option>
                        @endforeach
                    </x-select>
                </div>

                <div class="col-lg-3 col-md-6 mt-3">
                    <x-input type="text" onlyNumber="true" name="min_order_quantity"
                        label="Minimum Order Quantity" placeholder="Minimum Order Quantity" :value="$product->min_order_quantity" />
                </div>
            </div>


            <!--######## Size wise price table ##########-->
            <div id="sizeBox" class="mt-3" style="{{ count($product->sizes) > 0 ? '' : 'display: none;' }}">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>{{ __('Size') }}</th>
                                <th>{{ __('Extra Price') }}</th>
                            </tr>
                        </thead>
                        <tbody id="selectedSizesTableBody">
                            @foreach($product->sizes as $size)
                            <tr id="size_row_{{ $size->id }}">
                                <td>
                                    <h4 class="mb-0">{{ $size->name }}</h4>
                                    <input type="hidden" name="size[{{ $size->id }}][name]" value="{{ $size->name }}">
                                    <input type="hidden" name="size[{{ $size->id }}][id]" value="{{ $size->id }}">
                                </td>
                                <td>
                                    <input type="number"
                                        class="form-control"
                                        name="size[{{ $size->id }}][price]"
                                        value="{{ $size->pivot->price }}"
                                        min="0"
                                        step="0.01">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    {{--
        <!--######## Thumbnail Information ##########-->
        <div class="row mb-3">
            <!-- <div class="col-md-5 col-xl-3">
                <div class="card card-body h-100">
                    <div class="mb-2">
                        <h5>
                            {{ __('Thumbnail') }}
    <span class="text-primary">(Ratio 1:1 (500 x 500 px))</span> *
    </h5>
    @error('thumbnail')
    <p class="text-danger">{{ $message }}</p>
    @enderror
    </div>

    <label for="thumbnail" class="additionThumbnail">
        <img src="{{ $product->thumbnail ?? asset('default/upload.png') }}" id="preview"
            alt="" width="100%">
    </label>
    <input id="thumbnail" accept="image/*" type="file" name="thumbnail" class="d-none"
        onchange="previewFile(event, 'preview')">
    </div>
    </div> -->

    <div class="col-md-7 col-xl-9 mt-3 mt-md-0">
        <div class="card h-100">
            <div class="card-body">
                <!-- <div class="mb-2">
                            <h5>
                                {{ __(' Thumbnail') }}
                                <span class="text-primary">(Ratio 1:1 (500 x 500 px))</span> *
                            </h5>
                            @error('additionThumbnail')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div> -->

                <div class="d-flex flex-wrap gap-3" id="additionalElements">

                    <!-- previous additional thumbnail -->
                    @foreach ($product->medias as $media)
                    @php
                    $source = asset('default/upload.png');
                    if (Storage::exists($media->src)) {
                    $source = Storage::url($media->src);
                    }
                    @endphp

                    <div id="additionShow">
                        <label for="previousThumbnailShow{{ $media->id }}" class="additionThumbnail">
                            <img src="{{ $source }}" id="previewShow{{ $media->id }}"
                                alt="thumbnail" width="100%" height="100%">
                            <a href="{{ route('admin.product.remove.thumbnail', ['product' => $product->id, 'media' => $media->id]) }}"
                                class="delete btn btn-sm btn-outline-danger">
                                <i class="fa fa-trash"></i>
                            </a>
                        </label>
                        <input type="hidden" name="previousThumbnail[{{ $loop->index }}][id]"
                            value="{{ $media->id }}">
                        <input id="previousThumbnailShow{{ $media->id }}" accept="image/*" type="file"
                            name="previousThumbnail[{{ $loop->index }}][file]" class="d-none"
                            onchange="previewFile(event, 'previewShow{{ $media->id }}')" />
                    </div>
                    @endforeach

                    <!-- New additional thumbnail -->
                    <div id="addition">
                        <label for="additionThumbnail1" class="additionThumbnail">
                            <img src="{{ asset('default/upload.png') }}" id="preview2" alt=""
                                width="100%" height="100%">
                            <button onclick="removeThumbnail('addition')" id="removeThumbnail1" type="button"
                                class="delete btn btn-sm btn-outline-danger" style="display: none"><i
                                    class="fa fa-trash"></i></button>
                        </label>
                        <input id="additionThumbnail1" accept="image/*" type="file"
                            name="additionThumbnail[]" class="d-none"
                            onchange="previewAdditionalFile(event, 'preview2', 'removeThumbnail1')">
                    </div>

                </div>


            </div>
        </div>
    </div>
    </div> --}}
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
            <div class="mt-3" id="videoPreviewContainer">
                @if ($product->videos)
                @foreach ($product->videos as $video)
                <div id="video_{{ $video->id }}" class="mb-2 position-relative">
                    <video width="320" height="240" controls>
                        <source src="{{ Storage::disk('s3')->url($video->src) }}" type="{{ $video->type }}">
                        Your browser does not support the video tag.
                    </video>
                    @if($video->thumbnail)
                    <img src="{{ Storage::disk('s3')->url($video->thumbnail) }}"
                        class="video-poster"
                        alt="{{ $video->title }}"
                        width="320" height="240">
                    @endif
                    <a href="{{ route('admin.product.remove-videos', ['video' => $video->id]) }}"
                        class="delete btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-2">
                        <i class="fa fa-trash"></i> {{ __('Remove') }}
                    </a>
                </div>
                @endforeach
                @endif
            </div>
            <div class="mt-3" id="newVideoPreviewContainer"></div>
        </div>
    </div>
    <!--######## Thumbnail Information ##########-->
    <div class="row mb-3">


        <div class="col-md-7 col-xl-9 mt-3 mt-md-0">
            <div class="card h-100">
                <div class="card-body">
                    <div class="mb-2">
                        <h5>
                            {{ __('Product Images') }}
                            <span class="text-primary">(Ratio 1:1 (500 x 500 px))</span> *
                        </h5>
                        @error('additionThumbnail')
                        <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="d-flex flex-wrap gap-3" id="additionalElements">

                        <!-- previous additional thumbnail -->
                        @foreach ($product->medias as $media)
                        @php
                        $source = asset('default/upload.png');
                        if (Storage::exists($media->src)) {
                        $source = Storage::url($media->src);
                        }
                        @endphp

                        <div id="additionShow">
                            <label for="previousThumbnailShow{{ $media->id }}" class="additionThumbnail">
                                <img src="{{ $source }}" id="previewShow{{ $media->id }}"
                                    alt="thumbnail" width="100%" height="100%">
                                <a href="{{ route('shop.product.remove.thumbnail', ['product' => $product->id, 'media' => $media->id]) }}"
                                    class="delete btn btn-sm btn-outline-danger">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </label>
                            <input type="hidden" name="previousThumbnail[{{ $loop->index }}][id]"
                                value="{{ $media->id }}">
                            <input id="previousThumbnailShow{{ $media->id }}" accept="image/*" type="file"
                                name="previousThumbnail[{{ $loop->index }}][file]" class="d-none"
                                onchange="previewFile(event, 'previewShow{{ $media->id }}')" />
                        </div>
                        @endforeach

                        <!-- New additional thumbnail -->
                        <div id="addition">
                            <label for="additionThumbnail1" class="additionThumbnail">
                                <img src="{{ asset('default/upload.png') }}" id="preview2" alt=""
                                    width="100%" height="100%">
                                <button onclick="removeThumbnail('addition')" id="removeThumbnail1" type="button"
                                    class="delete btn btn-sm btn-outline-danger" style="display: none"><i
                                        class="fa fa-trash"></i></button>
                            </label>
                            <input id="additionThumbnail1" accept="image/*" type="file"
                                name="additionThumbnail[]" class="d-none"
                                onchange="previewAdditionalFile(event, 'preview2', 'removeThumbnail1')">
                        </div>

                    </div>


                </div>
            </div>
        </div>
    </div>


    <div class="d-flex gap-3 justify-content-end align-items-center mb-3">
        <button type="reset" class="btn btn-lg btn-outline-secondary rounded py-2">
            {{ __('Reset') }}
        </button>
        <button type="submit" class="btn btn-lg btn-primary rounded py-2 px-5">
            {{ __('Update') }}
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
<!-- TinyMCE -->
<script src="https://cdn.tiny.cloud/1/404ticmbmer7eyfwwt48gxb8isw74kc374o8ae3p06vqow2v/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
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
        if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
            jQuery('.select2').select2({
                width: '100%',
                allowClear: true
            });

            // Size dropdown change event
            jQuery('select[name="sizeIds[]"]').on('change', function() {
                console.log('Size selection changed:', jQuery(this).val());
                var productPrice = jQuery('#price').val() ?? 0;
                var productDiscountPrice = jQuery('#discount_price').val() ?? 0;
                var mainPrice = productDiscountPrice > 0 ? productDiscountPrice : productPrice;

                // Get the selected options
                var selectedOptions = jQuery(this).find(':selected');

                // Check if there are selected options
                if (selectedOptions.length > 0) {
                    jQuery('#sizeBox').show();
                } else {
                    jQuery('#sizeBox').hide();
                }

                // Clear existing rows first
                jQuery('#selectedSizesTableBody').empty();

                // Add rows for all selected sizes
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
                            <span class="bg-light px-2 py-1 rounded">
                                <i class="fa-solid fa-plus"></i>
                            </span>
                            <input type="text" class="form-control extraPriceForm" name="size[${sizeId}][price]" value="${existingPrice}" style="width: 140px" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/^(\d*\.\d{0,2}|\d*)$/, '$1');">
                        </div>
                    </td>
                </tr>
                `);
                });
            });

            // Update prices when main price changes
            jQuery('#price, #discount_price').on('input', function() {
                updateMainPrices();
            });

            // Initialize existing sizes
            initializeExistingSizes();
        } else {
            console.error('jQuery or Select2 is not loaded');
        }

        jQuery('#product_loader_form').submit(function() {
            if (jQuery(this)[0].checkValidity()) {
                showLoading();

            }


        });
    });

    function initializeExistingSizes() {
        var productPrice = parseFloat(jQuery('#price').val()) || 0;
        var productDiscountPrice = parseFloat(jQuery('#discount_price').val()) || 0;
        var mainPrice = productDiscountPrice > 0 ? productDiscountPrice : productPrice;

        // Clear existing rows first
        jQuery('#selectedSizesTableBody').empty();

        jQuery('select[name="sizeIds[]"] option:selected').each(function() {
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
                        <span class="bg-light px-2 py-1 rounded">
                            <i class="fa-solid fa-plus"></i>
                        </span>
                        <input type="text" 
                            class="form-control extraPriceForm" 
                            name="size[${sizeId}][price]" 
                            value="${existingPrice}"
                            style="width: 140px"
                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/^(\d*\.\d{0,2}|\d*)$/, '$1');">
                    </div>
                </td>
            </tr>
        `);
        });
    }

    function updateMainPrices() {
        var productPrice = parseFloat(jQuery('#price').val()) || 0;
        var productDiscountPrice = parseFloat(jQuery('#discount_price').val()) || 0;
        var mainPrice = productDiscountPrice > 0 ? productDiscountPrice : productPrice;
        jQuery('.mainProductPrice').text(mainPrice);
    }

    function updateSizePrice(sizeId, price) {
        jQuery(`select[name="sizeIds[]"] option[value="${sizeId}"]`).data('price', price);
    }

    function deleteSizeRow(sizeId) {
        // Only remove the visual row from the table, don't deselect from dropdown
        jQuery(`#size_row_${sizeId}`).remove();

        // Instead of deselecting, just mark it for removal with a hidden input
        // This ensures the size ID stays in the sizeIds array but we know it should be removed
        if (!jQuery(`#size_remove_${sizeId}`).length) {
            jQuery('form').append(`<input type="hidden" id="size_remove_${sizeId}" name="size_remove[]" value="${sizeId}">`);
        }

        // We don't trigger change as that would rebuild the table and remove our size
        // jQuery(`select[name="sizeIds[]"] option[value="${sizeId}"]`).prop('selected', false);
        // jQuery('select[name="sizeIds[]"]').trigger('change');
    }

    function showLoading() {
        let counter = 0;

        Swal.fire({
            icon: "info",
            title: "Progress",
            text: `${counter}% video transcoding progress`,
            showCancelButton: false,
            showConfirmButton: false
        });

        const interval = setInterval(function() {
            counter += 1;
            $('.swal2-html-container').text(`${counter}% video transcoding progress`);
            if (counter >= 100) {
                clearInterval(interval);
            }
        }, 500);


    }
</script>

@endpush