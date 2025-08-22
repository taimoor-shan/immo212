@if (theme_option('real_estate_enable_filter_by_price', 'yes') == 'yes')
    @if(isset($useDropdown) && $useDropdown)
        {{-- Dropdown version for main filter display --}}
        <div @class(['box-select form-search-price form-style', $class ?? null])>
            <!-- <label for="price-range" class="title-select text-variant-1">{{ __('Price Range') }}</label> -->
            <div class="price-dropdown-select" data-bb-toggle="price-dropdown">
                <!-- <div class="nice-select price-select-trigger">
                    <span class="current">
                        {{-- @if(request()->float('min_price') || request()->float('max_price'))
                            @if(request()->float('min_price'))
                                {{ format_price(request()->float('min_price')) }}
                            @else
                                {{ __('Any') }}
                            @endif
                            -
                            @if(request()->float('max_price'))
                                {{ format_price(request()->float('max_price')) }}
                            @else
                                {{ __('Any') }}
                            @endif
                        @else
                            {{ __('Select Price Range') }}
                        @endif --}}
                    </span>
                </div> -->
                <div class="price-dropdown-menu">
                    <div class="price-inputs-container">
                        <div class="price-input-row d-flex gap-3">
                            <div class="price-input-col">
                                <label for="min_price_dropdown">{{ __('Min Price') }}</label>
                                <input type="number"
                                       name="min_price"
                                       id="min_price_dropdown"
                                       class="form-control"
                                       placeholder="{{ format_price(100) }}"
                                       value="{{ BaseHelper::stringify(request()->float('min_price')) }}"
                                       min="100"
                                       step="100">
                            </div>
                            <div class="price-input-col">
                                <label for="max_price_dropdown">{{ __('Max Price') }}</label>
                                <input type="number"
                                       name="max_price"
                                       id="max_price_dropdown"
                                       class="form-control"
                                       placeholder="{{ format_price(1000) }}"
                                       value="{{ BaseHelper::stringify(request()->float('max_price')) }}"
                                       min="1000"
                                       step="100">
                            </div>
                        </div>
                        <!-- <div class="price-actions">
                            <button type="button" class="btn-clear">{{ __('Clear') }}</button>
                            <button type="button" class="btn-apply">{{ __('Apply') }}</button>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- Original slider version for advanced search --}}
        <!-- <div @class(['widget-price form-search-price', $class ?? null]) data-bb-toggle="range" data-min="1000000" data-max="{{ $maxPrice ?? get_max_properties_price() }}">
            <div class="box-title-price">
                <span class="title-price">{{ __('Price Range') }}</span>
                <div class="caption-price">
                    <span>{{ __('from') }}</span>
                    <span data-bb-toggle="range-from-value" class="fw-6 ms-1 me-1"></span>
                    <span>{{ __('to') }}</span>
                    <span data-bb-toggle="range-to-value" class="fw-6 ms-1"></span>
                </div>
            </div>
            <div data-bb-toggle="range-slider" data-currency-prefix-symbol="{{ get_application_currency()->is_prefix_symbol }}" data-currency-symbol="{{ get_application_currency()->symbol }}" data-currency-with-space="{{ (setting('real_estate_add_space_between_price_and_currency', 0) == 1) }}"></div>
            <div class="slider-labels">
                <div>
                    <input type="hidden" data-bb-toggle="min-input" name="min_price" value="{{ BaseHelper::stringify(request()->float('min_price')) }}" />
                    <input type="hidden" data-bb-toggle="max-input" name="max_price" value="{{ BaseHelper::stringify(request()->float('max_price')) }}" />
                </div>
            </div>
        </div> -->
    @endif
@endif
