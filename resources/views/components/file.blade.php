@props(['label', 'name', 'preview' => null, 'required' => false])

<div class="form-group">
    <label class="form-label">
        {{ __($label) }}
        @if($required)
            <span class="text-danger">*</span>
        @endif
    </label>
    <input type="file" 
           name="{{ $name }}" 
           class="form-control @error($name) is-invalid @enderror"
           {{ $required ? 'required' : '' }}
           onchange="previewImage(this, '{{ $preview }}')"
           {{ $attributes }}
    >
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

@once
@push('scripts')
<script>
    function previewImage(input, previewId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(previewId).src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
@endonce
