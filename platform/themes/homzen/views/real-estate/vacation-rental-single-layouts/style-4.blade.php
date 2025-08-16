<section class="pb-5 flat-property-detail-v4">
    <div class="container-fluid bg-surface stickyPropHeader">
        <div class="container">
            {!! apply_filters('ads_render', null, 'detail_page_before') !!}
            @include(Theme::getThemeNamespace('views.real-estate.vacation-rental-single-layouts.partials.header'))

        </div>
    </div>
    <div class="container-fluid">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-xl-8">
                @include(Theme::getThemeNamespace('views.real-estate.vacation-rental-single-layouts.partials.gallery-grid'))
                {!! apply_filters('before_single_content_detail', null, $vacationRental) !!}

                @include(Theme::getThemeNamespace('views.real-estate.vacation-rental-single-layouts.partials.description'), ['class' => 'single-property-element'])

                @include(Theme::getThemeNamespace('views.real-estate.vacation-rental-single-layouts.partials.vacation-rental-info'), ['class' => 'single-property-element'])

                @include(Theme::getThemeNamespace('views.real-estate.vacation-rental-single-layouts.partials.video'), ['class' => 'single-property-element'])

                @include(Theme::getThemeNamespace('views.real-estate.vacation-rental-single-layouts.partials.features'), ['class' => 'single-property-element'])

                @include(Theme::getThemeNamespace('views.real-estate.vacation-rental-single-layouts.partials.facilities'), ['class' => 'single-property-element'])

                @include(Theme::getThemeNamespace('views.real-estate.vacation-rental-single-layouts.partials.floor-plans'), ['class' => 'single-property-element'])

                @include(Theme::getThemeNamespace('views.real-estate.vacation-rental-single-layouts.partials.project'), ['class' => 'single-property-element'])

                @include(Theme::getThemeNamespace('views.real-estate.vacation-rental-single-layouts.partials.map'), ['class' => 'single-property-element'])

                {!! apply_filters('after_single_content_detail', null, $vacationRental) !!}

                <div class="wrapper-onepage">
                    @include(Theme::getThemeNamespace('views.real-estate.partials.social-sharing'), ['model' => $vacationRental])
                </div>

                {!! apply_filters(
                    BASE_FILTER_PUBLIC_COMMENT_AREA,
                    null,
                    $vacationRental
                ) !!}

                @include(Theme::getThemeNamespace('views.real-estate.vacation-rental-single-layouts.partials.reviews'), ['model' => $vacationRental, 'class' => 'single-property-element'])
            </div>
            <div class="col-lg-4 col-xl-4">
                <div class="widget-sidebar wrapper-sidebar-right">
                    {!! apply_filters('ads_render', null, 'detail_page_sidebar_before') !!}

                    {{-- Vacation rental booking form --}}
                    @include(Theme::getThemeNamespace('views.real-estate.vacation-rental-single-layouts.partials.vacation-rental-booking'), ['class' => 'bg-surface'])
                    
                    {{-- Contact form for vacation rentals --}}
                    @include(Theme::getThemeNamespace('views.real-estate.vacation-rental-single-layouts.partials.contact'), ['class' => 'bg-surface'])
                    
                    {!! apply_filters('ads_render', null, 'detail_page_sidebar_after') !!}
                </div>
            </div>
        </div>
        {!! apply_filters('ads_render', null, 'detail_page_after') !!}
    </div>
    </div>
</section>

@include(Theme::getThemeNamespace('views.real-estate.vacation-rental-single-layouts.partials.related-vacation-rentals'))
