@php
    Theme::asset()->container('footer')->usePath()->add('nouislider', 'js/nouislider.min.js');
    Theme::asset()->container('footer')->usePath()->add('wnumb', 'js/wNumb.min.js');
    Theme::asset()->container('footer')->usePath()->add('nice-select', 'js/jquery.nice-select.min.js');

    $style ??= 1;
@endphp

@if($style === 'sidebar')
    {{-- Sidebar uses the modern filter design --}}
    @include(Theme::getThemeNamespace('views.real-estate.partials.filters.vacation-rental-base'), ['style' => 3])
@else
    {{-- Main search box uses the modern filter design --}}
    @include(Theme::getThemeNamespace('views.real-estate.partials.filters.vacation-rental-base'))
@endif
