<?php

namespace Botble\RealEstate\Forms;

use Botble\Base\Facades\Assets;
use Botble\Base\Forms\FieldOptions\AutocompleteFieldOption;
use Botble\Base\Forms\FieldOptions\ContentFieldOption;
use Botble\Base\Forms\FieldOptions\DescriptionFieldOption;
use Botble\Base\Forms\FieldOptions\HtmlFieldOption;
use Botble\Base\Forms\FieldOptions\MediaImagesFieldOption;
use Botble\Base\Forms\FieldOptions\NameFieldOption;
use Botble\Base\Forms\FieldOptions\OnOffFieldOption;
use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\Fields\AutocompleteField;
use Botble\Base\Forms\Fields\EditorField;
use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\Fields\MediaImagesField;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\OnOffField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use Botble\Location\Fields\Options\SelectLocationFieldOption;
use Botble\Location\Fields\SelectLocationField;
use Botble\RealEstate\Enums\VacationRentalStatusEnum;
use Botble\RealEstate\Forms\Fields\CategoryMultiField;
use Botble\RealEstate\Http\Requests\VacationRentalRequest;
use Botble\RealEstate\Models\Currency;
use Botble\RealEstate\Models\Facility;
use Botble\RealEstate\Models\Feature;
use Botble\RealEstate\Models\VacationRental;
use stdClass;

class VacationRentalForm extends FormAbstract
{
    public function setup(): void
    {
        Assets::usingVueJS()
            ->addStyles('datetimepicker')
            ->addScripts('input-mask')
            ->addStylesDirectly([
                'vendor/core/plugins/real-estate/css/real-estate.css',
                'vendor/core/plugins/real-estate/css/calendar-backend.css',
            ])
            ->addScriptsDirectly([
                'vendor/core/plugins/real-estate/js/real-estate.js',
                'vendor/core/plugins/real-estate/js/components.js',
                'vendor/core/plugins/real-estate/js/vacation-rental-form.js',
                'vendor/core/plugins/real-estate/js/conditional-floor-plans.js',
            ]);

        $currencies = Currency::query()->latest('is_default')->orderBy('id')->pluck('title', 'id')->all();

        $selectedCategories = [];
        if ($this->getModel()) {
            /**
             * @var VacationRental $vacationRental
             */
            $vacationRental = $this->getModel();
            $selectedCategories = $vacationRental->categories()->pluck('category_id')->all();
        }

        $selectedFeatures = [];
        if ($this->getModel()) {
            /**
             * @var VacationRental $vacationRental
             */
            $vacationRental = $this->getModel();
            $selectedFeatures = $vacationRental->features()->pluck('re_features.id')->all();
        }

        $features = Feature::query()
            ->select('id', 'name')
            ->get()
            ->each(function ($item): void {
                $item->name = (string) $item->name;
            });

        $facilities = Facility::query()
            ->select('id', 'name')
            ->get()
            ->each(function ($item): void {
                $item->name = (string) $item->name;
            });

        if ($this->getModel()) {
            /**
             * @var VacationRental $vacationRental
             */
            $vacationRental = $this->getModel();
            $selectedFacilities = $vacationRental->facilities()->select('re_facilities.id', 'distance')->get();
        } else {
            $selectedFacilities = collect();

            $oldSelectedFacilities = old('facilities', []);

            if (! empty($oldSelectedFacilities)) {
                foreach ($oldSelectedFacilities as $oldSelectedFacility) {
                    if (! isset($oldSelectedFacility['id']) || ! isset($oldSelectedFacility['distance'])) {
                        continue;
                    }

                    $item = new stdClass();
                    $item->id = $oldSelectedFacility['id'];
                    $item->distance = $oldSelectedFacility['distance'];

                    $selectedFacilities->add($item);
                }
            }
        }

        $this
            ->model(VacationRental::class)
            ->setValidatorClass(VacationRentalRequest::class)
            ->template('plugins/real-estate::partials.forms.vacation-rental-form')
            ->add('name', TextField::class, NameFieldOption::make()->required())
            ->add('description', TextareaField::class, DescriptionFieldOption::make())
            ->add('content', EditorField::class, ContentFieldOption::make()->allowedShortcodes())
            ->add('images', MediaImagesField::class, MediaImagesFieldOption::make())
            ->add('location', TextField::class, [
                'label' => trans('plugins/real-estate::property.form.location'),
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::property.form.location'),
                    'data-counter' => 300,
                ],
            ])
            ->add('rowOpen1', HtmlField::class, HtmlFieldOption::make()->content('<div class="row">'))
            ->add('number_bedroom', NumberField::class, [
                'label' => trans('plugins/real-estate::property.form.number_bedroom'),
                'wrapper' => [
                    'class' => 'form-group mb-3 col-md-3',
                ],
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::property.form.number_bedroom'),
                ],
            ])
            ->add('number_bathroom', NumberField::class, [
                'label' => trans('plugins/real-estate::property.form.number_bathroom'),
                'wrapper' => [
                    'class' => 'form-group mb-3 col-md-3',
                ],
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::property.form.number_bathroom'),
                ],
            ])
            ->add('number_floor', NumberField::class, [
                'label' => trans('plugins/real-estate::property.form.number_floor'),
                'wrapper' => [
                    'class' => 'form-group mb-3 col-md-3',
                ],
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::property.form.number_floor'),
                ],
            ])
            ->add('square', NumberField::class, [
                'label' => trans('plugins/real-estate::property.form.square', ['unit' => setting('real_estate_square_unit', 'm²')]),
                'wrapper' => [
                    'class' => 'form-group mb-3 col-md-3',
                ],
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::property.form.square', ['unit' => setting('real_estate_square_unit', 'm²')]),
                ],
            ])
            ->add('rowClose1', HtmlField::class, HtmlFieldOption::make()->content('</div>'))
            ->add('rowOpen2', HtmlField::class, HtmlFieldOption::make()->content('<div class="row">'))
            ->add('price', NumberField::class, [
                'label' => trans('plugins/real-estate::property.form.price_per_night'),
                'wrapper' => [
                    'class' => 'form-group mb-3 col-md-4',
                ],
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::property.form.price_per_night'),
                    'class' => 'form-control input-mask-number',
                ],
            ])
            ->add('currency_id', SelectField::class, [
                'label' => trans('plugins/real-estate::property.form.currency'),
                'wrapper' => [
                    'class' => 'form-group mb-3 col-md-4',
                ],
                'attr' => [
                    'class' => 'select-search-full',
                ],
                'choices' => $currencies,
            ])
            ->add('never_expired', OnOffField::class, OnOffFieldOption::make()
                ->label(trans('plugins/real-estate::property.never_expired'))
                ->wrapperAttributes(['class' => 'form-group mb-3 col-md-4'])
                ->defaultValue(true)
            )
            ->add('rowClose2', HtmlField::class, HtmlFieldOption::make()->content('</div>'));

        if (is_plugin_active('location')) {
            $this->add(
                'location_data',
                SelectLocationField::class,
                SelectLocationFieldOption::make()
            );
        }

        $this
            ->add('rowOpen3', HtmlField::class, HtmlFieldOption::make()->content('<div class="row">'))
            ->add('latitude', TextField::class, [
                'label' => trans('plugins/real-estate::property.form.latitude'),
                'wrapper' => [
                    'class' => 'form-group mb-3 col-md-6',
                ],
                'attr' => [
                    'placeholder' => 'Ex: 1.462260',
                    'data-counter' => 25,
                ],
            ])
            ->add('longitude', TextField::class, [
                'label' => trans('plugins/real-estate::property.form.longitude'),
                'wrapper' => [
                    'class' => 'form-group mb-3 col-md-6',
                ],
                'attr' => [
                    'placeholder' => 'Ex: 103.812530',
                    'data-counter' => 25,
                ],
            ])
            ->add('rowClose3', HtmlField::class, HtmlFieldOption::make()->content('</div>'))

            // Vacation rental specific fields
            ->add('vacation_rental_section', HtmlField::class, HtmlFieldOption::make()->content('<h4>' . trans('plugins/real-estate::vacation-rental.vacation_rental_details') . '</h4>'))
            ->add('rowOpen4', HtmlField::class, HtmlFieldOption::make()->content('<div class="row">'))
            ->add('check_in_time', 'time', [
                'label' => trans('plugins/real-estate::property.form.check_in_time'),
                'wrapper' => [
                    'class' => 'form-group mb-3 col-md-6',
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '14:00',
                ],
            ])
            ->add('check_out_time', 'time', [
                'label' => trans('plugins/real-estate::property.form.check_out_time'),
                'wrapper' => [
                    'class' => 'form-group mb-3 col-md-6',
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '11:00',
                ],
            ])
            ->add('rowClose4', HtmlField::class, HtmlFieldOption::make()->content('</div>'))
            ->add('rowOpen5', HtmlField::class, HtmlFieldOption::make()->content('<div class="row">'))
            ->add('minimum_stay', NumberField::class, [
                'label' => trans('plugins/real-estate::property.form.minimum_stay'),
                'wrapper' => [
                    'class' => 'form-group mb-3 col-md-4',
                ],
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::property.form.minimum_stay'),
                    'min' => 1,
                ],
            ])
            ->add('maximum_stay', NumberField::class, [
                'label' => trans('plugins/real-estate::property.form.maximum_stay'),
                'wrapper' => [
                    'class' => 'form-group mb-3 col-md-4',
                ],
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::property.form.maximum_stay'),
                    'min' => 0,
                ],
            ])
            ->add('maximum_guests', NumberField::class, [
                'label' => trans('plugins/real-estate::property.form.maximum_guests'),
                'wrapper' => [
                    'class' => 'form-group mb-3 col-md-4',
                ],
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::property.form.maximum_guests'),
                    'min' => 1,
                ],
            ])
            ->add('rowClose5', HtmlField::class, HtmlFieldOption::make()->content('</div>'))
            ->add('rowOpen6', HtmlField::class, HtmlFieldOption::make()->content('<div class="row">'))
            ->add('cleaning_fee', NumberField::class, [
                'label' => trans('plugins/real-estate::property.form.cleaning_fee'),
                'wrapper' => [
                    'class' => 'form-group mb-3 col-md-6',
                ],
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::property.form.cleaning_fee'),
                    'class' => 'form-control input-mask-number',
                ],
            ])
            ->add('security_deposit', NumberField::class, [
                'label' => trans('plugins/real-estate::property.form.security_deposit'),
                'wrapper' => [
                    'class' => 'form-group mb-3 col-md-6',
                ],
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::property.form.security_deposit'),
                    'class' => 'form-control input-mask-number',
                ],
            ])
            ->add('rowClose6', HtmlField::class, HtmlFieldOption::make()->content('</div>'))
            ->add('house_rules', TextareaField::class, [
                'label' => trans('plugins/real-estate::property.form.house_rules'),
                'attr' => [
                    'rows' => 4,
                    'placeholder' => trans('plugins/real-estate::property.form.house_rules_placeholder'),
                ],
            ])
            ->add('cancellation_policy', SelectField::class, [
                'label' => trans('plugins/real-estate::property.form.cancellation_policy'),
                'attr' => [
                    'class' => 'select-search-full',
                ],
                'choices' => [
                    'flexible' => trans('plugins/real-estate::property.cancellation_policies.flexible'),
                    'moderate' => trans('plugins/real-estate::property.cancellation_policies.moderate'),
                    'strict' => trans('plugins/real-estate::property.cancellation_policies.strict'),
                    'super_strict' => trans('plugins/real-estate::property.cancellation_policies.super_strict'),
                ],
            ])

            // Categories and Features
            ->add('categories[]', CategoryMultiField::class, [
                'label' => trans('plugins/real-estate::property.form.categories'),
                'choices' => get_property_categories_with_children(),
                'value' => old('categories', $selectedCategories),
            ])

            ->add('is_featured', OnOffField::class, OnOffFieldOption::make()
                ->label(trans('core/base::forms.is_featured'))
                ->defaultValue(false)
            )
            ->add('auto_renew', OnOffField::class, OnOffFieldOption::make()
                ->label(trans('plugins/real-estate::property.form.auto_renew'))
                ->defaultValue(true)
            )
            ->add('private_notes', TextareaField::class, TextareaFieldOption::make()
                ->label(trans('plugins/real-estate::property.private_notes'))
                ->placeholder(trans('plugins/real-estate::property.private_notes_helper'))
                ->rows(4)
            )
            ->setBreakFieldPoint('auto_renew')
            ->addMetaBoxes([
                'features' => [
                    'title' => trans('plugins/real-estate::property.form.features'),
                    'content' => view(
                        'plugins/real-estate::partials.form-features',
                        compact('selectedFeatures', 'features')
                    )->render(),
                    'priority' => 1,
                ],
                'facilities' => [
                    'title' => trans('plugins/real-estate::property.distance_key'),
                    'content' => view(
                        'plugins/real-estate::partials.form-facilities',
                        compact('facilities', 'selectedFacilities')
                    ),
                    'priority' => 0,
                ],
                'vacation_rental_availability' => [
                    'title' => trans('plugins/real-estate::vacation-rental.availability_calendar'),
                    'content' => view(
                        'plugins/real-estate::partials.form-vacation-rental-availability',
                        ['vacationRental' => $this->getModel()]
                    )->render(),
                    'priority' => 2,
                ],
            ])
            ->add(
                'status',
                SelectField::class,
                StatusFieldOption::make()
                    ->choices(VacationRentalStatusEnum::labels())
                    ->selected((string) $this->model->status ?: VacationRentalStatusEnum::DRAFT)
            );
    }
}
