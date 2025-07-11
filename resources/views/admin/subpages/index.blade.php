@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Subpages</h2>
        <a href="{{ route('admin.subpages.create') }}" class="btn btn-primary">Add New Subpage</a>
        <table class="table mt-3">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Slug</th>
                    <th>Section</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($subpages as $subpage)
                    <tr>
                        <td>{{ $subpage->title }}</td>
                        <td>{{ $subpage->slug }}</td>
                        <td>{{ ucfirst($subpage->section) }}</td>
                        <td>
                            <a href="{{ route('admin.subpages.edit', $subpage) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('admin.subpages.destroy', $subpage) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                            <a href="{{ url('' . $subpage->slug) }}" class="btn btn-info btn-sm">View</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection