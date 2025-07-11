@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Update Subpage</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.subpages.update', $subpage->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" name="title" id="title" class="form-control" value="{{ $subpage->title }}" required>
            </div>

            <div class="form-group mt-3">
                <label>Section</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="section" id="about" value="about" 
                        {{ $subpage->section == 'about' ? 'checked' : '' }} required>
                    <label class="form-check-label" for="about">About Us</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="section" id="information" value="information" 
                        {{ $subpage->section == 'information' ? 'checked' : '' }} required>
                    <label class="form-check-label" for="information">Information</label>
                </div>
            </div>

            <div class="form-group mt-3">
                <label for="slug">Slug (URL Path)</label>
                <input type="text" name="slug" id="slug" class="form-control" value="{{ $subpage->slug }}" required>
            </div>

            <div class="form-group mt-3">
                <label for="content">Content</label>
                <div id="editor" class="form-control" style="height: 400px;">{!! $subpage->content !!}</div>
                                <textarea name="content" id="description" class="d-none" required>{!! $subpage->content !!}</textarea>
            </div>

            <button type="submit" class="btn btn-success mt-3">Update Subpage</button>
            <a href="{{ route('admin.subpages.index') }}" class="btn btn-secondary mt-3">Cancel</a>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const quill = new Quill('#editor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                        [{ 'font': [] }],
                        ['bold', 'italic', 'underline', 'strike', 'blockquote'],
                        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                        [{ 'align': [] }],
                        [{ 'script': 'sub' }, { 'script': 'super' }],
                        [{ 'indent': '-1' }, { 'indent': '+1' }],
                        [{ 'direction': 'rtl' }],
                        [{ 'color': [] }, { 'background': [] }],
                        ['link', 'image', 'video', 'formula']
                    ]
                }
            });

            quill.on('text-change', function(delta, oldDelta, source) {
                document.getElementById('description').value = quill.root.innerHTML;
            });
        });
    </script>
@endpush