<?php

/**
 * Test script for vacation rental booking system fixes
 * Run with: php test-vacation-rental-fixes.php
 */

require __DIR__ . '/vendor/autoload.php';

use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Models\PropertyAvailability;
use Botble\RealEstate\Models\VacationRentalBooking;
use Botble\RealEstate\Enums\PropertyTypeEnum;
use Botble\RealEstate\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "\n=== VACATION RENTAL BOOKING SYSTEM TEST ===\n\n";

try {
    DB::beginTransaction();
    
    // Test 1: Create a vacation rental property
    echo "Test 1: Creating vacation rental property...\n";
    $property = Property::create([
        'name' => 'Test Beach Villa',
        'type' => PropertyTypeEnum::VACATION_RENTAL,
        'price' => 200.00,
        'minimum_stay' => 3,
        'maximum_guests' => 8,
        'cleaning_fee' => 75.00,
        'security_deposit' => 300.00,
        'moderation_status' => 'approved',
        'status' => 'renting',
        'description' => 'Beautiful beach villa for vacation rental',
        'location' => '123 Beach Street',
        'number_bedroom' => 4,
        'number_bathroom' => 3,
        'square' => 200,
    ]);
    echo "✓ Property created with ID: {$property->id}\n\n";
    
    // Test 2: Check availability for new property (should create records)
    echo "Test 2: Checking availability for new property...\n";
    $availabilityService = new AvailabilityService();
    $checkIn = Carbon::tomorrow();
    $checkOut = Carbon::tomorrow()->addDays(5);
    
    $isAvailable = $availabilityService->checkAvailability($property->id, $checkIn, $checkOut);
    echo "✓ Availability check result: " . ($isAvailable ? 'Available' : 'Not Available') . "\n";
    
    // Verify records were created
    $recordsCount = PropertyAvailability::forProperty($property->id)
        ->inDateRange($checkIn, $checkOut)
        ->count();
    echo "✓ Availability records created: {$recordsCount}\n\n";
    
    // Test 3: Create a booking
    echo "Test 3: Creating a booking...\n";
    $booking = VacationRentalBooking::create([
        'property_id' => $property->id,
        'guest_name' => 'John Doe',
        'guest_email' => 'john@example.com',
        'guest_phone' => '+1234567890',
        'check_in_date' => $checkIn,
        'check_out_date' => $checkOut,
        'nights_count' => $checkIn->diffInDays($checkOut),
        'guests_count' => 4,
        'base_price_per_night' => 200,
        'total_nights_cost' => 1000,
        'cleaning_fee' => 75,
        'service_fee' => 50,
        'taxes' => 125,
        'security_deposit' => 300,
        'total_amount' => 1250,
        'status' => VacationRentalBooking::STATUS_CONFIRMED,
        'payment_status' => VacationRentalBooking::PAYMENT_PAID,
    ]);
    echo "✓ Booking created with number: {$booking->booking_number}\n\n";
    
    // Test 4: Verify dates are now booked
    echo "Test 4: Verifying dates are marked as booked...\n";
    $bookedDates = PropertyAvailability::forProperty($property->id)
        ->inDateRange($checkIn, $checkOut)
        ->where('status', PropertyAvailability::STATUS_BOOKED)
        ->count();
    echo "✓ Booked dates count: {$bookedDates}\n\n";
    
    // Test 5: Try to book the same dates (should fail)
    echo "Test 5: Testing double booking prevention...\n";
    $isStillAvailable = $availabilityService->checkAvailability($property->id, $checkIn, $checkOut);
    echo "✓ Double booking check: " . ($isStillAvailable ? 'FAILED - Dates still available!' : 'SUCCESS - Dates blocked') . "\n\n";
    
    // Test 6: Check availability for different dates
    echo "Test 6: Checking availability for different dates...\n";
    $newCheckIn = $checkOut->copy()->addDay();
    $newCheckOut = $newCheckIn->copy()->addDays(3);
    $isDifferentAvailable = $availabilityService->checkAvailability($property->id, $newCheckIn, $newCheckOut);
    echo "✓ Different dates availability: " . ($isDifferentAvailable ? 'Available' : 'Not Available') . "\n\n";
    
    // Test 7: Cancel booking and verify dates become available
    echo "Test 7: Cancelling booking and checking availability...\n";
    $booking->status = VacationRentalBooking::STATUS_CANCELLED;
    $booking->save();
    
    // Small delay to ensure event listeners have processed
    sleep(1);
    
    $isAvailableAfterCancel = $availabilityService->checkAvailability($property->id, $checkIn, $checkOut);
    echo "✓ Availability after cancellation: " . ($isAvailableAfterCancel ? 'Available' : 'Not Available') . "\n\n";
    
    // Test 8: Verify minimum stay validation
    echo "Test 8: Testing minimum stay validation...\n";
    $shortCheckIn = Carbon::tomorrow()->addDays(10);
    $shortCheckOut = $shortCheckIn->copy()->addDays(2); // Only 2 nights (less than minimum 3)
    $isValidStay = $availabilityService->validateMinimumStay($property->id, $shortCheckIn, $shortCheckOut);
    echo "✓ Minimum stay validation (2 nights, min 3): " . ($isValidStay ? 'FAILED - Should not be valid!' : 'SUCCESS - Correctly rejected') . "\n\n";
    
    // Test 9: Calculate booking price
    echo "Test 9: Testing price calculation...\n";
    try {
        $pricing = $availabilityService->calculateBookingPrice(
            $property->id,
            Carbon::tomorrow()->addDays(20),
            Carbon::tomorrow()->addDays(25),
            4
        );
        echo "✓ Price calculation successful:\n";
        echo "  - Nights: {$pricing['nights']}\n";
        echo "  - Total nights cost: \${$pricing['total_nights_cost']}\n";
        echo "  - Cleaning fee: \${$pricing['cleaning_fee']}\n";
        echo "  - Total amount: \${$pricing['total_amount']}\n\n";
    } catch (Exception $e) {
        echo "✗ Price calculation failed: " . $e->getMessage() . "\n\n";
    }
    
    DB::rollBack();
    echo "=== ALL TESTS COMPLETED ===\n";
    echo "Note: All changes were rolled back (test data not saved)\n\n";
    
} catch (Exception $e) {
    DB::rollBack();
    echo "\n✗ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
