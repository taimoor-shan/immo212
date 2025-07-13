<?php

namespace Botble\RealEstate\Forms\Fronts;

use Botble\Base\Forms\FieldOptions\ButtonFieldOption;
use Botble\Base\Forms\FieldOptions\DateFieldOption;
use Botble\Base\Forms\FieldOptions\NumberFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\DateField;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\TextField;
use Botble\RealEstate\Enums\ConsultCustomFieldTypeEnum;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Http\Requests\VacationRentalBookingInquiryRequest;
use Botble\RealEstate\Models\ConsultCustomField;
use Botble\Theme\FormFront;
use Illuminate\Support\Collection;

class VacationRentalBookingInquiryForm extends FormFront
{
    public function setup(): void
    {
        $customFields = ConsultCustomField::query()
            ->wherePublished()
            ->orderBy('order')
            ->get();

        $mandatoryFields = RealEstateHelper::enabledMandatoryFieldsAtConsultForm();

        $this
            ->contentOnly()
            ->formClass('vacation-rental-booking-form')
            ->setValidatorClass(VacationRentalBookingInquiryRequest::class)
            ->setUrl(route('public.vacation-rental.booking-inquiry'))
            ->add(
                'name',
                TextField::class,
                TextFieldOption::make()
                    ->required()
                    ->label(__('Full Name'))
                    ->placeholder(__('Enter your full name')),
            )
            ->when(! RealEstateHelper::isHiddenFieldAtConsultForm('phone'), function (VacationRentalBookingInquiryForm $form) use (
                $mandatoryFields
            ): void {
                $form->add(
                    'phone',
                    TextField::class,
                    TextFieldOption::make()
                        ->required(in_array('phone', $mandatoryFields))
                        ->label(__('Phone Number'))
                        ->placeholder(__('Enter your phone number')),
                );
            })
            ->when(! RealEstateHelper::isHiddenFieldAtConsultForm('email'), function (VacationRentalBookingInquiryForm $form) use (
                $mandatoryFields
            ): void {
                $form->add(
                    'email',
                    TextField::class,
                    TextFieldOption::make()
                        ->required(in_array('email', $mandatoryFields))
                        ->label(__('Email Address'))
                        ->placeholder(__('Enter your email address')),
                );
            })
            ->add(
                'check_in_date',
                DateField::class,
                DateFieldOption::make()
                    ->required()
                    ->label(__('Check-in Date'))
                    ->placeholder(__('Select check-in date'))
                    ->addAttribute('min', date('Y-m-d'))
                    ->addAttribute('class', 'form-control booking-date-picker'),
            )
            ->add(
                'check_out_date',
                DateField::class,
                DateFieldOption::make()
                    ->required()
                    ->label(__('Check-out Date'))
                    ->placeholder(__('Select check-out date'))
                    ->addAttribute('min', date('Y-m-d', strtotime('+1 day')))
                    ->addAttribute('class', 'form-control booking-date-picker'),
            )
            ->add(
                'guests_count',
                NumberField::class,
                NumberFieldOption::make()
                    ->required()
                    ->label(__('Number of Guests'))
                    ->placeholder(__('Enter number of guests'))
                    ->addAttribute('min', 1)
                    ->addAttribute('max', 50),
            )
            ->when($customFields, function (VacationRentalBookingInquiryForm $form, Collection $customFields): void {
                foreach ($customFields as $customField) {
                    /**
                     * @var ConsultCustomField $customField
                     */
                    $options = $customField->options()->select('id', 'label', 'value')->get()->mapWithKeys(function ($option) {
                        return [$option->value => $option->label];
                    })->all();

                    match ($customField->type->getValue()) {
                        ConsultCustomFieldTypeEnum::TEXT => $form->add(
                            "consult_custom_fields.{$customField->id}",
                            TextField::class,
                            TextFieldOption::make()
                                ->required($customField->required)
                                ->label($customField->name)
                                ->placeholder($customField->placeholder)
                        ),
                        ConsultCustomFieldTypeEnum::TEXTAREA => $form->add(
                            "consult_custom_fields.{$customField->id}",
                            TextareaField::class,
                            TextareaFieldOption::make()
                                ->required($customField->required)
                                ->label($customField->name)
                                ->placeholder($customField->placeholder)
                        ),
                        ConsultCustomFieldTypeEnum::NUMBER => $form->add(
                            "consult_custom_fields.{$customField->id}",
                            NumberField::class,
                            NumberFieldOption::make()
                                ->required($customField->required)
                                ->label($customField->name)
                                ->placeholder($customField->placeholder)
                        ),
                        ConsultCustomFieldTypeEnum::DROPDOWN => $form->add(
                            "consult_custom_fields.{$customField->id}",
                            SelectField::class,
                            SelectFieldOption::make()
                                ->required($customField->required)
                                ->label($customField->name)
                                ->choices($options)
                                ->placeholder(__('-- Select --'))
                        ),
                        default => null,
                    };
                }
            })
            ->add(
                'content',
                TextareaField::class,
                TextareaFieldOption::make()
                    ->required()
                    ->label(__('Special Requests / Message'))
                    ->placeholder(__('Please let us know about any special requests or additional information...')),
            )
            ->add(
                'property_id',
                'hidden',
                ['value' => '']
            )
            ->add(
                'type',
                'hidden',
                ['value' => 'vacation_rental_booking']
            )
            ->add(
                'submit',
                'submit',
                ButtonFieldOption::make()
                    ->label(__('Send Booking Inquiry'))
                    ->addAttribute('class', 'tf-btn primary w-100')
            );
    }

    public function renderForm(array $options = [], bool $showStart = true, bool $showFields = true, bool $showEnd = true): string
    {
        if (! RealEstateHelper::isEnabledConsultForm()) {
            return '';
        }

        return parent::renderForm($options, $showStart, $showFields, $showEnd);
    }
}
