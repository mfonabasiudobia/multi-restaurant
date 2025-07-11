@props(['label' => '', 'value' => ''])

<div>
    @if($label)
        <label class="form-label">{{ $label }}</label>
    @endif
    
    <textarea 
        {{ $attributes->merge(['class' => 'form-control ' . ($errors->has($attributes->get('name')) ? 'is-invalid' : '')]) }}
    >{{ $value }}</textarea>

    @error($attributes->get('name'))
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div> 