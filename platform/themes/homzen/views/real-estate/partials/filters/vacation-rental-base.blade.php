{{-- New 3-row vacation rental filter design --}}
<div class="vacation-rental-filter-modern @if($style === 3) sidebar-style @endif">
    {{-- Row 1: Property Types (like tabs in image) --}}
    <div class="vr-filter-row vr-property-types-row">
        @php
            // Get vacation rental specific categories (excludes Land, Commercial, etc.)
            $vacationRentalCategories = get_vacation_rental_categories([
                'indent' => '',
                'conditions' => ['status' => \Botble\Base\Enums\BaseStatusEnum::PUBLISHED],
            ]);

            // Build property types array starting with "All"
            $propertyTypes = ['' => __('All Accommodation')];
            foreach($vacationRentalCategories as $category) {
                $propertyTypes[$category->getKey()] = $category->name;
            }

            $currentType = request()->query('category_id');
        @endphp

        <div class="vr-property-type-tabs justify-content-start justify-content-lg-between">
            {{-- Property Type Tabs --}}
            @foreach($propertyTypes as $value => $label)
                <button type="button"
                        class="vr-property-tab @if($currentType == $value || (!$currentType && $value === '')) active @endif"
                        data-property-type="{{ $value }}"
                        onclick="selectPropertyType('{{ $value }}')">
                    {{ $label }}
                </button>
            @endforeach
        </div>
        <input type="hidden" name="category_id" id="selected_category_id" value="{{ $currentType }}">
    </div>

    {{-- Row 2: Location, Check-in, Check-out, Participants --}}
    <div class="vr-filter-row vr-main-filters-row">
        <div class="vr-filter-grid">
            {{-- Location --}}
            <div class="vr-filter-field vr-location-field">
                <label class="vr-field-label">{{ __('Location') }}</label>
                <div class="vr-field-input">
                    @if (is_plugin_active('location'))
                        <div class="group-select">
                            <select name="city_id" id="location" class="vr-input vr-select select_js" data-bb-toggle="select-dropdown">
                                <option value="">{{ __('Type the destination') }}</option>
                                @if (request()->query('city_id'))
                                    @php
                                        $selectedCity = \Botble\Location\Models\City::query()
                                            ->wherePublished()
                                            ->where('id', request()->query('city_id'))
                                            ->first();
                                    @endphp
                                    @if ($selectedCity)
                                        <option value="{{ $selectedCity->getKey() }}" selected>{{ $selectedCity->name }}</option>
                                    @endif
                                @endif
                            </select>
                        </div>
                    @else
                        <input type="text"
                               class="vr-input"
                               placeholder="{{ __('Type the destination') }}"
                               value="{{ BaseHelper::stringify(request()->query('location')) }}"
                               name="location"
                               data-url="{{ route('public.ajax.cities') }}"
                               data-bb-toggle="search-suggestion" />
                        <div data-bb-toggle="data-suggestion"></div>
                    @endif
                    <x-core::icon name="ti ti-current-location" class="vr-field-icon" />
                </div>
            </div>

            {{-- Check In --}}
            <div class="vr-filter-field vr-checkin-field">
                <label class="vr-field-label">{{ __('Check In') }}</label>
                <div class="vr-field-input">
                    <input type="date"
                           name="check_in_date"
                           id="check_in_date"
                           class="vr-input vr-date-input"
                           value="{{ request()->input('check_in_date') }}"
                           min="{{ date('Y-m-d') }}"
                           placeholder="{{ __('Add date') }}">
                    <x-core::icon name="ti ti-calendar" class="vr-field-icon" />
                </div>
            </div>

            {{-- Check Out --}}
            <div class="vr-filter-field vr-checkout-field">
                <label class="vr-field-label">{{ __('Check Out') }}</label>
                <div class="vr-field-input">
                    <input type="date"
                           name="check_out_date"
                           id="check_out_date"
                           class="vr-input vr-date-input"
                           value="{{ request()->input('check_out_date') }}"
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                           placeholder="{{ __('Add date') }}">
                    <x-core::icon name="ti ti-calendar" class="vr-field-icon" />
                </div>
            </div>

            {{-- Participants/Guests (as input instead of dropdown) --}}
            <div class="vr-filter-field vr-guests-field">
                <label class="vr-field-label">{{ __('Participant') }}</label>
                <div class="vr-field-input">
                    <input type="number"
                           name="maximum_guests"
                           id="maximum_guests"
                           class="vr-input vr-number-input"
                           value="{{ request()->input('maximum_guests') }}"
                           min="1"
                           max="20"
                           placeholder="{{ __('Add guests') }}">
                    <x-core::icon name="ti ti-users" class="vr-field-icon" />
                </div>
            </div>
        </div>
    </div>

    {{-- Row 3: Action Buttons (Reset + Advanced + Search) --}}
    <div class="vr-filter-row vr-actions-row">
        <div class="vr-action-buttons">
            {{-- Reset Button (replaces /Night) --}}
            <button type="button" class="vr-action-btn vr-reset-btn" onclick="resetVacationRentalFilters()">
                <x-core::icon name="ti ti-refresh" class="vr-btn-icon" />
                <span>{{ __('Reset') }}</span>
            </button>

            {{-- Advanced Filter Button (replaces /Hour) --}}
            @if (theme_option('real_estate_enable_advanced_search', 'yes') == 'yes')
                <button type="button" class="vr-action-btn vr-advanced-btn"
                        data-filter-text-default="{{ __('Advanced') }}"
                        data-filter-text-active="{{ __('Hide Advanced') }}"
                        onclick="toggleAdvancedFilters()"
                        aria-expanded="false"
                        aria-controls="vr-advanced-filters">
                    <x-core::icon name="ti ti-adjustments-alt" class="vr-btn-icon" />
                    <span class="vr-advanced-text">{{ __('Advanced') }}</span>
                </button>
            @endif

            {{-- Search Button --}}
            <button type="submit" class="vr-search-btn">
                <x-core::icon name="ti ti-search" class="vr-search-icon" />
                <span>{{ __('Search Accommodation') }}</span>
            </button>
        </div>
    </div>

    {{-- Advanced Filters Panel (hidden by default) --}}
    <div class="vr-advanced-filters" id="vr-advanced-filters" style="display: none;">
        <div class="vr-advanced-content">
            <div class="vr-advanced-grid">
                {{-- Price Range --}}
                @include(Theme::getThemeNamespace('views.real-estate.partials.filters.price'), ['class' => 'vr-advanced-field', 'useDropdown' => true])

                {{-- Min Stay --}}
                @include(Theme::getThemeNamespace('views.real-estate.partials.filters.vacation-rental-stay'), ['class' => 'vr-advanced-field'])

                {{-- Max Stay --}}
                @include(Theme::getThemeNamespace('views.real-estate.partials.filters.vacation-rental-max-stay'), ['class' => 'vr-advanced-field'])

                {{-- Bedrooms --}}
                @include(Theme::getThemeNamespace('views.real-estate.partials.filters.bedroom'), ['class' => 'vr-advanced-field'])

                {{-- Bathrooms --}}
                {{-- @include(Theme::getThemeNamespace('views.real-estate.partials.filters.bathroom'), ['class' => 'vr-advanced-field']) --}}
            </div>

            {{-- Features --}}
            <div class="vr-features-section">
                @include(Theme::getThemeNamespace('views.real-estate.partials.filters.features'), ['class' => 'vr-features-grid'])
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Handle date validation for vacation rental filters
    const $checkInDate = $('#check_in_date');
    const $checkOutDate = $('#check_out_date');
    const $form = $checkInDate.closest('form');

    // Update checkout date minimum when checkin date changes
    $checkInDate.on('change', function() {
        const checkInValue = $(this).val();
        if (checkInValue) {
            // Set checkout date minimum to be the day after checkin
            const checkInDate = new Date(checkInValue);
            checkInDate.setDate(checkInDate.getDate() + 1);
            const minCheckoutDate = checkInDate.toISOString().split('T')[0];
            $checkOutDate.attr('min', minCheckoutDate);

            // Clear checkout date if it's before the new minimum
            const currentCheckOutValue = $checkOutDate.val();
            if (currentCheckOutValue && currentCheckOutValue <= checkInValue) {
                $checkOutDate.val('');
            }
        }

        // Trigger validation when both dates are selected
        if ($checkOutDate.val()) {
            validateDateRange();
        }
    });

    // Validate that checkout is after checkin on checkout change
    $checkOutDate.on('change', function() {
        validateDateRange();
    });

    // Function to validate the complete date range
    function validateDateRange() {
        const checkInValue = $checkInDate.val();
        const checkOutValue = $checkOutDate.val();

        if (!checkInValue || !checkOutValue) {
            return;
        }

        if (checkOutValue <= checkInValue) {
            alert('{{ __('Check-out date must be after check-in date') }}');
            $checkOutDate.val('');
            return;
        }

        // Calculate nights for potential minimum stay validation
        const checkIn = new Date(checkInValue);
        const checkOut = new Date(checkOutValue);
        const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));

        // Add visual feedback about the stay duration
        let $feedback = $checkOutDate.siblings('.date-feedback');
        if ($feedback.length === 0) {
            $feedback = $('<div class="date-feedback text-muted small mt-1"></div>');
            $checkOutDate.parent().append($feedback);
        }

        $feedback.text(nights + ' {{ __('night(s)') }}' +
                      (nights === 1 ? '' : 's') +
                      ' - {{ __('Results will show properties available for this duration') }}');
    }

    // Initialize checkout minimum date on page load
    if ($checkInDate.val()) {
        $checkInDate.trigger('change');
    }

    // Add form submission validation
    if ($form.length) {
        $form.on('submit', function(e) {
            const checkInValue = $checkInDate.val();
            const checkOutValue = $checkOutDate.val();

            // If both dates are provided, validate them
            if (checkInValue && checkOutValue) {
                if (checkOutValue <= checkInValue) {
                    e.preventDefault();
                    alert('{{ __('Please select valid check-in and check-out dates') }}');
                    return false;
                }
            }
        });
    }
});

// Global function to reset vacation rental filters
function resetVacationRentalFilters() {
    const $form = $('#check_in_date').closest('form');
    if ($form.length) {
        // Clear all form inputs
        $form.find('input[type="text"], input[type="date"], input[type="number"]').val('');
        $form.find('input[type="checkbox"], input[type="radio"]').prop('checked', false);

        // Reset all select dropdowns to their first option
        $form.find('select').each(function() {
            $(this).val($(this).find('option:first').val());
            // Trigger nice-select update if it exists
            if ($(this).hasClass('nice-select')) {
                $(this).niceSelect('update');
            }
        });

        // Reset property type tabs
        $('.vr-property-tab').removeClass('active');
        $('.vr-property-tab[data-property-type=""]').addClass('active');
        $('#selected_category_id').val('');

        // Clear date feedback
        $('.date-feedback').remove();

        // Reset date constraints
        $('#check_out_date').removeAttr('min');

        // Hide advanced filters
        $('#vr-advanced-filters').slideUp();
        $('.vr-advanced-btn').attr('aria-expanded', 'false');
        $('.vr-advanced-text').text($('.vr-advanced-btn').data('filter-text-default'));

        // Redirect to the clean URL
        const baseUrl = window.location.pathname;
        window.location.href = baseUrl;
    }
}

// Function to handle property type selection
function selectPropertyType(typeValue) {
    // Update visual state
    $('.vr-property-tab').removeClass('active');
    $(`.vr-property-tab[data-property-type="${typeValue}"]`).addClass('active');

    // Update hidden input
    $('#selected_category_id').val(typeValue);
}

// Function to toggle advanced filters
function toggleAdvancedFilters() {
    const $advancedPanel = $('#vr-advanced-filters');
    const $button = $('.vr-advanced-btn');
    const $buttonText = $('.vr-advanced-text');
    const isExpanded = $button.attr('aria-expanded') === 'true';

    if (isExpanded) {
        // Hide advanced filters
        $advancedPanel.slideUp(300);
        $button.attr('aria-expanded', 'false');
        $buttonText.text($button.data('filter-text-default'));
        $button.removeClass('active');
    } else {
        // Show advanced filters
        $advancedPanel.slideDown(300);
        $button.attr('aria-expanded', 'true');
        $buttonText.text($button.data('filter-text-active'));
        $button.addClass('active');
    }
}
</script>
