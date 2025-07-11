@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center">
        <h4>{{ __('Frontend Translations') }}</h4>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('Language') }}</th>
                            <th>{{ __('Total Translations') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(['en', 'ro'] as $locale)
                            <tr>
                                <td>{{ strtoupper($locale) }}</td>
                                <td>
                                    @php
                                        $path = base_path("lang/frontend/{$locale}.json");
                                        $count = 0;
                                        if (file_exists($path)) {
                                            $translations = json_decode(file_get_contents($path), true);
                                            $count = count($translations ?? []);
                                        }
                                    @endphp
                                    {{ $count }} {{ __('entries') }}
                                </td>
                                <td>
                                    <a href="{{ route('admin.frontend-translations.edit', $locale) }}" 
                                       class="btn btn-primary btn-sm">
                                        <i class="fa fa-edit"></i> {{ __('Edit') }}
                                    </a>
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