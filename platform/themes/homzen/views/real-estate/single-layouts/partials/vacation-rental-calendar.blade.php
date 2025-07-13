{{-- Vacation Rental Calendar Component --}}
@if($property->type === \Botble\RealEstate\Enums\PropertyTypeEnum::VACATION_RENTAL)
<div class="property-calendar-section">
    <div class="property-calendar" 
         id="property-calendar"
         data-property-id="{{ $property->id }}"
         data-property-slug="{{ $property->slug }}"
         data-min-stay="{{ $property->minimum_stay ?? 1 }}"
         data-max-stay="{{ $property->maximum_stay ?? null }}"
         data-max-guests="{{ $property->maximum_guests ?? null }}">
        <!-- Calendar will be initialized here by JavaScript -->
    </div>
</div>

@push('header')
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    
    <!-- Custom Frontend Calendar CSS -->
    <link rel="stylesheet" href="{{ Theme::asset()->url('css/calendar-frontend.css') }}">
@endpush

@push('footer')
    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    
    <!-- Custom Frontend Calendar JS -->
    <script src="{{ Theme::asset()->url('js/calendar-frontend.js') }}"></script>
    
    <script>
        // Initialize booking modal integration
        window.initiateBooking = function() {
            const calendar = window.vacationRentalFrontendCalendar;
            if (!calendar || !calendar.checkInDate || !calendar.checkOutDate) {
                alert('{{ __("Please select check-in and check-out dates") }}');
                return;
            }

            const checkIn = calendar.formatDateForAPI(calendar.checkInDate);
            const checkOut = calendar.formatDateForAPI(calendar.checkOutDate);
            const guests = 1; // Default, can be made dynamic

            // Show booking modal with pre-filled dates
            const modal = document.getElementById('vacation-rental-booking-modal');
            if (modal) {
                // Pre-fill the form
                const checkInInput = modal.querySelector('input[name="check_in"]');
                const checkOutInput = modal.querySelector('input[name="check_out"]');
                const guestsInput = modal.querySelector('input[name="guests"]');

                if (checkInInput) checkInInput.value = checkIn;
                if (checkOutInput) checkOutInput.value = checkOut;
                if (guestsInput) guestsInput.value = guests;

                // Show the modal
                const bootstrapModal = new bootstrap.Modal(modal);
                bootstrapModal.show();
            } else {
                // Fallback: redirect to booking page
                const bookingUrl = `/vacation-rental/book?property_id={{ $property->id }}&check_in=${checkIn}&check_out=${checkOut}&guests=${guests}`;
                window.location.href = bookingUrl;
            }
        };
    </script>
@endpush
@endif
