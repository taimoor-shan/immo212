<?php

namespace Botble\RealEstate\Tests\Unit;

use Botble\RealEstate\Forms\Fronts\VacationRentalBookingInquiryForm;
use Botble\RealEstate\Http\Requests\VacationRentalBookingInquiryRequest;
use Botble\RealEstate\Models\ConsultCustomField;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VacationRentalBookingInquiryFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_form_has_correct_validator_class()
    {
        $form = VacationRentalBookingInquiryForm::create();
        
        $this->assertEquals(VacationRentalBookingInquiryRequest::class, $form->getValidatorClass());
    }

    public function test_form_has_correct_url()
    {
        $form = VacationRentalBookingInquiryForm::create();
        
        $this->assertEquals(route('public.vacation-rental.booking-inquiry'), $form->getFormOption('url'));
    }

    public function test_form_has_correct_css_class()
    {
        $form = VacationRentalBookingInquiryForm::create();
        
        $this->assertEquals('vacation-rental-booking-form', $form->getFormOption('class'));
    }

    public function test_form_contains_required_fields()
    {
        $form = VacationRentalBookingInquiryForm::create();
        $formData = $form->getFormData();

        // Check that required fields exist
        $this->assertArrayHasKey('name', $formData['fields']);
        $this->assertArrayHasKey('check_in_date', $formData['fields']);
        $this->assertArrayHasKey('check_out_date', $formData['fields']);
        $this->assertArrayHasKey('guests_count', $formData['fields']);
        $this->assertArrayHasKey('content', $formData['fields']);
        $this->assertArrayHasKey('property_id', $formData['fields']);
        $this->assertArrayHasKey('type', $formData['fields']);
        $this->assertArrayHasKey('submit', $formData['fields']);
    }

    public function test_form_has_correct_field_types()
    {
        $form = VacationRentalBookingInquiryForm::create();
        $formData = $form->getFormData();

        $this->assertEquals('text', $formData['fields']['name']['type']);
        $this->assertEquals('date', $formData['fields']['check_in_date']['type']);
        $this->assertEquals('date', $formData['fields']['check_out_date']['type']);
        $this->assertEquals('number', $formData['fields']['guests_count']['type']);
        $this->assertEquals('textarea', $formData['fields']['content']['type']);
        $this->assertEquals('hidden', $formData['fields']['property_id']['type']);
        $this->assertEquals('hidden', $formData['fields']['type']['type']);
        $this->assertEquals('submit', $formData['fields']['submit']['type']);
    }

    public function test_form_has_correct_field_labels()
    {
        $form = VacationRentalBookingInquiryForm::create();
        $formData = $form->getFormData();

        $this->assertEquals(__('Full Name'), $formData['fields']['name']['label']);
        $this->assertEquals(__('Check-in Date'), $formData['fields']['check_in_date']['label']);
        $this->assertEquals(__('Check-out Date'), $formData['fields']['check_out_date']['label']);
        $this->assertEquals(__('Number of Guests'), $formData['fields']['guests_count']['label']);
        $this->assertEquals(__('Special Requests / Message'), $formData['fields']['content']['label']);
    }

    public function test_form_has_correct_field_attributes()
    {
        $form = VacationRentalBookingInquiryForm::create();
        $formData = $form->getFormData();

        // Check date field attributes
        $this->assertEquals(date('Y-m-d'), $formData['fields']['check_in_date']['attr']['min']);
        $this->assertEquals(date('Y-m-d', strtotime('+1 day')), $formData['fields']['check_out_date']['attr']['min']);
        $this->assertStringContainsString('booking-date-picker', $formData['fields']['check_in_date']['attr']['class']);

        // Check number field attributes
        $this->assertEquals(1, $formData['fields']['guests_count']['attr']['min']);
        $this->assertEquals(50, $formData['fields']['guests_count']['attr']['max']);

        // Check hidden field values
        $this->assertEquals('vacation_rental_booking', $formData['fields']['type']['value']);
    }

    public function test_form_includes_custom_fields_when_available()
    {
        // Create a custom field
        $customField = ConsultCustomField::factory()->create([
            'name' => 'Special Requirements',
            'key' => 'special_requirements',
            'type' => 'text',
            'required' => true,
            'status' => 'published',
        ]);

        $form = VacationRentalBookingInquiryForm::create();
        $formData = $form->getFormData();

        $fieldKey = "consult_custom_fields.{$customField->id}";
        $this->assertArrayHasKey($fieldKey, $formData['fields']);
        $this->assertEquals('Special Requirements', $formData['fields'][$fieldKey]['label']);
        $this->assertTrue($formData['fields'][$fieldKey]['required']);
    }

    public function test_form_handles_different_custom_field_types()
    {
        // Create different types of custom fields
        $textField = ConsultCustomField::factory()->create([
            'type' => 'text',
            'status' => 'published',
        ]);

        $textareaField = ConsultCustomField::factory()->create([
            'type' => 'textarea',
            'status' => 'published',
        ]);

        $numberField = ConsultCustomField::factory()->create([
            'type' => 'number',
            'status' => 'published',
        ]);

        $dropdownField = ConsultCustomField::factory()->create([
            'type' => 'dropdown',
            'status' => 'published',
        ]);

        $form = VacationRentalBookingInquiryForm::create();
        $formData = $form->getFormData();

        $this->assertEquals('text', $formData['fields']["consult_custom_fields.{$textField->id}"]['type']);
        $this->assertEquals('textarea', $formData['fields']["consult_custom_fields.{$textareaField->id}"]['type']);
        $this->assertEquals('number', $formData['fields']["consult_custom_fields.{$numberField->id}"]['type']);
        $this->assertEquals('select', $formData['fields']["consult_custom_fields.{$dropdownField->id}"]['type']);
    }

    public function test_form_respects_hidden_fields_configuration()
    {
        // Mock the RealEstateHelper to hide phone field
        $this->mock(\Botble\RealEstate\Facades\RealEstateHelper::class, function ($mock) {
            $mock->shouldReceive('isHiddenFieldAtConsultForm')
                ->with('phone')
                ->andReturn(true);
            $mock->shouldReceive('isHiddenFieldAtConsultForm')
                ->with('email')
                ->andReturn(false);
            $mock->shouldReceive('enabledMandatoryFieldsAtConsultForm')
                ->andReturn([]);
            $mock->shouldReceive('isEnabledConsultForm')
                ->andReturn(true);
        });

        $form = VacationRentalBookingInquiryForm::create();
        $formData = $form->getFormData();

        // Phone field should not be present
        $this->assertArrayNotHasKey('phone', $formData['fields']);
        
        // Email field should be present
        $this->assertArrayHasKey('email', $formData['fields']);
    }

    public function test_form_respects_mandatory_fields_configuration()
    {
        // Mock the RealEstateHelper to make email mandatory
        $this->mock(\Botble\RealEstate\Facades\RealEstateHelper::class, function ($mock) {
            $mock->shouldReceive('isHiddenFieldAtConsultForm')
                ->andReturn(false);
            $mock->shouldReceive('enabledMandatoryFieldsAtConsultForm')
                ->andReturn(['email']);
            $mock->shouldReceive('isEnabledConsultForm')
                ->andReturn(true);
        });

        $form = VacationRentalBookingInquiryForm::create();
        $formData = $form->getFormData();

        // Email field should be required
        $this->assertTrue($formData['fields']['email']['required']);
    }

    public function test_form_renders_when_consult_form_is_enabled()
    {
        $this->mock(\Botble\RealEstate\Facades\RealEstateHelper::class, function ($mock) {
            $mock->shouldReceive('isEnabledConsultForm')
                ->andReturn(true);
            $mock->shouldReceive('isHiddenFieldAtConsultForm')
                ->andReturn(false);
            $mock->shouldReceive('enabledMandatoryFieldsAtConsultForm')
                ->andReturn([]);
        });

        $form = VacationRentalBookingInquiryForm::create();
        $rendered = $form->renderForm();

        $this->assertNotEmpty($rendered);
        $this->assertStringContainsString('vacation-rental-booking-form', $rendered);
    }

    public function test_form_does_not_render_when_consult_form_is_disabled()
    {
        $this->mock(\Botble\RealEstate\Facades\RealEstateHelper::class, function ($mock) {
            $mock->shouldReceive('isEnabledConsultForm')
                ->andReturn(false);
        });

        $form = VacationRentalBookingInquiryForm::create();
        $rendered = $form->renderForm();

        $this->assertEmpty($rendered);
    }

    public function test_form_submit_button_has_correct_attributes()
    {
        $form = VacationRentalBookingInquiryForm::create();
        $formData = $form->getFormData();

        $submitField = $formData['fields']['submit'];
        
        $this->assertEquals(__('Send Booking Inquiry'), $submitField['label']);
        $this->assertStringContainsString('tf-btn primary w-100', $submitField['attr']['class']);
    }
}
