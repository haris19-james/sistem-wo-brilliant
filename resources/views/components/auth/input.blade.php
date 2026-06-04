@props([
    'label',
    'name',
    'type' => 'text',
    'value' => null,
    'required' => false,
    'autocomplete' => null,
])

<div>
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1.5">{{ $label }}</label>
    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ $value }}"
        @if($required) required @endif
        @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif
        {{ $attributes->merge(['class' => 'w-full border border-gray-200 rounded-xl px-4 py-2.5 text-gray-900 placeholder:text-gray-400 focus:ring-2 focus:ring-bottle/40 focus:border-bottle outline-none transition shadow-sm']) }}
    />
</div>
