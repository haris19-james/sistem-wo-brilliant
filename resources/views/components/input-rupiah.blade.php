@props([
    'name' => 'total_biaya',
    'label' => 'Total biaya',
    'value' => '',
    'required' => true,
    'placeholder' => '0',
    'id' => null,
])

@php
    $rawValue = $value !== '' && $value !== null
        ? (string) (int) round(\App\Support\MoneyParser::toFloat($value))
        : '';
    $displayValue = $rawValue !== ''
        ? \App\Support\MoneyParser::formatId($rawValue)
        : '';
    $inputId = $id ?? $name.'_display';
    $hiddenId = $name.'_raw';
@endphp

<div {{ $attributes->merge(['class' => 'space-y-1']) }} data-rupiah-input>
    @if($label)
    <label for="{{ $inputId }}" class="text-xs font-semibold text-gray-600 block">{{ $label }}</label>
    @endif
    <div class="flex rounded-lg border border-gray-200 overflow-hidden focus-within:ring-1 focus-within:ring-bottle focus-within:border-bottle bg-white">
        <span class="inline-flex items-center px-3 text-sm font-semibold text-gray-600 bg-gray-50 border-r border-gray-200 shrink-0">Rp</span>
        <input type="text"
               id="{{ $inputId }}"
               class="rupiah-input-display flex-1 min-w-0 px-3 py-2 text-sm outline-none border-0"
               inputmode="numeric"
               autocomplete="off"
               placeholder="{{ $placeholder }}"
               value="{{ $displayValue }}"
               @if($required) required aria-required="true" @endif
               pattern="[0-9.]*"
               title="Hanya angka. Contoh: ketik 1000000 untuk Rp 1.000.000">
        <input type="hidden"
               name="{{ $name }}"
               id="{{ $hiddenId }}"
               data-rupiah-value
               value="{{ $rawValue }}">
    </div>
    <p class="text-[10px] text-gray-500">Ketik angka saja — otomatis diformat (contoh: 1000000 → 1.000.000). Nilai disimpan tanpa pengali.</p>
</div>
