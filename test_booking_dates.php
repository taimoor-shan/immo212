<?php

require_once 'bootstrap/app.php';

use Botble\RealEstate\Models\VacationRentalBooking;
use Botble\RealEstate\Models\PropertyAvailability;
use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Services\AvailabilityService;
use Carbon\Carbon;

$app = app();

echo "=== VACATION RENTAL BOOKING DATE TEST ===" . PHP_EOL;

// Find a vacation rental property
$property = Property::where('type', 'vacation_rental')->first();

if (!$property) {
    echo "No vacation rental property found!" . PHP_EOL;
    exit(1);
}

echo "Testing with Property ID: " . $property->id . PHP_EOL;
echo "Property Name: " . $property->name . PHP_EOL;

// Test scenario: Book 3 consecutive dates (tomorrow, day after, day after that)
$checkIn = Carbon::tomorrow();
$checkOut = $checkIn->copy()->addDays(2); // This creates a 2-night stay

echo PHP_EOL . "=== TEST SCENARIO ===" . PHP_EOL;
echo "User selects 3 dates in calendar: " . $checkIn->format('Y-m-d') . ", " . $checkIn->copy()->addDay()->format('Y-m-d') . ", " . $checkOut->format('Y-m-d') . PHP_EOL;
echo "This translates to:" . PHP_EOL;
echo "Check-in: " . $checkIn->format('Y-m-d') . PHP_EOL;
echo "Check-out: " . $checkOut->format('Y-m-d') . PHP_EOL;
echo "Nights: " . $checkIn->diffInDays($checkOut) . PHP_EOL;

// Clean up any existing bookings for these dates
echo PHP_EOL . "=== CLEANUP ===" . PHP_EOL;
VacationRentalBooking::where('property_id', $property->id)
    ->where('check_in_date', '>=', $checkIn->format('Y-m-d'))
    ->where('check_out_date', '<=', $checkOut->format('Y-m-d'))
    ->delete();

PropertyAvailability::where('property_id', $property->id)
    ->whereBetween('date', [$checkIn->format('Y-m-d'), $checkOut->format('Y-m-d')])
    ->delete();

echo "Cleaned up existing bookings and availability records" . PHP_EOL;

// Create a test booking
echo PHP_EOL . "=== CREATING BOOKING ===" . PHP_EOL;
$booking = VacationRentalBooking::create([
    'booking_number' => 'TEST' . time(),
    'property_id' => $property->id,
    'guest_name' => 'Test User',
    'guest_email' => 'test@example.com',
    'guest_phone' => '1234567890',
    'check_in_date' => $checkIn,
    'check_out_date' => $checkOut,
    'nights_count' => $checkIn->diffInDays($checkOut),
    'guests_count' => 2,
    'base_price_per_night' => 100,
    'total_nights_cost' => 200,
    'total_amount' => 200,
    'status' => 'confirmed',
]);

echo "Booking created with ID: " . $booking->id . PHP_EOL;

// Check what dates the booking thinks should be booked
echo PHP_EOL . "=== BOOKING DATE RANGE ===" . PHP_EOL;
$dateRange = $booking->getDateRange();
echo "Dates that should be marked as booked: " . json_encode($dateRange) . PHP_EOL;

// Check what's actually in the database
echo PHP_EOL . "=== DATABASE CHECK ===" . PHP_EOL;
$bookedDates = PropertyAvailability::where('property_id', $property->id)
    ->where('status', 'booked')
    ->orderBy('date')
    ->pluck('date')
    ->map(function($date) { return $date->format('Y-m-d'); })
    ->toArray();

echo "Dates marked as booked in DB: " . json_encode($bookedDates) . PHP_EOL;

// Check what the API returns
echo PHP_EOL . "=== API RESPONSE ===" . PHP_EOL;
$service = app(AvailabilityService::class);
$startDate = $checkIn->copy()->subDays(2);
$endDate = $checkOut->copy()->addDays(2);

$availability = $service->getAvailabilityExceptions($property->id, $startDate, $endDate);

echo "API availability exceptions:" . PHP_EOL;
foreach ($availability as $date => $info) {
    echo "  " . $date . ": " . $info['status'] . PHP_EOL;
}

// Summary
echo PHP_EOL . "=== SUMMARY ===" . PHP_EOL;
echo "Expected behavior for vacation rental:" . PHP_EOL;
echo "- User selects 3 dates: " . $checkIn->format('Y-m-d') . ", " . $checkIn->copy()->addDay()->format('Y-m-d') . ", " . $checkOut->format('Y-m-d') . PHP_EOL;
echo "- Check-in: " . $checkIn->format('Y-m-d') . " (guest arrives)" . PHP_EOL;
echo "- Check-out: " . $checkOut->format('Y-m-d') . " (guest leaves, date available for next guest)" . PHP_EOL;
echo "- Dates marked as booked: " . $checkIn->format('Y-m-d') . ", " . $checkIn->copy()->addDay()->format('Y-m-d') . PHP_EOL;
echo "- Date available: " . $checkOut->format('Y-m-d') . PHP_EOL;

$expectedBooked = [$checkIn->format('Y-m-d'), $checkIn->copy()->addDay()->format('Y-m-d')];
$actualBooked = $bookedDates;

if ($expectedBooked === $actualBooked) {
    echo PHP_EOL . "✅ SUCCESS: Booking dates are correct!" . PHP_EOL;
} else {
    echo PHP_EOL . "❌ ERROR: Booking dates are incorrect!" . PHP_EOL;
    echo "Expected: " . json_encode($expectedBooked) . PHP_EOL;
    echo "Actual: " . json_encode($actualBooked) . PHP_EOL;
}

echo PHP_EOL . "=== END TEST ===" . PHP_EOL;
