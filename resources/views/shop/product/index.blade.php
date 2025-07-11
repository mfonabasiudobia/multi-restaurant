@extends('layouts.app')
@section('content')
    @php
        // Import necessary facades
        use Illuminate\Support\Facades\Storage;
    @endphp
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <h4>
            {{ __('Product List') }}
        </h4>
    </div>

    <!-- Flash Deal Alert -->
    @if ($flashSale)
        <div>
            <div class="alert flash-deal-alert d-flex justify-content-between align-items-center">
                <div>
                    <div class="deal-title">{{ __('Flash Sale Coming Soon') }}</div>
                    <span class="deal-text">{{ $flashSale->name }}</span>
                </div>
                <div class="countdown d-flex align-items-center">
                    <!-- Days -->
                    <div class="countdown-section">
                        <div class="countdown-label">Days</div>
                        <div id="days" class="countdown-time">00</div>
                    </div>
                    <!-- Hours -->
                    <div class="countdown-section">
                        <div class="countdown-label">Hours</div>
                        <div id="hours" class="countdown-time">00</div>
                    </div>
                    <!-- Minutes -->
                    <div class="countdown-section">
                        <div class="countdown-label">Minutes</div>
                        <div id="minutes" class="countdown-time">00</div>
                    </div>
                    <!-- Seconds -->
                    <div class="countdown-section">
                        <div class="countdown-label">Seconds</div>
                        <div id="seconds" class="countdown-time">00</div>
                    </div>
                </div>
                @hasPermission('shop.flashSale.show')
                    <a href="{{ route('shop.flashSale.show', $flashSale->id) }}" class="btn btn-primary py-2.5">
                        Add Product
                    </a>
                @endhasPermission
            </div>
        </div>
    @endif
    <!-- End Flash Deal Alert -->

    <div class="container-fluid mt-3">

        <div class="card my-3">
            <div class="card-body">

                <div class="d-flex gap-2 pb-2">
                    <h5>
                        {{ __('Filter Products') }}
                    </h5>
                </div>

                <form action="" method="GET">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <x-select label="Category" name="category">
                                <option value="">{{ __('All Categories') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </x-select>
                        </div>

                        <div class="col-md-3 mb-3">
                            <x-select label="Season" name="season">
                                <option value="">{{ __('All Seasons') }}</option>
                                @foreach ($seasons as $season)
                                    <option value="{{ $season->id }}" 
                                            {{ request('season') == $season->id ? 'selected' : '' }}>
                                        {{ $season->name }}
                                    </option>
                                @endforeach
                            </x-select>
                        </div>

                        <div class="col-md-3 mb-3">
                            <x-select label="Quality" name="quality">
                                <option value="">{{ __('All Qualities') }}</option>
                                @foreach ($qualities as $quality)
                                    <option value="{{ $quality->id }}" 
                                            {{ request('quality') == $quality->id ? 'selected' : '' }}>
                                        {{ $quality->name }}
                                    </option>
                                @endforeach
                            </x-select>
                        </div>

                        <div class="col-md-3 mb-3">
                            <x-select label="Status" name="is_active">
                                <option value="">{{ __('All Status') }}</option>
                                <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>
                                    {{ __('Active') }}
                                </option>
                                <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>
                                    {{ __('Inactive') }}
                                </option>
                            </x-select>
                        </div>

                        <div class="col-md-12 mb-3">
                            <x-input 
                                type="search" 
                                label="Search" 
                                name="search" 
                                value="{{ request('search') }}" 
                                placeholder="{{ __('Search by product name, code or category...') }}" 
                            />
                        </div>
                    </div>

                    <div class="mt-2 d-flex gap-2 justify-content-end">
                        <a href="{{ route('shop.product.index') }}" class="btn btn-light">
                            <i class="fa-solid fa-rotate"></i> {{ __('Reset') }}
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-filter"></i> {{ __('Filter') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="mb-3 card">
            <div class="card-body">

                <form action="" class="d-flex align-items-center justify-content-between gap-3 mb-3">
                    <div class="input-group" style="max-width: 400px">
                        <input type="text" name="search" class="form-control"
                            placeholder="{{ __('Search by product name') }}" value="{{ request('search') }}">
                        <button type="submit" class="input-group-text btn btn-primary">
                            <i class="fa fa-search"></i> {{ __('Search') }}
                        </button>
                    </div>
                    @hasPermission('shop.product.create')
                        <a href="{{ route('shop.product.create') }}" class="btn py-2 btn-primary">
                            <i class="fa fa-plus-circle"></i>
                            {{ __('Create New') }}
                        </a>
                    @endhasPermission
                </form>
                <!-- Modal -->
                <!-- Video and thumbnail modals will be dynamically added by JavaScript -->

                <div class="table-responsive">
                    <table class="table border table-responsive-lg">
                        <thead>
                            <tr>
                                <th class="text-center">{{ __('SL') }}</th>
                             
                                <th>{{ __('Videos') }}</th>
                                <th>{{ __('Product Name') }}</th>
                                <th class="text-center">{{ __('Price') }}</th>
                                <th class="text-center">{{ __('Discount Price') }}</th>
                                <th class="text-center">
                                    {{ __('Verify Status') }}
                                </th>
                                @hasPermission('shop.product.toggle')
                                    <th class="text-center">{{ __('Status') }}</th>
                                @endhasPermission
                                <th class="text-center">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        @forelse($products as $key => $product)
                            <tr>
                                <td class="text-center">{{ ++$key }}</td>

                              
                                <td>
                                    <div class="d-flex gap-2">
                                        <!-- Videos -->
                                        @if($product->videos->isNotEmpty())
                                            <div class="cursor-pointer" onclick="showVideos({{ json_encode($product->processedVideos()) }}, '{{ $product->name }}')">
                                                <div class="position-relative">
                                                    <img src="{{ $product->videos->first()->thumbnail }}"
                                                        alt="{{ $product->name }}"
                                                        class="rounded"
                                                        width="50" height="50"
                                                        style="object-fit: cover;">
                                                    @if($product->videos->count() > 1)
                                                        <span class="position-absolute top-0 end-0 badge bg-primary">
                                                            +{{ $product->videos->count() - 1 }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Additional Thumbnails -->
                                        @if($product->medias->isNotEmpty())
                                            <div class="cursor-pointer" onclick="showThumbnails({{ json_encode($product->additionalThumbnails()) }}, '{{ $product->name }}')">
                                                <div class="position-relative">
                                                    @php
                                                        $firstMedia = $product->medias->first();
                                                        $thumbnailUrl = $firstMedia && $firstMedia->src ? $product->transformUrl($firstMedia->src) : asset('default/default.jpg');
                                                    @endphp
                                                    <img src="{{ $thumbnailUrl }}" 
                                                        alt="{{ $product->name }}"
                                                        class="rounded"
                                                        width="50" height="50"
                                                        style="object-fit: cover;">
                                                    @if($product->medias->count() > 1)
                                                        <span class="position-absolute top-0 end-0 badge bg-info">
                                                            +{{ $product->medias->count() - 1 }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif

                                        @if($product->videos->isEmpty() && $product->medias->isEmpty())
                                            <span class="badge bg-light text-dark">{{ __('No Media') }}</span>
                                        @endif
                                    </div>
                                </td>

                                <td>{{ Str::limit($product->name, 50, '...') }}</td>

                                <td class="text-center">
                                    {{ showCurrency($product->price) }}
                                </td>

                                <td class="text-center">
                                    {{ showCurrency($product->discount_price) }}
                                </td>

                                <td class="text-center" style="min-width: 110px">
                                    @if ($product->is_approve)
                                        <span class="status-approved">
                                            <i class="fa fa-check-circle text-success"></i> {{ __('Approved') }}
                                        </span>
                                    @else
                                        <span class="status-pending" data-bs-toggle="tooltip" data-bs-placement="top"
                                            data-bs-title="Your product status is pending because admin hasn't approved it. When admin will approve your product, it will be show as approved.">
                                            <i class="fa-solid fa-triangle-exclamation"></i>
                                            {{ __('Pending') }}
                                        </span>
                                    @endif
                                </td>

                                @hasPermission('shop.product.toggle')
                                    <td class="text-center">
                                        <label class="switch mb-0" data-bs-toggle="tooltip" data-bs-placement="left"
                                            data-bs-title="{{ __('Update product status') }}">
                                            <a href="{{ route('shop.product.toggle', $product->id) }}">
                                                <input type="checkbox" {{ $product->is_active ? 'checked' : '' }}>
                                                <span class="slider round"></span>
                                            </a>
                                        </label>
                                    </td>
                                @endhasPermission

                                <td class="text-center">
                                    <div class="d-flex gap-2 justify-content-center">
                                        @hasPermission('shop.product.show')
                                            <a href="{{ route('shop.product.show', $product->id) }}"
                                                class="btn btn-outline-primary circleIcon" data-bs-toggle="tooltip"
                                                data-bs-placement="left" data-bs-title="{{ __('View Product') }}">
                                                <i class="fa-regular fa-eye"></i>
                                            </a>
                                        @endhasPermission
                                        @hasPermission('shop.product.barcode')
                                            <a href="{{ route('shop.product.barcode', $product->id) }}"
                                                class="btn btn-outline-info circleIcon" data-bs-toggle="tooltip"
                                                data-bs-placement="top"
                                                data-bs-title="{{ __('Generate Barcode for this product') }}">
                                                <i class="bi bi-upc-scan"></i>
                                            </a>
                                        @endhasPermission
                                        @hasPermission('shop.product.edit')
                                            <a href="{{ route('shop.product.edit', $product->id) }}"
                                                class="btn btn-outline-primary circleIcon" data-bs-toggle="tooltip"
                                                data-bs-placement="left" data-bs-title="{{ __('Edit Product') }}">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>
                                        @endhasPermission
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center" colspan="100%">{{ __('No Data Found') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="my-3">
            {{ $products->links() }}
        </div>

    </div>
@endsection
@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(".confirmApprove").on("click", function(e) {
            e.preventDefault();
            const url = $(this).attr("href");
            Swal.fire({
                title: "Are you sure?",
                text: "You want to approve this product",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Approve it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('.delete-btn').on('click', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');
                
                if (confirm('Are you sure you want to delete this product?')) {
                    window.location.href = url;
                }
            });
        });
    </script>

    @if ($flashSale)
        <script>
            // Set the start and end date/time
            var startDateAndTime = "{{ $flashSale->start_date }}T{{ $flashSale->start_time }}";
            var endDateAndTime = "{{ $flashSale->end_date }}T{{ $flashSale->end_time }}";
            let startDate = new Date(startDateAndTime).getTime();
            let endDate = new Date(endDateAndTime).getTime();

            // Update the countdown every 1 second
            let countdownInterval = setInterval(() => {
                let now = new Date().getTime();

                // If current time is before the start date, show "Deal Coming" message
                if (now < startDate) {
                    let distanceToStart = startDate - now;

                    // Time calculations for days, hours, minutes, and seconds
                    let days = Math.floor(distanceToStart / (1000 * 60 * 60 * 24));
                    let hours = Math.floor((distanceToStart % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    let minutes = Math.floor((distanceToStart % (1000 * 60 * 60)) / (1000 * 60));
                    let seconds = Math.floor((distanceToStart % (1000 * 60)) / 1000);

                    // Display the countdown with a "Deal Coming" message
                    document.getElementById("days").innerHTML = String(days).padStart(2, '0');
                    document.getElementById("hours").innerHTML = String(hours).padStart(2, '0');
                    document.getElementById("minutes").innerHTML = String(minutes).padStart(2, '0');
                    document.getElementById("seconds").innerHTML = String(seconds).padStart(2, '0');
                    return;
                }

                // Once the current time is after the start date and before the end date, show the active countdown
                let distance = endDate - now;

                // If the deal has ended, stop the countdown and show the message
                if (distance < 0) {
                    clearInterval(countdownInterval);
                    document.getElementById("days").innerHTML = "00";
                    document.getElementById("hours").innerHTML = "00";
                    document.getElementById("minutes").innerHTML = "00";
                    document.getElementById("seconds").innerHTML = "00";
                    document.querySelector(".deal-text").innerHTML = "Deal Ended!";
                    return;
                }

                // Time calculations for days, hours, minutes, and seconds
                let days = Math.floor(distance / (1000 * 60 * 60 * 24));
                let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                let seconds = Math.floor((distance % (1000 * 60)) / 1000);

                // Display the result
                document.getElementById("days").innerHTML = String(days).padStart(2, '0');
                document.getElementById("hours").innerHTML = String(hours).padStart(2, '0');
                document.getElementById("minutes").innerHTML = String(minutes).padStart(2, '0');
                document.getElementById("seconds").innerHTML = String(seconds).padStart(2, '0');
            }, 1000);
        </script>
    @endif
    <script>
        function showVideos(videos, productName) {
            let modalContent = `
                <div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="videoModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="videoModalLabel">${productName} - Videos</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">`;
            
            videos.forEach(video => {
                modalContent += `
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body p-0">
                                <video src="${video.url}" controls width="100%" height="200"></video>
                            </div>
                        </div>
                    </div>`;
            });
            
            modalContent += `
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
            
            // Remove any existing modal
            $('#videoModal').remove();
            
            // Append the new modal to the body
            $('body').append(modalContent);
            
            // Show the modal
            $('#videoModal').modal('show');
        }

        function showThumbnails(thumbnails, productName) {
            let modalContent = `
                <div class="modal fade" id="thumbnailModal" tabindex="-1" aria-labelledby="thumbnailModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="thumbnailModalLabel">${productName} - Images</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">`;
            
            thumbnails.forEach(thumbnail => {
                modalContent += `
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body p-0">
                                <img src="${thumbnail.thumbnail}" alt="${productName}" class="img-fluid">
                            </div>
                        </div>
                    </div>`;
            });
            
            modalContent += `
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
            
            // Remove any existing modal
            $('#thumbnailModal').remove();
            
            // Append the new modal to the body
            $('body').append(modalContent);
            
            // Show the modal
            $('#thumbnailModal').modal('show');
        }
    </script>
@endpush
@push('css')
    <style>
        /* Flash Deal Alert Styles */
        .flash-deal-alert {
            background: linear-gradient(90deg, #9b34ff, #617eff);
            color: white;
            border-radius: 8px;
            padding: 8px 15px;
        }

        .deal-title {
            font-size: 20px;
        }

        .deal-text {
            font-size: 16px;
        }

        /* Countdown Timer Styles */
        .countdown {
            display: flex;
            gap: 20px;
            /* Space between sections */
        }

        .countdown-section {
            text-align: center;
        }

        .countdown-label {
            font-size: 14px;
            font-weight: bold;
        }

        .countdown-time {
            width: 46px;
            height: 46px;
            font-size: 20px;
            font-weight: bold;
            margin-top: 5px;
            border: 1px solid var(--theme-color);
            padding: 5px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            background-color: var(--theme-color);
        }
    </style>
@endpush
