<?php

namespace Botble\RealEstate\Tests\Unit;

use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Models\PropertyAvailability;
use Botble\RealEstate\Models\PropertyAvailabilityRule;
use Botble\RealEstate\Services\AvailabilityService;
use Botble\RealEstate\Enums\PropertyTypeEnum;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AvailabilityServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AvailabilityService $service;
    protected Property $property;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->service = app(AvailabilityService::class);
        
        $this->property = Property::factory()->create([
            'type' => PropertyTypeEnum::VACATION_RENTAL,
            'price' => 100.00,
            'minimum_stay' => 2,
            'maximum_stay' => 14,
            'maximum_guests' => 4,
            'cleaning_fee' => 25.00,
            'security_deposit' => 100.00,
        ]);
    }

    /** @test */
    public function it_returns_true_for_available_dates()
    {
        $checkIn = Carbon::tomorrow();
        $checkOut = $checkIn->copy()->addDays(3);

        $result = $this->service->checkAvailability($this->property->id, $checkIn, $checkOut);

        $this->assertTrue($result);
    }

    /** @test */
    public function it_returns_false_for_blocked_dates()
    {
        $checkIn = Carbon::tomorrow();
        $checkOut = $checkIn->copy()->addDays(3);

        // Block the dates
        PropertyAvailability::create([
            'property_id' => $this->property->id,
            'date' => $checkIn->format('Y-m-d'),
            'status' => 'blocked',
            'price' => 0,
        ]);

        $result = $this->service->checkAvailability($this->property->id, $checkIn, $checkOut);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_calculates_correct_base_pricing()
    {
        $checkIn = Carbon::tomorrow();
        $checkOut = $checkIn->copy()->addDays(3);
        $guests = 2;

        $pricing = $this->service->calculateBookingPrice($this->property->id, $checkIn, $checkOut, $guests);

        $this->assertEquals(3, $pricing['nights']);
        $this->assertEquals(100.00, $pricing['base_price_per_night']);
        $this->assertEquals(300.00, $pricing['total_nights_cost']);
        $this->assertEquals(25.00, $pricing['cleaning_fee']);
        $this->assertEquals(100.00, $pricing['security_deposit']);
        $this->assertEquals(325.00, $pricing['total_amount']); // 300 + 25
    }

    /** @test */
    public function it_applies_custom_pricing_rules()
    {
        $checkIn = Carbon::tomorrow();
        $checkOut = $checkIn->copy()->addDays(3);

        // Create a pricing rule for weekends
        PropertyAvailabilityRule::create([
            'property_id' => $this->property->id,
            'rule_type' => 'pricing',
            'start_date' => $checkIn->format('Y-m-d'),
            'end_date' => $checkOut->format('Y-m-d'),
            'price_modifier' => 1.5, // 50% increase
            'minimum_stay' => null,
        ]);

        $pricing = $this->service->calculateBookingPrice($this->property->id, $checkIn, $checkOut, 2);

        $this->assertEquals(150.00, $pricing['base_price_per_night']); // 100 * 1.5
        $this->assertEquals(450.00, $pricing['total_nights_cost']); // 150 * 3
    }

    /** @test */
    public function it_validates_minimum_stay_correctly()
    {
        $checkIn = Carbon::tomorrow();
        
        // Test with 1 night (should fail)
        $checkOut = $checkIn->copy()->addDay();
        $result = $this->service->validateMinimumStay($this->property->id, $checkIn, $checkOut);
        $this->assertFalse($result);

        // Test with 2 nights (should pass)
        $checkOut = $checkIn->copy()->addDays(2);
        $result = $this->service->validateMinimumStay($this->property->id, $checkIn, $checkOut);
        $this->assertTrue($result);
    }

    /** @test */
    public function it_applies_custom_minimum_stay_rules()
    {
        $checkIn = Carbon::tomorrow();
        $checkOut = $checkIn->copy()->addDays(2); // 2 nights

        // Create a rule requiring 3 nights minimum
        PropertyAvailabilityRule::create([
            'property_id' => $this->property->id,
            'rule_type' => 'minimum_stay',
            'start_date' => $checkIn->format('Y-m-d'),
            'end_date' => $checkOut->format('Y-m-d'),
            'minimum_stay' => 3,
        ]);

        $result = $this->service->validateMinimumStay($this->property->id, $checkIn, $checkOut);
        $this->assertFalse($result);

        // Test with 3 nights
        $checkOut = $checkIn->copy()->addDays(3);
        $result = $this->service->validateMinimumStay($this->property->id, $checkIn, $checkOut);
        $this->assertTrue($result);
    }

    /** @test */
    public function it_correctly_calculates_nights_for_specific_date_ranges()
    {
        // Test the specific case mentioned: 31-7-2025 to 3-8-2025 should be 3 nights
        $checkIn = Carbon::parse('2025-07-31');
        $checkOut = Carbon::parse('2025-08-03');

        // This should be 3 nights
        $nights = $checkIn->diffInDays($checkOut);
        $this->assertEquals(3, $nights);

        // With minimum stay of 1, this should pass
        $this->property->update(['minimum_stay' => 1]);
        $result = $this->service->validateMinimumStay($this->property->id, $checkIn, $checkOut);
        $this->assertTrue($result);

        // With minimum stay of 3, this should pass
        $this->property->update(['minimum_stay' => 3]);
        $result = $this->service->validateMinimumStay($this->property->id, $checkIn, $checkOut);
        $this->assertTrue($result);

        // With minimum stay of 4, this should fail
        $this->property->update(['minimum_stay' => 4]);
        $result = $this->service->validateMinimumStay($this->property->id, $checkIn, $checkOut);
        $this->assertFalse($result);
    }

    /** @test */
    public function it_correctly_calculates_nights_for_extended_date_range()
    {
        // Test the working case: 31-7-2025 to 4-8-2025 should be 4 nights
        $checkIn = Carbon::parse('2025-07-31');
        $checkOut = Carbon::parse('2025-08-04');

        // This should be 4 nights
        $nights = $checkIn->diffInDays($checkOut);
        $this->assertEquals(4, $nights);

        // With minimum stay of 1, this should pass
        $this->property->update(['minimum_stay' => 1]);
        $result = $this->service->validateMinimumStay($this->property->id, $checkIn, $checkOut);
        $this->assertTrue($result);

        // With minimum stay of 4, this should pass
        $this->property->update(['minimum_stay' => 4]);
        $result = $this->service->validateMinimumStay($this->property->id, $checkIn, $checkOut);
        $this->assertTrue($result);
    }

    /** @test */
    public function it_blocks_dates_correctly()
    {
        $startDate = Carbon::tomorrow();
        $endDate = $startDate->copy()->addDays(3);

        $this->service->blockDates($this->property->id, $startDate, $endDate, 'Test block');

        // Check that availability records were created
        $this->assertDatabaseHas('re_property_availability', [
            'property_id' => $this->property->id,
            'date' => $startDate->format('Y-m-d'),
            'status' => 'blocked',
        ]);

        $this->assertDatabaseHas('re_property_availability', [
            'property_id' => $this->property->id,
            'date' => $startDate->addDay()->format('Y-m-d'),
            'status' => 'blocked',
        ]);
    }

    /** @test */
    public function it_unblocks_dates_correctly()
    {
        $startDate = Carbon::tomorrow();
        $endDate = $startDate->copy()->addDays(3);

        // First block the dates
        $this->service->blockDates($this->property->id, $startDate, $endDate, 'Test block');

        // Then unblock them
        $this->service->unblockDates($this->property->id, $startDate, $endDate);

        // Check that availability records were removed
        $this->assertDatabaseMissing('re_property_availability', [
            'property_id' => $this->property->id,
            'date' => $startDate->format('Y-m-d'),
            'status' => 'blocked',
        ]);
    }

    /** @test */
    public function it_gets_availability_details_correctly()
    {
        $startDate = Carbon::tomorrow();
        $endDate = $startDate->copy()->addDays(7);

        // Block one day in the middle
        PropertyAvailability::create([
            'property_id' => $this->property->id,
            'date' => $startDate->copy()->addDays(3)->format('Y-m-d'),
            'status' => 'blocked',
            'price' => 0,
        ]);

        $details = $this->service->getAvailabilityDetails($this->property->id, $startDate, $endDate);

        $this->assertIsArray($details);
        
        // Check available day
        $availableDate = $startDate->format('Y-m-d');
        $this->assertArrayHasKey($availableDate, $details);
        $this->assertEquals('available', $details[$availableDate]['status']);
        $this->assertEquals(100.00, $details[$availableDate]['price']);

        // Check blocked day
        $blockedDate = $startDate->copy()->addDays(3)->format('Y-m-d');
        $this->assertArrayHasKey($blockedDate, $details);
        $this->assertEquals('blocked', $details[$blockedDate]['status']);
    }

    /** @test */
    public function it_generates_monthly_summary_correctly()
    {
        $year = Carbon::now()->year;
        $month = Carbon::now()->month;

        // Block a few days
        $startOfMonth = Carbon::create($year, $month, 1);
        PropertyAvailability::create([
            'property_id' => $this->property->id,
            'date' => $startOfMonth->copy()->addDays(5)->format('Y-m-d'),
            'status' => 'blocked',
            'price' => 0,
        ]);

        PropertyAvailability::create([
            'property_id' => $this->property->id,
            'date' => $startOfMonth->copy()->addDays(10)->format('Y-m-d'),
            'status' => 'booked',
            'price' => 100,
        ]);

        $summary = $this->service->getMonthlyAvailabilitySummary($this->property->id, $year, $month);

        $this->assertIsArray($summary);
        $this->assertArrayHasKey('summary', $summary);
        $this->assertArrayHasKey('availability', $summary);
        $this->assertArrayHasKey('events', $summary);

        $this->assertArrayHasKey('available_days', $summary['summary']);
        $this->assertArrayHasKey('booked_days', $summary['summary']);
        $this->assertArrayHasKey('blocked_days', $summary['summary']);
        $this->assertArrayHasKey('occupancy_rate', $summary['summary']);

        $this->assertEquals(1, $summary['summary']['booked_days']);
        $this->assertEquals(1, $summary['summary']['blocked_days']);
    }

    /** @test */
    public function it_handles_edge_cases_for_date_ranges()
    {
        $sameDate = Carbon::tomorrow();

        // Test same check-in and check-out date (0 nights)
        $this->expectException(\InvalidArgumentException::class);
        $this->service->calculateBookingPrice($this->property->id, $sameDate, $sameDate, 2);
    }

    /** @test */
    public function it_handles_past_dates_correctly()
    {
        $pastDate = Carbon::yesterday();
        $futureDate = Carbon::tomorrow();

        $result = $this->service->checkAvailability($this->property->id, $pastDate, $futureDate);

        // Should handle past dates gracefully (implementation dependent)
        $this->assertIsBool($result);
    }
}
