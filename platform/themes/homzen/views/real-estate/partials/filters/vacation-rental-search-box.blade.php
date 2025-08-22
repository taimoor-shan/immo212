@php
    Theme::asset()->container('footer')->usePath()->add('nouislider', 'js/nouislider.min.js');
    Theme::asset()->container('footer')->usePath()->add('wnumb', 'js/wNumb.min.js');
    Theme::asset()->container('footer')->usePath()->add('nice-select', 'js/jquery.nice-select.min.js');

    $style ??= 1;
@endphp

@if($style === 'sidebar')
    <div class="flat-tab flat-tab-form widget-filter-search widget-box bg-surface">
        <div class="h7 title fw-6">{{ __('Search Vacation Rentals') }}</div>
        <div class="form-sl">
            <div class="wd-filter-select">
                <div class="inner-group inner-filter">
                    {{-- Vacation rental specific filters --}}
                    @include(Theme::getThemeNamespace('views.real-estate.partials.filters.location'), ['style' => 3])
                    @include(Theme::getThemeNamespace('views.real-estate.partials.filters.categories'))
                    @include(Theme::getThemeNamespace('views.real-estate.partials.filters.price'), ['class' => 'form-style', 'useDropdown' => true])
                    @include(Theme::getThemeNamespace('views.real-estate.partials.filters.vacation-rental-guests'), ['class' => 'form-style'])

                    {{-- Advanced filters section --}}
                    @include(Theme::getThemeNamespace('views.real-estate.partials.filters.vacation-rental-stay'), ['class' => 'form-style'])
                    @include(Theme::getThemeNamespace('views.real-estate.partials.filters.vacation-rental-max-stay'), ['class' => 'form-style'])
                    @include(Theme::getThemeNamespace('views.real-estate.partials.filters.bedroom'), ['class' => 'form-style'])
                    @include(Theme::getThemeNamespace('views.real-estate.partials.filters.bathroom'), ['class' => 'form-style'])

                    <div class="form-style btn-show-advanced">
                        <a class="filter-advanced pull-right" href="#">
                            <x-core::icon name="ti ti-adjustments-alt" />
                            <span class="text-advanced">{{ __('Advanced') }}</span>
                        </a>
                    </div>
                    <div class="form-style wd-amenities">
                        @include(Theme::getThemeNamespace('views.real-estate.partials.filters.features'), ['asGrid' => false])
                    </div>
                    <div class="form-style btn-hide-advanced">
                        <a class="filter-advanced pull-right" href="#">
                            <x-core::icon name="ti ti-adjustments-alt" />
                            <span class="text-advanced">{{ __('Hide Advanced') }}</span>
                        </a>
                    </div>
                    <div class="form-style mt-5">
                        <div class="d-flex gap-2">
                            <button type="submit" class="tf-btn primary flex-fill">{{ __('Find Vacation Rentals') }}</button>
                            <button type="button" class="tf-btn outline-primary" onclick="resetVacationRentalFilters()">
                                <i class="fas fa-undo-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    <div @class(['flat-tab flat-tab-form', $class ?? null])>
        <div class="form-sl">
            @include(Theme::getThemeNamespace('views.real-estate.partials.filters.vacation-rental-base'))
            <div class="wd-search-form">
                <div class="grid-2 group-box group-price">
                    @include(Theme::getThemeNamespace('views.real-estate.partials.filters.price'))
                    @include(Theme::getThemeNamespace('views.real-estate.partials.filters.vacation-rental-guests'))
                </div>
                <div class="group-box">
                    <div class="group-select grid-4">
                        @include(Theme::getThemeNamespace('views.real-estate.partials.filters.vacation-rental-stay'))
                        @include(Theme::getThemeNamespace('views.real-estate.partials.filters.vacation-rental-max-stay'))
                        @include(Theme::getThemeNamespace('views.real-estate.partials.filters.bedroom'))
                        @include(Theme::getThemeNamespace('views.real-estate.partials.filters.bathroom'))
                    </div>
                </div>
                @include(Theme::getThemeNamespace('views.real-estate.partials.filters.features'))

                <div class="d-flex gap-2 mt-5">
                    <button type="submit" class="tf-btn primary form-search-box-offcanvas-button flex-fill">{{ __('Find Vacation Rentals') }}</button>
                    <button type="button" class="tf-btn outline-primary" onclick="resetVacationRentalFilters()">
                        <i class="fas fa-undo-alt me-1"></i>
                        {{ __('Reset') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
