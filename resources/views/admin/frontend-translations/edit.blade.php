@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center">
        <h4>{{ __('Edit Frontend Translations') }} ({{ strtoupper($locale) }})</h4>
        <a href="{{ route('admin.frontend-translations.index') }}" class="btn btn-danger">
            {{ __('Back') }}
        </a>
    </div>

    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between">
            <h5 class="m-0">{{ __('Translations') }}</h5>
            <div class="d-flex gap-2">
                <input type="text" class="form-control" id="searchTranslation" placeholder="{{ __('Search...') }}">
                <button type="button" class="btn btn-primary" id="saveAllBtn">
                    {{ __('Save All Changes') }}
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Add New Translation Form -->
            <form id="addTranslationForm" class="mb-4">
                <div class="row">
                    <div class="col-md-5">
                        <input type="text" name="key" class="form-control" placeholder="{{ __('Key') }}" required>
                    </div>
                    <div class="col-md-5">
                        <input type="text" name="value" class="form-control" placeholder="{{ __('Translation') }}" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-success w-100" id="addBtn">
                            {{ __('Add New') }}
                        </button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('Key') }}</th>
                            <th>{{ __('Translation') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody id="translationsTable">
                        @php
                            $path = base_path("lang/frontend/{$locale}.json");
                            $translations = [];
                            if (file_exists($path)) {
                                $translations = json_decode(file_get_contents($path), true) ?? [];
                            }
                        @endphp
                        @foreach($translations as $key => $value)
                            <tr class="translation-row">
                                <td>
                                    <input type="text" class="form-control translation-key" value="{{ $key }}" readonly>
                                </td>
                                <td>
                                    <input type="text" class="form-control translation-value" value="{{ $value }}">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm delete-translation" data-key="{{ $key }}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Add New Translation
    $('#addTranslationForm').on('submit', function(e) {
        e.preventDefault();
        const addBtn = $('#addBtn');
        const originalText = addBtn.html();
        addBtn.html('<i class="fa fa-spinner fa-spin"></i> Adding...');
        addBtn.prop('disabled', true);

        const key = $(this).find('input[name="key"]').val();
        const value = $(this).find('input[name="value"]').val();
        
        // Add new row to table
        const newRow = `
            <tr class="translation-row">
                <td><input type="text" class="form-control translation-key" value="${key}" readonly></td>
                <td><input type="text" class="form-control translation-value" value="${value}"></td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm delete-translation" data-key="${key}">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#translationsTable').append(newRow);
        
        // Save all translations
        saveAllTranslations().then(() => {
            // Clear form
            $(this).trigger('reset');
            addBtn.html(originalText);
            addBtn.prop('disabled', false);
        }).catch(() => {
            addBtn.html(originalText);
            addBtn.prop('disabled', false);
        });
    });

    // Save All Translations
    $('#saveAllBtn').on('click', function() {
        saveAllTranslations();
    });

    // Delete Translation
    $(document).on('click', '.delete-translation', function() {
        const key = $(this).data('key');
        deleteTranslation(key, $(this).closest('tr'));
    });

    // Search Translations
    $('#searchTranslation').on('keyup', function() {
        const value = $(this).val().toLowerCase();
        $('.translation-row').filter(function() {
            const text = $(this).text().toLowerCase();
            $(this).toggle(text.indexOf(value) > -1);
        });
    });
});

function saveAllTranslations() {
    const saveBtn = $('#saveAllBtn');
    const originalText = saveBtn.html();
    saveBtn.html('<i class="fa fa-spinner fa-spin"></i> Saving...');
    saveBtn.prop('disabled', true);

    const translations = {};
    $('.translation-row').each(function() {
        const key = $(this).find('.translation-key').val();
        const value = $(this).find('.translation-value').val();
        translations[key] = value;
    });

    return $.ajax({
        url: '{{ route("admin.frontend-translations.update", $locale) }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            translations: translations
        },
        success: function(response) {
            Toast.fire({
                icon: 'success',
                title: '{{ __("Translations updated successfully") }}'
            });
        },
        error: function(error) {
            Toast.fire({
                icon: 'error',
                title: '{{ __("Error updating translations") }}'
            });
        },
        complete: function() {
            saveBtn.html(originalText);
            saveBtn.prop('disabled', false);
        }
    });
}

function deleteTranslation(key, row) {
    if (confirm('{{ __("Are you sure you want to delete this translation?") }}')) {
        $.ajax({
            url: '{{ route("admin.frontend-translations.destroy", [$locale, ""]) }}/' + key,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                row.remove();
                Toast.fire({
                    icon: 'success',
                    title: '{{ __("Translation deleted successfully") }}'
                });
            },
            error: function(error) {
                Toast.fire({
                    icon: 'error',
                    title: '{{ __("Error deleting translation") }}'
                });
            }
        });
    }
}
</script>
@endpush 