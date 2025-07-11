@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">{{ __('Logistics Management') }}</h3>
                        <a href="{{ route('admin.logistics.create') }}" class="btn btn-primary">
                            {{ __('Add New Entry') }}
                        </a>
                    </div>
                    <div class="card-body">
                        <!-- Search and Filter Form -->
                        <form action="{{ route('admin.logistics.index') }}" method="GET" class="mb-4">
                            <div class="row">
                                <div class="col-md-3">
                                    <input type="text" name="search" class="form-control" placeholder="{{ __('Search by Bag Number...') }}" value="{{ request('search') }}">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="location" class="form-control" placeholder="{{ __('Filter by Location...') }}" value="{{ request('location') }}">
                                </div>
                                <div class="col-md-3">
                                    <input type="number" name="row" class="form-control" placeholder="{{ __('Filter by Row...') }}" value="{{ request('row') }}">
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary">{{ __('Search') }}</button>
                                    <a href="{{ route('admin.logistics.index') }}" class="btn btn-secondary">{{ __('Reset') }}</a>
                                </div>
                            </div>
                        </form>

                        <!-- Logistics Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('ID') }}</th>
                                        <th>{{ __('Article Name') }}</th>
                                        <th>{{ __('Bag Number') }}</th>
                                        <th>{{ __('Location') }}</th>
                                        <th>{{ __('Row') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($logistics as $entry)
                                        <tr>
                                            <td>{{ $entry->id }}</td>
                                            <td>{{ $entry->article_name }}</td>
                                            <td>{{ $entry->bag_number }}</td>
                                            <td>{{ $entry->location }}</td>
                                            <td>{{ $entry->row }}</td>
                                            <td>
                                                <a href="{{ route('admin.logistics.edit', $entry) }}" class="btn btn-sm btn-info">
                                                    {{ __('Edit') }}
                                                </a>
                                                <form action="{{ route('admin.logistics.destroy', $entry) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('Are you sure you want to delete this entry?') }}')">
                                                        {{ __('Delete') }}
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">{{ __('No logistics entries found.') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $logistics->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 