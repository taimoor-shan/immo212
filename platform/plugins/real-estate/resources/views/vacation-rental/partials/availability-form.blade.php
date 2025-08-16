@php
    // Adapt VacationRental to work with the existing availability system
    // The calendar was designed for vacation rentals when they were part of properties
    // Now we're using it with the standalone VacationRental model
    $propertyId = $vacationRental && $vacationRental->exists ? $vacationRental->id : null;
    
    // Create a wrapper object that the existing view can work with
    $property = new \stdClass();
    $property->id = $propertyId;
    $property->exists = $vacationRental->exists;
    $property->type = 'vacation_rental'; // This will trigger the calendar display
@endphp

<div class="vacation-rental-availability-wrapper">
    @if($propertyId)
        <!-- Use the existing comprehensive calendar view -->
        @include('plugins/real-estate::partials.form-vacation-rental-availability', [
            'property' => $property
        ])
    @else
        <div class="alert alert-info">
            <x-core::icon name="ti ti-info-circle" class="me-2" />
            {{ __('Please save the vacation rental first to manage the availability calendar.') }}
        </div>
    @endif
</div>

<script>
    // Override the property ID detection for vacation rentals
    document.addEventListener('DOMContentLoaded', function() {
        @if($propertyId)
            // Ensure the calendar knows this is a vacation rental
            if (window.propertyAvailabilityData === undefined) {
                window.propertyAvailabilityData = {};
            }
            
            // The calendar system will work with vacation rental IDs
            const calendarElement = document.getElementById('property-availability-calendar');
            if (calendarElement) {
                calendarElement.setAttribute('data-property-id', '{{ $propertyId }}');
                calendarElement.setAttribute('data-property-type', 'vacation_rental');
            }
            
            // Show the calendar section immediately for vacation rentals
            const calendarSection = document.getElementById('calendar-section');
            if (calendarSection) {
                calendarSection.style.display = 'block';
            }
            
            // Hide the info message
            const infoMessage = document.getElementById('vacation-rental-info-message');
            if (infoMessage) {
                infoMessage.style.display = 'none';
            }
        @endif
    });
</script>
