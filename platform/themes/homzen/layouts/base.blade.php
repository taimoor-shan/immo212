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
    </head>

    <body {!! Theme::bodyAttributes() !!}>
        {!! apply_filters(THEME_FRONT_BODY, null) !!}

        <div id="wrapper">
            <div class="clearfix">
                @yield('content')
            </div>
        </div>

        {{-- JavaScript Translations --}}
        @php
            $translations = [
                // Vacation Rental Calendar (HIGH PRIORITY) - Used in frontend-calendar.js
                'failed_load_calendar' => __('plugins/real-estate::vacation-rental.js.failed_load_calendar'),
                'select_dates' => __('plugins/real-estate::vacation-rental.js.select_dates'),
                'available' => __('plugins/real-estate::vacation-rental.js.available'),
                'booked' => __('plugins/real-estate::vacation-rental.js.booked'),
                'maintenance' => __('plugins/real-estate::vacation-rental.js.maintenance'),
                'check_in' => __('plugins/real-estate::vacation-rental.js.check_in'),
                'check_out' => __('plugins/real-estate::vacation-rental.js.check_out'),
                'nights' => __('plugins/real-estate::vacation-rental.js.nights'),
                'full_name' => __('plugins/real-estate::vacation-rental.js.full_name'),
                'email' => __('plugins/real-estate::vacation-rental.js.email'),
                'phone' => __('plugins/real-estate::vacation-rental.js.phone'),
                'guests' => __('plugins/real-estate::vacation-rental.js.guests'),
                'total' => __('plugins/real-estate::vacation-rental.js.total'),
                'terms_agreement' => __('plugins/real-estate::vacation-rental.js.terms_agreement'),
                'terms_and_conditions' => __('plugins/real-estate::vacation-rental.js.terms_and_conditions'),
                'send_inquiry' => __('plugins/real-estate::vacation-rental.js.send_inquiry'),
                'book_now' => __('plugins/real-estate::vacation-rental.js.book_now'),
                'dates_unavailable' => __('plugins/real-estate::vacation-rental.js.dates_unavailable'),
                'minimum_stay_prefix' => __('plugins/real-estate::vacation-rental.js.minimum_stay_prefix'),
                'maximum_stay_prefix' => __('plugins/real-estate::vacation-rental.js.maximum_stay_prefix'),
                'cleaning_fee' => __('plugins/real-estate::vacation-rental.js.cleaning_fee'),
                'service_fee' => __('plugins/real-estate::vacation-rental.js.service_fee'),
                'taxes' => __('plugins/real-estate::vacation-rental.js.taxes'),
                'select_dates_required' => __('plugins/real-estate::vacation-rental.js.select_dates_required'),
                'please_fill_required_fields' => __('plugins/real-estate::vacation-rental.js.please_fill_required_fields'),
                'terms_acceptance_required' => __('plugins/real-estate::vacation-rental.js.terms_acceptance_required'),

                // Additional Calendar Keys (not currently used in frontend-calendar.js but available)
                'calendar_element_not_found' => __('plugins/real-estate::vacation-rental.js.calendar_element_not_found'),
                'missing_availability_url' => __('plugins/real-estate::vacation-rental.js.missing_availability_url'),
                'failed_load_availability' => __('plugins/real-estate::vacation-rental.js.failed_load_availability'),
                'pricing_calculation_error' => __('plugins/real-estate::vacation-rental.js.pricing_calculation_error'),
                'login_required' => __('plugins/real-estate::vacation-rental.js.login_required'),
                'night' => __('plugins/real-estate::vacation-rental.js.night'),

                // Common UI (MEDIUM PRIORITY)
                'error' => __('plugins/real-estate::vacation-rental.js.error'),
                'success' => __('plugins/real-estate::vacation-rental.js.success'),
                'please_wait' => __('plugins/real-estate::vacation-rental.js.please_wait'),
                'loading' => __('plugins/real-estate::vacation-rental.js.loading'),
                'any' => __('plugins/real-estate::vacation-rental.js.any'),
                'select_price_range' => __('plugins/real-estate::vacation-rental.js.select_price_range'),

                // Form validation (MEDIUM PRIORITY)
                'invalid_email' => __('plugins/real-estate::vacation-rental.js.invalid_email'),
                'please_enter_email' => __('plugins/real-estate::vacation-rental.js.please_enter_email'),
                'please_enter_password' => __('plugins/real-estate::vacation-rental.js.please_enter_password'),

                // Calendar messages (MEDIUM PRIORITY)
                'please_select_dates' => __('plugins/real-estate::vacation-rental.js.please_select_dates'),
                'please_select_valid_dates' => __('plugins/real-estate::vacation-rental.js.please_select_valid_dates'),
                'failed_load_events' => __('plugins/real-estate::vacation-rental.js.failed_load_events'),
                'please_select_both_dates' => __('plugins/real-estate::vacation-rental.js.please_select_both_dates'),
                'failed_save_availability' => __('plugins/real-estate::vacation-rental.js.failed_save_availability'),

                // Notifications (MEDIUM PRIORITY)
                'error_loading_data' => __('plugins/real-estate::vacation-rental.js.error_loading_data'),
                'error_sending_message' => __('plugins/real-estate::vacation-rental.js.error_sending_message'),
                'error_subscribing' => __('plugins/real-estate::vacation-rental.js.error_subscribing'),
                'please_enter_email_address' => __('plugins/real-estate::vacation-rental.js.please_enter_email_address'),

                // Admin messages (LOW PRIORITY)
                'error_colon' => __('plugins/real-estate::vacation-rental.js.error_colon'),
                'success_colon' => __('plugins/real-estate::vacation-rental.js.success_colon'),
                'error_loading_language' => __('plugins/real-estate::vacation-rental.js.error_loading_language'),
                'please_select_language' => __('plugins/real-estate::vacation-rental.js.please_select_language')
            ];
        @endphp

        <script>
            window.translations = window.translations || {};

            // Global translations available to all JavaScript files
            window.translations = @json($translations);

            // Global translation helper function
            window.__ = function(key, replacements = {}) {
                let text = window.translations[key] || key;

                // Handle placeholder replacements like :min_stay, :max_stay
                for (let placeholder in replacements) {
                    text = text.replace(new RegExp(':' + placeholder, 'g'), replacements[placeholder]);
                }

                return text;
            };

            // Alternative helper for shorter syntax
            window.t = window.__;
        </script>

        {!! Theme::footer() !!}
        @stack('footer')
    </body>
</html>
