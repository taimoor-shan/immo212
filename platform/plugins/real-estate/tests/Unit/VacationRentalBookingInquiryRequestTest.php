<?php

namespace Botble\RealEstate\Tests\Unit;

use Botble\RealEstate\Enums\PropertyTypeEnum;
use Botble\RealEstate\Http\Requests\VacationRentalBookingInquiryRequest;
use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;
use Mockery;

class VacationRentalBookingInquiryRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock the availability service
        $this->availabilityService = Mockery::mock(AvailabilityService::class);
        $this->app->instance(AvailabilityService::class, $this->availabilityService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_validation_rules_are_correct()
    {
        $request = new VacationRentalBookingInquiryRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('name', $rules);
        $this->assertArrayHasKey('email', $rules);
        $this->assertArrayHasKey('phone', $rules);
        $this->assertArrayHasKey('content', $rules);
        $this->assertArrayHasKey('property_id', $rules);
        $this->assertArrayHasKey('check_in_date', $rules);
        $this->assertArrayHasKey('check_out_date', $rules);
        $this->assertArrayHasKey('guests_count', $rules);

        // Check specific validation rules
        $this->assertContains('required', $rules['name']);
        $this->assertContains('required', $rules['property_id']);
        $this->assertContains('required', $rules['check_in_date']);
        $this->assertContains('required', $rules['check_out_date']);
        $this->assertContains('required', $rules['guests_count']);
    }

    public function test_valid_data_passes_validation()
    {
        $property = Property::factory()->create([
            'type' => PropertyTypeEnum::VACATION_RENTAL,
            'maximum_guests' => 4,
            'minimum_stay' => 2,
        ]);

        $this->availabilityService
            ->shouldReceive('checkAvailability')
            ->once()
            ->andReturn(true);

        $this->availabilityService
            ->shouldReceive('validateMinimumStay')
            ->once()
            ->andReturn(true);

        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'content' => 'Looking forward to staying at your property.',
            'property_id' => $property->id,
            'check_in_date' => Carbon::tomorrow()->format('Y-m-d'),
            'check_out_date' => Carbon::tomorrow()->addDays(3)->format('Y-m-d'),
            'guests_count' => 2,
        ];

        $request = new VacationRentalBookingInquiryRequest();
        $request->merge($data);

        $validator = Validator::make($data, $request->rules());
        
        $this->assertTrue($validator->passes());
    }

    public function test_invalid_property_type_fails_validation()
    {
        $property = Property::factory()->create([
            'type' => PropertyTypeEnum::SALE, // Not vacation rental
        ]);

        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'content' => 'Test message',
            'property_id' => $property->id,
            'check_in_date' => Carbon::tomorrow()->format('Y-m-d'),
            'check_out_date' => Carbon::tomorrow()->addDays(2)->format('Y-m-d'),
            'guests_count' => 2,
        ];

        $request = new VacationRentalBookingInquiryRequest();
        $request->merge($data);

        $validator = Validator::make($data, $request->rules());
        $validator->after(function ($validator) use ($request) {
            $request->withValidator($validator);
        });

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('property_id', $validator->errors()->toArray());
    }

    public function test_past_dates_fail_validation()
    {
        $property = Property::factory()->create([
            'type' => PropertyTypeEnum::VACATION_RENTAL,
        ]);

        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'content' => 'Test message',
            'property_id' => $property->id,
            'check_in_date' => Carbon::yesterday()->format('Y-m-d'),
            'check_out_date' => Carbon::today()->format('Y-m-d'),
            'guests_count' => 2,
        ];

        $validator = Validator::make($data, (new VacationRentalBookingInquiryRequest())->rules());
        
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('check_in_date', $validator->errors()->toArray());
    }

    public function test_check_out_before_check_in_fails_validation()
    {
        $property = Property::factory()->create([
            'type' => PropertyTypeEnum::VACATION_RENTAL,
        ]);

        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'content' => 'Test message',
            'property_id' => $property->id,
            'check_in_date' => Carbon::tomorrow()->addDays(2)->format('Y-m-d'),
            'check_out_date' => Carbon::tomorrow()->format('Y-m-d'),
            'guests_count' => 2,
        ];

        $validator = Validator::make($data, (new VacationRentalBookingInquiryRequest())->rules());
        
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('check_out_date', $validator->errors()->toArray());
    }

    public function test_unavailable_dates_fail_validation()
    {
        $property = Property::factory()->create([
            'type' => PropertyTypeEnum::VACATION_RENTAL,
        ]);

        $this->availabilityService
            ->shouldReceive('checkAvailability')
            ->once()
            ->andReturn(false);

        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'content' => 'Test message',
            'property_id' => $property->id,
            'check_in_date' => Carbon::tomorrow()->format('Y-m-d'),
            'check_out_date' => Carbon::tomorrow()->addDays(2)->format('Y-m-d'),
            'guests_count' => 2,
        ];

        $request = new VacationRentalBookingInquiryRequest();
        $request->merge($data);

        $validator = Validator::make($data, $request->rules());
        $validator->after(function ($validator) use ($request) {
            $request->withValidator($validator);
        });

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('check_in_date', $validator->errors()->toArray());
    }

    public function test_minimum_stay_validation_fails()
    {
        $property = Property::factory()->create([
            'type' => PropertyTypeEnum::VACATION_RENTAL,
            'minimum_stay' => 3,
        ]);

        $this->availabilityService
            ->shouldReceive('checkAvailability')
            ->once()
            ->andReturn(true);

        $this->availabilityService
            ->shouldReceive('validateMinimumStay')
            ->once()
            ->andReturn(false);

        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'content' => 'Test message',
            'property_id' => $property->id,
            'check_in_date' => Carbon::tomorrow()->format('Y-m-d'),
            'check_out_date' => Carbon::tomorrow()->addDays(2)->format('Y-m-d'), // Only 2 nights
            'guests_count' => 2,
        ];

        $request = new VacationRentalBookingInquiryRequest();
        $request->merge($data);

        $validator = Validator::make($data, $request->rules());
        $validator->after(function ($validator) use ($request) {
            $request->withValidator($validator);
        });

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('check_out_date', $validator->errors()->toArray());
    }

    public function test_exceeding_maximum_guests_fails_validation()
    {
        $property = Property::factory()->create([
            'type' => PropertyTypeEnum::VACATION_RENTAL,
            'maximum_guests' => 2,
        ]);

        $this->availabilityService
            ->shouldReceive('checkAvailability')
            ->once()
            ->andReturn(true);

        $this->availabilityService
            ->shouldReceive('validateMinimumStay')
            ->once()
            ->andReturn(true);

        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'content' => 'Test message',
            'property_id' => $property->id,
            'check_in_date' => Carbon::tomorrow()->format('Y-m-d'),
            'check_out_date' => Carbon::tomorrow()->addDays(2)->format('Y-m-d'),
            'guests_count' => 4, // Exceeds maximum
        ];

        $request = new VacationRentalBookingInquiryRequest();
        $request->merge($data);

        $validator = Validator::make($data, $request->rules());
        $validator->after(function ($validator) use ($request) {
            $request->withValidator($validator);
        });

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('guests_count', $validator->errors()->toArray());
    }

    public function test_attributes_method_returns_correct_labels()
    {
        $request = new VacationRentalBookingInquiryRequest();
        $attributes = $request->attributes();

        $this->assertArrayHasKey('name', $attributes);
        $this->assertArrayHasKey('check_in_date', $attributes);
        $this->assertArrayHasKey('check_out_date', $attributes);
        $this->assertArrayHasKey('guests_count', $attributes);
        
        $this->assertEquals(__('Name'), $attributes['name']);
        $this->assertEquals(__('Check-in Date'), $attributes['check_in_date']);
        $this->assertEquals(__('Check-out Date'), $attributes['check_out_date']);
        $this->assertEquals(__('Number of Guests'), $attributes['guests_count']);
    }
}
