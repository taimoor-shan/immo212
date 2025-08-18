<section class="pb-5 flat-vacation-rental-detail-v4">
    <div class="container-fluid bg-surface stickyVacationRentalHeader">
        <div class="container">
            {!! apply_filters('ads_render', null, 'vacation_rental_detail_page_before') !!}
            @include(Theme::getThemeNamespace('views.real-estate.single-layouts.partials.vacation-rental-header'))
        </div>
    </div>
    <div class="container-fluid">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-xl-8">
                    @include(Theme::getThemeNamespace('views.real-estate.single-layouts.partials.vacation-rental-gallery'))
                    {!! apply_filters('before_single_content_detail', null, $vacationRental) !!}

                    @include(Theme::getThemeNamespace('views.real-estate.single-layouts.partials.vacation-rental-description'), ['class' => 'single-vacation-rental-element'])

                    @include(Theme::getThemeNamespace('views.real-estate.single-layouts.partials.vacation-rental-features'), ['class' => 'single-vacation-rental-element'])

                    @include(Theme::getThemeNamespace('views.real-estate.single-layouts.partials.vacation-rental-facilities'), ['class' => 'single-vacation-rental-element'])

                    @include(Theme::getThemeNamespace('views.real-estate.single-layouts.partials.vacation-rental-info'), ['class' => 'single-vacation-rental-element'])

                    @include(Theme::getThemeNamespace('views.real-estate.single-layouts.partials.vacation-rental-map'), ['class' => 'single-vacation-rental-element'])

                    {!! apply_filters('after_single_content_detail', null, $vacationRental) !!}

                </div>
                <div class="col-lg-4 col-xl-4">
                    <div class="widget-sidebar wrapper-sidebar-right">
                        {!! apply_filters('ads_render', null, 'vacation_rental_detail_page_sidebar_before') !!}

                        @include(Theme::getThemeNamespace('views.real-estate.single-layouts.partials.vacation-rental-booking'), ['class' => 'bg-surface'])

                        @include(Theme::getThemeNamespace('views.real-estate.single-layouts.partials.vacation-rental-contact'), ['class' => 'bg-surface'])

                        {!! apply_filters('ads_render', null, 'vacation_rental_detail_page_sidebar_after') !!}
                    </div>
                </div>
            </div>
            {!! apply_filters('ads_render', null, 'vacation_rental_detail_page_after') !!}
        </div>
    </div>
</section>

