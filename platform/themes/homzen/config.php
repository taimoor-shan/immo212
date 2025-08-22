<?php

use Botble\Base\Facades\BaseHelper;
use Botble\Shortcode\View\View;
use Botble\Theme\Theme;
use Illuminate\Support\Facades\Route;

return [

    'inherit' => null, //default

    'events' => [
        'beforeRenderTheme' => function (Theme $theme): void {
            $version = get_cms_version();

            $boostrapCss = BaseHelper::isRtlEnabled() ? 'bootstrap.rtl.min.css' : 'bootstrap.min.css';

            $theme->asset()->usePath()->add('bootstrap', "plugins/bootstrap/css/$boostrapCss");
            $theme->asset()->usePath()->add('animate', 'css/animate.min.css');
            $theme->asset()->usePath()->add('swiper', 'plugins/swiper/swiper-bundle.min.css');
            $theme->asset()->usePath()->add('flatpickr', 'css/plugins/flatpickr.min.css');
            $theme->asset()->usePath()->add('style', 'css/style.css', version: $version);

            // Load vacation rental calendar assets on relevant pages
            if (is_plugin_active('real-estate')) {
                $currentRoute = request()->route();
                $routeName = $currentRoute ? $currentRoute->getName() : '';
                $currentPath = request()->path();

                // Load on vacation rental pages, property detail pages, and booking pages
                $shouldLoadCalendar =
                    str_contains($routeName, 'vacation-rental') ||
                    str_contains($routeName, 'property.') ||
                    str_contains($currentPath, 'vacation-rental') ||
                    str_contains($currentPath, 'properties/') ||
                    str_contains($currentPath, 'booking');

                if ($shouldLoadCalendar) {
                    // Load Flatpickr JavaScript from CDN first
                    $theme->asset()->container('footer')->usePath(false)->add('flatpickr-js', 'https://cdn.jsdelivr.net/npm/flatpickr', ['jquery']);

                    // Load from public directory (will be created)
                    $cssPath = 'css/vacation-rental-calendar.css';
                    $jsPath = 'js/frontend-calendar.js';

                    $theme->asset()->usePath()->add('vacation-rental-calendar-css', $cssPath, version: $version);
                    $theme->asset()->container('footer')->usePath()->add('frontend-calendar-js', $jsPath, ['jquery', 'flatpickr-js'], version: $version);
                }
            }

            $theme->asset()->container('footer')->usePath()->add('popper', 'js/popper.min.js');
            $theme->asset()->container('footer')->usePath()->add('bootstrap', 'plugins/bootstrap/js/bootstrap.min.js');
            $theme->asset()->container('footer')->usePath()->add('jquery', 'js/jquery.min.js');
            $theme->asset()->container('footer')->usePath()->add('wow', 'js/wow.min.js');
            $theme->asset()->container('footer')->usePath()->add('swiper', 'plugins/swiper/swiper-bundle.min.js');

            $theme->asset()->container('footer')->usePath()->add('script', 'js/script.js', version: $version);

            if (is_plugin_active('social-login')) {
                $theme->asset()
                    ->usePath(false)
                    ->add(
                        'social-login-css',
                        asset('vendor/core/plugins/social-login/css/social-login.css'),
                        [],
                        [],
                        '1.1.1'
                    );
            }

            if (function_exists('shortcode')) {
                $theme->composer([
                    'page',
                    'post',
                    'career.career',
                    'real-estate.project',
                    'real-estate.property',
                ], function (View $view): void {
                    $view->withShortcodes();
                });
            }
        },
    ],
];
