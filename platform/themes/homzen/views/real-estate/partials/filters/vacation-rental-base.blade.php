<div @class(['wd-find-select position-relative' =>  in_array($style, [1, 2, 4]), 'wd-filter-select' => $style === 3, 'no-left-round' => $noLeftRound ?? false])>
    <div class="inner-group">
        {{-- Vacation rental specific filter order: Check-in, Check-out, Cities, Categories, Price Range, Guests --}}
        @include(Theme::getThemeNamespace('views.real-estate.partials.filters.vacation-rental-checkin'))
        @include(Theme::getThemeNamespace('views.real-estate.partials.filters.vacation-rental-checkout'))
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
    <div class="btn-group d-flex gap-2">
        <button type="submit" class="tf-btn primary flex-fill">{{ __('Search Vacation Rentals') }}</button>
        <button type="button" class="tf-btn outline-primary" onclick="resetVacationRentalFilters()">
            <i class="fas fa-undo-alt me-1"></i>
            {{ __('Reset') }}
        </button>
    </div>
    @if($style === 3)
        </div>
    @endif
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
        
        // Clear date feedback
        $('.date-feedback').remove();
        
        // Reset date constraints
        $('#check_out_date').removeAttr('min');
        
        // Optionally auto-submit the form to refresh results
        // $form.submit();
        
        // Or redirect to the clean URL
        const baseUrl = window.location.pathname;
        window.location.href = baseUrl;
    }
}
</script>
