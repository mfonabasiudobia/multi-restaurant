@props(['label', 'name', 'type' => 'text', 'placeholder' => '', 'required' => false, 'value' => null])

<div class="form-group">
    <label for="{{ $name }}" class="form-label">
        {{ __($label) }}
        @if($required)
            <span class="text-danger">*</span>
        @endif
    </label>
    <input type="{{ $type }}" 
           name="{{ $name }}" 
           id="{{ $name }}"
           class="form-control @error($name) is-invalid @enderror"
           placeholder="{{ __($placeholder) }}"
           value="{{ old($name, $value) }}"
           {{ $required ? 'required' : '' }}
           {{ $attributes }}
    >
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
