@php
    Theme::asset()->container('footer')->usePath()->add('lightbox-js', 'js/vendors/fancybox.umd.js');
    Theme::asset()->usePath()->add('lightbox-css', 'css/vendors/jquery.fancybox.min.css');

    $layout = MetaBox::getMetaData($vacationRental, 'layout', true);

    if (! $layout) {
        $layout = theme_option('vacation_rental_single_layout', 'style-4');
    }
@endphp

@includeIf(Theme::getThemeNamespace("views.real-estate.vacation-rental-single-layouts.{$layout}"), compact('vacationRental'))
