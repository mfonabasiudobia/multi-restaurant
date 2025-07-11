@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h4 class="m-0">{{ __('Edit Language') . '(' . $language->name . ')' }}</h4>
            <a href="{{ route('admin.language.index') }}" class="btn btn-danger">
                {{ __('Back') }}
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-12 mt-4">
            <div class="card-body">
                <form action="{{ route('admin.language.update', $language->id) }}" method="POST">
                    @csrf
                    @method('put')
                    <label for="" class="form-label fw-bold mb-1">
                        {{ __('Title') . ' for ' . $language->name . '' }}
                    </label>
                    <div class="input-group">
                        <input type="text" name="title" class="form-control py-2.5" placeholder="title"
                            value="{{ $language->title }}" />
                        <button type="submit" class="input-group-text btn btn-primary">
                            <i class="fa-solid fa-floppy-disk"></i>
                            {{ __('Update') }}
                        </button>
                    </div>
                    @error('title')
                        <p class="text-danger m-0">{{ $message }}</p>
                    @enderror
                </form>
            </div>
        </div>

        <!-- Add New Translation -->
        <div class="card border-0 shadow-sm rounded-12 mt-4">
            <div class="card-header">
                <h5 class="m-0">{{ __('Add New Translation') }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.language.translation.store', $language->id) }}" method="POST" id="addTranslationForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-5">
                            <input type="text" name="key" class="form-control" placeholder="{{ __('Key') }}" required>
                        </div>
                        <div class="col-md-5">
                            <input type="text" name="value" class="form-control" placeholder="{{ __('Translation') }}" required>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                {{ __('Add') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Translations -->
        <div class="card border-0 shadow-sm rounded-12 mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="m-0">{{ $language->name }}.json</h5>
                <div class="d-flex gap-2">
                    <input type="text" class="form-control" placeholder="{{ __('Search translations') }}" id="search" onkeyup="filterTranslations()">
                    <button type="button" class="btn btn-primary" onclick="saveAllTranslations()">
                        {{ __('Save All Changes') }}
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form id="translationsForm">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Key') }}</th>
                                    <th>{{ __('Translation') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($languageData as $key => $value)
                                    <tr class="translation-row">
                                        <td>
                                            <input type="text" class="form-control translation-key" value="{{ $key }}" readonly>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control translation-value" value="{{ $value }}">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm" onclick="deleteTranslation(this, '{{ $key }}')">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>

        <div class="row mb-4 mt-4">
            <div class="col-md-6">
                @hasPermission('admin.language.import')
                <div class="card shadow border-0 rounded-lg import-card">
                    <div class="card-body text-center">
                        <h4 class="text-dark mb-2 font-weight-bold">
                            {{ __('Select JSON File to Import') }}
                        </h4>
                        <p class="text-muted mb-3">
                            Upload a JSON file to update your language settings.
                        </p>

                        <form action="{{ route('admin.language.import', $language->id) }}" method="POST"
                            enctype="multipart/form-data" id="bulkForm">
                            @csrf

                            <!-- Enhanced Drop Zone -->
                            <div class="drop-zone mx-auto">
                                <span class="drop-zone__prompt">
                                    <div class="icon">
                                        <i class="fa-solid fa-cloud-arrow-up"></i>
                                    </div>
                                    {{ __('Drop file here or click to upload') }}
                                </span>
                                <input name="file" type="file" class="drop-zone__input" accept=".json">
                            </div>

                            <!-- File Input Error -->
                            @error('file')
                                <p class="text-danger m-0">{{ $message }}</p>
                            @enderror

                            <!-- Submit Button -->
                            <div id="galler" style="display: none">
                                <button type="submit" class="btn btn-primary btn-lg mt-4 py-2 px-4">
                                    {{ __('Update Language JSON') }}
                                    <i class="fa-solid fa-upload ml-2"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                @endhasPermission
            </div>

            <div class="col-md-6">
                @hasPermission('admin.language.export')
                <div class="card h-100 shadow border-0  rounded-lg export-card">
                    <div class="card-body text-center">
                        <div class="icon-container mb-3">
                            <i class="fa-solid fa-file-code fa-3x text-primary"></i>
                        </div>
                        <h4 class="text-dark mb-3 font-weight-bold">
                            {{ __('Export JSON File') }}
                        </h4>
                        <p class="text-muted">
                            Export your language files in JSON format with just one click!
                        </p>
                        <form action="{{ route('admin.language.export', $language->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-lg mt-4 export-btn py-2 px-4">
                                {{ __('Export') }}
                                <i class="fa-solid fa-cloud-arrow-down ml-2"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @endhasPermission
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-12 mb-3">
            <div class="card-header py-2.5">
                <h4 class="m-0">
                    {{ $language->name }}.json (file content)
                </h4>
            </div>
            <div class="card-body p-3">
                <div class="mb-2">
                    <input type="text" class="form-control" placeholder="Search in JSON" id="search" onkeyup="filterJSON()" />
                </div>
                <div class="json-view-container">
                    @foreach ($languageData as $key => $value)
                        <div class="json-item">
                            <span class="json-key">"{{ $key }}":</span>
                            <span class="json-value">{{ is_null($value) ? 'null' : '"' . $value . '"' }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
@endsection

@push('css')
    <style>
        .export-card {
            background-color: #f8f9fa;
            transition: transform 0.3s, box-shadow 0.3s;
            padding: 20px;
            border-radius: 15px;
        }

        .export-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .icon-container {
            background-color: #e7f3ff;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto;
        }

        h4 {
            color: #343a40;
        }

        .export-btn {
            display: inline-block;
            border-radius: 8px;
            transition: background-color 0.3s, box-shadow 0.3s;
        }

        .export-btn i {
            margin-left: 10px;
        }

        p {
            font-size: 14px;
            color: #6c757d;
        }

        .import-card {
            background-color: #f8f9fa;
            padding: 25px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 15px;
        }

        .import-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .json-view-container {
            background-color: #2d2d2d;
            color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            overflow-y: auto;
            max-height: 600px;
        }

        .json-item {
            padding: 8px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 14px;
        }

        .json-item:last-child {
            border-bottom: none;
        }

        .json-key {
            color: #66d9ef;
            font-weight: bold;
        }

        .json-value {
            color: #a6e22e;
        }

        .highlight {
            background-color: yellow;
            /* Highlight color */
            font-weight: bold;
            /* Make it bold */
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('assets/scripts/drop-zone.js') }}"></script>
    <script>
        $('input[name="file"]').change(function() {
            $('#galler').css('display', 'block');
        });

        function selectFolder(button, name) {
            var gallery = $('#bulkForm');

            var input = $('#input' + name);

            if (input.length) {
                input.remove();
                $(button).removeClass('active');
            } else {
                var element = document.createElement('input');
                element.type = 'hidden';
                element.name = 'folder[' + name + ']';
                element.value = name;
                element.id = 'input' + name;
                gallery.append(element);

                $(button).addClass('active');
            }
        }

        function submitForm() {
            $('#galleryModal').modal('hide');

            $('#bulkForm').submit();
        }
    </script>

    @if (session('successAlert'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Language Import Successful',
                text: "{{ session('successAlert') }}"
            });
        </script>
    @endif

    <script>
        let debounceTimer;

        function filterJSON() {
            clearTimeout(debounceTimer); // Clear the previous timer

            debounceTimer = setTimeout(() => {
                const searchInput = document.getElementById('search').value.toLowerCase();
                const jsonItems = document.querySelectorAll('.json-item');

                jsonItems.forEach(item => {
                    const key = item.querySelector('.json-key').innerText.toLowerCase();
                    const value = item.querySelector('.json-value').innerText.toLowerCase();
                    const keyElement = item.querySelector('.json-key');
                    const valueElement = item.querySelector('.json-value');

                    // Reset highlighting
                    keyElement.innerHTML = key; // Reset to original key
                    valueElement.innerHTML = value; // Reset to original value

                    // Check if the key or value includes the search string
                    let isMatch = false;
                    if (key.includes(searchInput) || value.includes(searchInput)) {
                        // Highlight matching text
                        if (key.includes(searchInput)) {
                            keyElement.innerHTML = key.replace(new RegExp(`(${searchInput})`, 'gi'), '<span class="highlight">$1</span>');
                            isMatch = true;
                        }
                        if (value.includes(searchInput)) {
                            valueElement.innerHTML = value.replace(new RegExp(`(${searchInput})`, 'gi'), '<span class="highlight">$1</span>');
                            isMatch = true;
                        }

                        item.style.display = 'flex'; // Show item
                    } else {
                        item.style.display = 'flex'; // Show all items
                    }
                });

                // Scroll to the first matching item
                const firstMatch = [...jsonItems].find(item => {
                    const key = item.querySelector('.json-key').innerText.toLowerCase();
                    const value = item.querySelector('.json-value').innerText.toLowerCase();
                    return key.includes(searchInput) || value.includes(searchInput);
                });

                if (firstMatch) {
                    firstMatch.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            }, 300); // Delay in milliseconds
        }
    </script>

    <script>
    function filterTranslations() {
        const searchText = $('#search').val().toLowerCase();
        $('.translation-row').each(function() {
            const key = $(this).find('.translation-key').val().toLowerCase();
            const value = $(this).find('.translation-value').val().toLowerCase();
            $(this).toggle(key.includes(searchText) || value.includes(searchText));
        });
    }

    function saveAllTranslations() {
        const translations = {};
        $('.translation-row').each(function() {
            const key = $(this).find('.translation-key').val();
            const value = $(this).find('.translation-value').val();
            translations[key] = value;
        });

        $.ajax({
            url: '{{ route("admin.language.translation.update", $language->id) }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                translations: translations
            },
            success: function(response) {
                Toast.fire({
                    icon: 'success',
                    title: response.message
                });
            },
            error: function(error) {
                Toast.fire({
                    icon: 'error',
                    title: 'Error saving translations'
                });
            }
        });
    }

    function deleteTranslation(button, key) {
        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("This translation will be removed") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '{{ __("Yes, delete it!") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $(button).closest('tr').remove();
                saveAllTranslations();
            }
        });
    }

    // Handle add translation form submission
    $('#addTranslationForm').on('submit', function(e) {
        e.preventDefault();
        const key = $(this).find('input[name="key"]').val();
        const value = $(this).find('input[name="value"]').val();
        
        // Add new row to table
        const newRow = `
            <tr class="translation-row">
                <td><input type="text" class="form-control translation-key" value="${key}" readonly></td>
                <td><input type="text" class="form-control translation-value" value="${value}"></td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteTranslation(this, '${key}')">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        $('tbody').append(newRow);
        
        // Clear form
        $(this).trigger('reset');
        
        // Save all translations
        saveAllTranslations();
    });
    </script>

@endpush
