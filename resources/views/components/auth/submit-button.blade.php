@props(['text' => 'Masuk'])

<button type="submit" {{ $attributes->merge(['class' => 'w-full flex items-center justify-center bg-bottle text-white font-semibold py-3 rounded-xl shadow-sm hover:bg-bottleHover active:scale-[0.99] transition']) }}>
    {{ $text }}
</button>
