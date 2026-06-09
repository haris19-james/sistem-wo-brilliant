@php
    $c = config('brilliant.colors');
    $extraColors = $extraColors ?? [];
    $fontSerif = $fontSerif ?? false;
@endphp
<link rel="stylesheet" href="{{ asset('css/brilliant-brand.css') }}">
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    bottle: @json($c['bottle']),
                    bottleHover: @json($c['bottle_hover']),
                    bottleBright: @json($c['bottle_bright']),
                    lime: @json($c['lime']),
                    leafSoft: @json($c['leaf_soft']),
                    leaf: @json($c['leaf']),
                    leafBg: @json($c['leaf_bg']),
                    ink: @json($c['ink']),
                    grayBox: @json($c['gray_box']),
                    golden: @json($c['golden']),
                    goldenHover: @json($c['golden_hover']),
                    goldenSoft: @json($c['golden_soft']),
                    @foreach($extraColors as $key => $value)
                    {{ $key }}: @json($value),
                    @endforeach
                },
                fontFamily: {
                    sans: ['Poppins', 'sans-serif'],
                    @if($fontSerif)
                    serif: ['Playfair Display', 'serif'],
                    @endif
                },
            }
        }
    }
</script>
