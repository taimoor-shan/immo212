<?php

namespace Botble\Theme\Homzen\Forms;

use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\TextField;
use Botble\RealEstate\Forms\Fronts\ConsultForm;

class CustomConsultForm extends ConsultForm
{
    public function setup(): void
    {
        parent::setup();

        // Add custom fields after the standard ones
        $this
            ->addAfter('email', 'preferred_contact_method', SelectField::class, 
                SelectFieldOption::make()
                    ->label(__('Preferred Contact Method'))
                    ->required(false)
                    ->choices([
                        '' => __('Select preferred method'),
                        'email' => __('Email'),
                        'phone' => __('Phone Call'),
                        'whatsapp' => __('WhatsApp'),
                        'both' => __('Both Email & Phone'),
                    ])
            )
            ->addAfter('preferred_contact_method', 'budget_range', SelectField::class,
                SelectFieldOption::make()
                    ->label(__('Budget Range'))
                    ->required(false)
                    ->choices([
                        '' => __('Select your budget range'),
                        '0-100000' => __('Under $100,000'),
                        '100000-250000' => __('$100,000 - $250,000'),
                        '250000-500000' => __('$250,000 - $500,000'),
                        '500000-750000' => __('$500,000 - $750,000'),
                        '750000-1000000' => __('$750,000 - $1,000,000'),
                        '1000000+' => __('Over $1,000,000'),
                    ])
            )
            ->addAfter('budget_range', 'viewing_timeframe', SelectField::class,
                SelectFieldOption::make()
                    ->label(__('When would you like to view?'))
                    ->required(false)
                    ->choices([
                        '' => __('Select viewing timeframe'),
                        'asap' => __('As soon as possible'),
                        'this_week' => __('This week'),
                        'next_week' => __('Next week'),
                        'within_month' => __('Within a month'),
                        'flexible' => __('I am flexible'),
                    ])
            )
            ->addAfter('viewing_timeframe', 'financing_status', SelectField::class,
                SelectFieldOption::make()
                    ->label(__('Financing Status'))
                    ->required(false)
                    ->choices([
                        '' => __('Select financing status'),
                        'cash_buyer' => __('Cash Buyer'),
                        'pre_approved' => __('Pre-approved for mortgage'),
                        'need_financing' => __('Need financing assistance'),
                        'other' => __('Other'),
                    ])
            )
            ->addAfter('financing_status', 'property_requirements', TextareaField::class,
                TextareaFieldOption::make()
                    ->label(__('Specific Requirements'))
                    ->required(false)
                    ->placeholder(__('Any specific requirements or questions about the property?'))
                    ->attr(['rows' => 3])
            )
            ->modify('content', TextareaField::class, 
                TextareaFieldOption::make()
                    ->label(__('Message'))
                    ->required(true)
                    ->placeholder(__('I am interested in this property. Please provide more details about...'))
                    ->attr(['rows' => 4])
            );
    }
}
