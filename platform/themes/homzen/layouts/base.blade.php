<!DOCTYPE html>
<html {!! Theme::htmlAttributes() !!}>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=5, user-scalable=1" name="viewport"/>

        <style>
            :root {
                --primary-color: {{ theme_option('primary_color', '#db1d23') }};
                --hover-color: {{ theme_option('hover_color', '#cd380f') }};
                --top-header-background-color: {{ theme_option('top_header_background_color', '#f7f7f7') }};
                --top-header-text-color: {{ theme_option('top_header_text_color', '#161e2d') }};
                --main-header-background-color: {{ theme_option('main_header_background_color', '#ffffff') }};
                --main-header-text-color: {{ theme_option('main_header_text_color', '#161e2d') }};
                --main-header-border-color: {{ theme_option('main_header_border_color', '#e4e4e4') }};
                --map-marker-icon-image: url({{ theme_option('map_marker_image') ? RvMedia::getImageUrl(theme_option('map_marker_image')) : Theme::asset()->url('images/map-icon.png') }});
            }
        </style>

        {!! Theme::header() !!}
        @stack('header')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    </head>

    <body {!! Theme::bodyAttributes() !!}>
        {!! apply_filters(THEME_FRONT_BODY, null) !!}

        <div id="wrapper">
            <div class="clearfix">
                @yield('content')
            </div>
        </div>

        {!! Theme::footer() !!}
        @stack('footer')
    </body>
</html>
