<div @class(['wd-find-select position-relative' =>  in_array($style, [1, 2, 4]), 'wd-filter-select' => $style === 3, 'no-left-round' => $noLeftRound ?? false])>
    <div class="inner-group">
        {{-- Vacation rental specific filter order: Cities, Categories, Price Range, Guests --}}
        @include(Theme::getThemeNamespace('views.real-estate.partials.filters.location'))
        @include(Theme::getThemeNamespace('views.real-estate.partials.filters.categories'))
        @include(Theme::getThemeNamespace('views.real-estate.partials.filters.price'), ['useDropdown' => true])
        @include(Theme::getThemeNamespace('views.real-estate.partials.filters.vacation-rental-guests'))

        @if (theme_option('real_estate_enable_advanced_search', 'yes') == 'yes')
            <div @class(['form-group-4 box-filter', 'form-style' => $style === 3])>
                <a class="filter-advanced pull-right" href="#"
                   data-filter-text-default="{{ __('More Filters') }}"
                   data-filter-text-active="{{ __('Hide Filters') }}"
                   role="button"
                   aria-expanded="false"
                   aria-label="{{ __('More Filters') }}"
                   aria-controls="advanced-search-form">
                    <span class="filter-text">{{ __('More Filters') }}</span>
                    <i class="icon-arr-down filter-icon"></i>
                </a>
            </div>
        @endif
    </div>
    @if($style === 3)
        <div class="form-style">
    @endif
    <button type="submit" class="tf-btn primary">{{ __('Search Vacation Rentals') }}</button>
    @if($style === 3)
        </div>
    @endif
</div>
