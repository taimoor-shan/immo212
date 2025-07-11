@php
    $style = in_array($shortcode->style, range(1, 6)) ? $shortcode->style : 1;
@endphp

@include(Theme::getThemeNamespace("partials.shortcodes.services.styles.style-$style"))
