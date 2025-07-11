@extends('layouts.app')
@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0">{{ $pageTitle }}</h4>
        
        @hasPermission('admin.product.create')
            @unless(request()->filled('status'))
                <a href="{{ route('admin.product.create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> {{ __('Add Product') }}
                </a>
            @endunless
        @endhasPermission
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="" method="GET">
                @if(request('my_shop'))
                    <input type="hidden" name="my_shop" value="1">
                @endif

                @if (request('approve'))
                    <input type="hidden" name="approve" value="{{ request('approve') }}">
                @else
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif

                <div class="row">
                    @unless(request('my_shop'))
                    <div class="col-lg-4 col-md-6 mb-3">
                        <x-select label="Shop" name="shop">
                            <option value="">{{ __('All Shops') }}</option>
                            @foreach ($shops as $shop)
                                <option value="{{ $shop->id }}" {{ request('shop') == $shop->id ? 'selected' : '' }}>
                                    {{ $shop->name }}
                                </option>
                            @endforeach
                        </x-select>
                    </div>
                    @endunless

                    <div class="col-lg-4 col-md-6 mb-3">
                        <x-select label="Category" name="category_id">
                            <option value="">{{ __('All Categories') }}</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </x-select>
                    </div>

                    

                    <div class="col-lg-4 col-md-6 mb-3">
                        <x-input type="search" label="Search" name="search" value="{{ request('search') }}" placeholder="Search by name..." />
                    </div>

                    <div class="col-lg-4 col-md-6 mb-3">
                        <x-select label="Status" name="is_active">
                            <option value="">{{ __('All Status') }}</option>
                            <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>{{ __('Active') }}</option>
                            <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                        </x-select>
                    </div>
                </div>

                <div class="mt-2 d-flex gap-2 justify-content-end">
                    <a href="{{ route('admin.product.index', request('my_shop') ? ['my_shop' => 1] : []) }}" 
                       class="btn btn-light">
                        <i class="fa-solid fa-rotate"></i> {{ __('Reset') }}
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-filter"></i> {{ __('Filter') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>{{ __('Videos') }}</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Category') }}</th>
                        <th>{{ __('Price') }}</th>
                        <th>{{ __('Stock') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
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
                            <td>
                                <h6 class="mb-0">{{ $product->name }}</h6>
                                
                            </td>
                            <td>
                                @foreach($product->categories as $category)
                                    <span class="badge bg-light text-dark">{{ $category->name }}</span>
                                @endforeach
                            </td>
                            <td>
                                <div class="fw-bold">{{ format_price($product->price) }}</div>
                                @if($product->discount)
                                    <small class="text-danger">-{{ $product->discount }}%</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $product->quantity > 0 ? 'bg-success' : 'bg-danger' }}">
                                    {{ $product->quantity }}
                                </span>
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input type="checkbox" 
                                           class="form-check-input" 
                                           {{ $product->is_active ? 'checked' : '' }}
                                           onchange="toggleStatus({{ $product->id }})">
                                </div>
                            </td>
                            <td>
                                <div class="d-flex gap-2 justify-content-end">
                                    @if(!$product->is_approve)
                                        @hasPermission('admin.product.approve')
                                            <a href="{{ route('admin.product.approve', $product->id) }}"
                                               class="btn btn-success btn-sm confirmApprove">
                                                <i class="fa-solid fa-check"></i> {{ __('Approve') }}
                                            </a>
                                        @endhasPermission
                                    @endif

                                    @hasPermission('admin.product.barcode')
                                        <a href="{{ route('admin.product.barcode', $product->id) }}"
                                            class="btn btn-outline-primary circleIcon" data-bs-toggle="tooltip"
                                            data-bs-placement="left" data-bs-title="{{ __('Generate Barcode') }}">
                                            <i class="bi bi-upc-scan"></i>
                                        </a>
                                    @endhasPermission

                                    @hasPermission('admin.product.edit')
                                        <a href="{{ route('admin.product.edit', $product->id) }}"
                                            class="btn btn-outline-primary circleIcon" data-bs-toggle="tooltip"
                                            data-bs-placement="left" data-bs-title="{{ __('Edit Product') }}">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                    @endhasPermission

                                    <a href="{{ route('admin.product.show', $product->id) }}" class="btn btn-outline-primary circleIcon" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="{{ __('View Product') }}">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>

                                    <button 
                                        class="btn btn-info btn-sm" 
                                        onclick="sendWhatsAppNotification({{ $product->id }})"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="left" 
                                        data-bs-title="{{ __('Send WhatsApp Notification') }}"
                                    >
                                        <i class="fab fa-whatsapp"></i>
                                    </button>

                                    <form action="{{ route('admin.product.destroy', $product->id) }}" 
                                          method="POST" 
                                          class="d-inline" 
                                          id="deleteForm-{{ $product->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                                onclick="confirmDelete({{ $product->id }})"
                                                class="btn btn-outline-danger circleIcon" 
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="left" 
                                                data-bs-title="{{ __('Delete Product') }}">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <img src="{{ asset('images/no-data.svg') }}" alt="No Data" width="120">
                                <div class="mt-3 text-muted">
                                    @if(request()->filled('status'))
                                        {{ request()->status == '0' ? __('No pending item requests') : __('No pending update requests') }}
                                    @elseif(request()->filled('approve'))
                                        {{ __('No approved items found') }}
                                    @else
                                        {{ __('No products found') }}
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $products->links() }}
    </div>

    <div class="modal fade" id="videoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="video-grid"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
<style>
.video-thumbnail {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.video-poster {
    position: absolute;
    top: 0;
    left: 0;
    cursor: pointer;
    transition: opacity 0.2s;
    width: 100%;
    height: 100%;
    object-fit: cover;
    background: rgba(0, 0, 0, 0.1);
}

.video-poster:hover {
    opacity: 0.8;
}

.table > :not(caption) > * > * {
    padding: 1rem 1rem;
}

.form-switch .form-check-input {
    width: 2.5em;
}

.btn-outline-primary:hover,
.btn-outline-info:hover,
.btn-outline-success:hover,
.btn-outline-danger:hover {
    color: white;
}

.video-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1rem;
    padding: 1rem;
}

.video-item {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    aspect-ratio: 16/9;
}

.cursor-pointer {
    cursor: pointer;
}

.video-item video {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.video-item .video-poster {
    display: flex;
    align-items: center;
    justify-content: center;
}

.video-item .video-poster::after {
    content: '\f144';
    font-family: 'Font Awesome 5 Free';
    font-size: 3rem;
    color: white;
    text-shadow: 0 2px 4px rgba(0,0,0,0.2);
    opacity: 0.8;
    transition: opacity 0.2s;
}

.video-item .video-poster:hover::after {
    opacity: 1;
}

.thumbnail-container {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.video-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

@media (max-width: 768px) {
    .video-grid {
        grid-template-columns: 1fr;
    }
    
    .modal-dialog {
        margin: 0.5rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
$(".confirmApprove").on("click", function(e) {
    e.preventDefault();
    const url = $(this).attr("href");
    Swal.fire({
        title: "{{ __('Are you sure?') }}",
        text: "{{ __('You want to approve this product') }}",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "{{ __('Yes, Approve it!') }}",
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
});

function confirmDelete(id) {
    Swal.fire({
        title: "{{ __('Are you sure?') }}",
        text: "{{ __('You want to delete this product! If you confirm, it will be deleted permanently.') }}",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ef4444",
        cancelButtonColor: "#64748b",
        confirmButtonText: "{{ __('Yes, Delete it!') }}"
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(`deleteForm-${id}`).submit();
        }
    });
}

const videoModal = new bootstrap.Modal(document.getElementById('videoModal'));

function showVideos(videos, productName) {
    const modalTitle = document.querySelector('#videoModal .modal-title');
    const videoGrid = document.querySelector('#videoModal .video-grid');
    
    modalTitle.textContent = `${productName} - Videos`;
    videoGrid.innerHTML = '';
    
    videos.forEach(video => {
        const videoItem = document.createElement('div');
        videoItem.className = 'video-item';
        
        videoItem.innerHTML = `
            <video controls playsinline>
                <source src="${video.src}" type="${video.type}">
                Your browser does not support the video tag.
            </video>
            ${video.thumbnail ? `
                <div class="video-poster" onclick="playVideo(this)">
                    <img src="${video.thumbnail}" alt="${video.title || 'Video thumbnail'}" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
            ` : ''}
        `;
        
        videoGrid.appendChild(videoItem);
    });
    
    videoModal.show();
}

function playVideo(posterElement) {
    posterElement.style.display = 'none';
    const video = posterElement.previousElementSibling;
    if (video) {
        try {
            const playPromise = video.play();
            if (playPromise !== undefined) {
                playPromise.catch(error => {
                    console.log("Video playback error:", error);
                });
            }
        } catch (error) {
            console.log("Error playing video:", error);
        }
    }
}

document.getElementById('videoModal').addEventListener('hidden.bs.modal', function () {
    const videos = document.querySelectorAll('#videoModal video');
    const posters = document.querySelectorAll('#videoModal .video-poster');
    
    videos.forEach(video => {
        video.pause();
        video.currentTime = 0;
    });
    
    posters.forEach(poster => {
        poster.style.display = 'flex';
    });
});

function toggleStatus(productId) {
    $.ajax({
        url: `{{ url('admin/products') }}/${productId}/toggle-status`,
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                toastr.success(response.message);
            } else {
                toastr.error(response.message);
            }
        },
        error: function() {
            toastr.error('{{ __("Something went wrong!") }}');
        }
    });
}

function sendWhatsAppNotification(productId) {
    if (confirm('Are you sure you want to send a WhatsApp notification for this product?')) {
        $.ajax({
            url: `/admin/products/${productId}/send-whatsapp`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                } else {
                    alert(response.message || 'Failed to send notification.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error sending notification:', error);
                alert('Failed to send notification. Please try again.');
            }
        });
    }
}

function showThumbnails(thumbnails, productName) {
    const modalTitle = document.querySelector('#videoModal .modal-title');
    const videoGrid = document.querySelector('#videoModal .video-grid');
    
    modalTitle.textContent = `${productName} - Thumbnails`;
    videoGrid.innerHTML = '';
    
    thumbnails.forEach(thumb => {
        const thumbItem = document.createElement('div');
        thumbItem.className = 'video-item';
        
        thumbItem.innerHTML = `
            <img src="${thumb.thumbnail}" 
                 alt="${productName}"
                 class="w-full h-full object-cover">
        `;
        
        videoGrid.appendChild(thumbItem);
    });
    
    videoModal.show();
}
</script>
@endpush
