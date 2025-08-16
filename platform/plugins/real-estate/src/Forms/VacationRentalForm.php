<?php

namespace Botble\RealEstate\Forms;

use Botble\Base\Facades\Assets;
use Botble\Base\Forms\FieldOptions\ContentFieldOption;
use Botble\Base\Forms\FieldOptions\DescriptionFieldOption;
use Botble\Base\Forms\FieldOptions\HtmlFieldOption;
use Botble\Base\Forms\FieldOptions\MediaImagesFieldOption;
use Botble\Base\Forms\FieldOptions\NameFieldOption;
use Botble\Base\Forms\FieldOptions\OnOffFieldOption;
use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\Fields\EditorField;
use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\Fields\MediaImagesField;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\OnOffField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use Botble\RealEstate\Forms\Fronts\Auth\FieldOptions\TextFieldOption;
use Botble\Location\Fields\Options\SelectLocationFieldOption;
use Botble\Location\Fields\SelectLocationField;
use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Forms\Fields\CategoryMultiField;
use Botble\RealEstate\Http\Requests\VacationRentalRequest;
use Botble\RealEstate\Models\Currency;
use Botble\RealEstate\Models\Feature;
use Botble\RealEstate\Models\VacationRental;
use Botble\Base\Enums\BaseStatusEnum;

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
                'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css',
            ])
            ->addScriptsDirectly([
                'vendor/core/plugins/real-estate/js/real-estate.js',
                'vendor/core/plugins/real-estate/js/components.js',
                'https://cdn.jsdelivr.net/npm/flatpickr',
                'vendor/core/plugins/real-estate/js/calendar-admin.js',
            ]);

        $currencies = Currency::query()->latest('is_default')->orderBy('id')->pluck('title', 'id')->all();

        $selectedCategories = [];
        if ($this->getModel() && $this->getModel()->id) {
            $selectedCategories = $this->getModel()->categories()->pluck('category_id')->all();
        }

        $selectedFeatures = [];
        if ($this->getModel() && $this->getModel()->id) {
            $selectedFeatures = $this->getModel()->features()->pluck('re_features.id')->all();
        }

        $features = Feature::query()
            ->select('id', 'name')
            ->get()
            ->each(function ($item): void {
                $item->name = (string) $item->name;
            });

        $this
            ->model(VacationRental::class)
            ->setValidatorClass(VacationRentalRequest::class)
            
            ->add('name', TextField::class, NameFieldOption::make()->required())
            ->add('description', TextareaField::class, DescriptionFieldOption::make())
            ->add(
                'is_featured',
                OnOffField::class,
                OnOffFieldOption::make()
                    ->label(trans('core/base::forms.is_featured'))
                    ->defaultValue(false)
            )
            ->add(
                'content',
                EditorField::class,
                ContentFieldOption::make()
                    ->label(trans('plugins/real-estate::vacation-rental.content'))
                    ->allowedShortcodes()
            )
            ->add(
                'images[]',
                MediaImagesField::class,
                MediaImagesFieldOption::make()
                    ->label(trans('plugins/real-estate::vacation-rental.images'))
                    ->selected($this->getModel()->id ? $this->getModel()->images : [])
            )
            ->when(is_plugin_active('location'), function (FormAbstract $form): void {
                $form->add(
                    'location_data',
                    SelectLocationField::class,
                    SelectLocationFieldOption::make()
                );
            })
            ->add(
                'location',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/real-estate::vacation-rental.location'))
                    ->placeholder(trans('plugins/real-estate::vacation-rental.location'))
                    ->maxLength(191)
            )
            ->add('rowOpen', HtmlField::class, [
                'html' => '<div class="row">',
            ])
            ->add('latitude', TextField::class, [
                'label' => trans('plugins/real-estate::property.form.latitude'),
                'wrapper' => [
                    'class' => 'form-group mb-3 col-md-6',
                ],
                'attr' => [
                    'placeholder' => 'Ex: 1.462260',
                    'data-counter' => 25,
                ],
                'help_block' => [
                    'tag' => 'a',
                    'text' => trans('plugins/real-estate::property.form.latitude_helper'),
                    'attr' => [
                        'href' => 'https://www.latlong.net/convert-address-to-lat-long.html',
                        'target' => '_blank',
                        'rel' => 'nofollow',
                    ],
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
                'help_block' => [
                    'tag' => 'a',
                    'text' => trans('plugins/real-estate::property.form.longitude_helper'),
                    'attr' => [
                        'href' => 'https://www.latlong.net/convert-address-to-lat-long.html',
                        'target' => '_blank',
                        'rel' => 'nofollow',
                    ],
                ],
            ])
            ->add('rowClose', 'html', [
                'html' => '</div>',
            ])
            
            // Property details row
            ->add('rowOpen1', 'html', [
                'html' => '<div class="row">',
            ])
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
            ->add('rowClose1', 'html', [
                'html' => '</div>',
            ])
            
            // Pricing row
            ->add('rowOpen2', 'html', [
                'html' => '<div class="row">',
            ])
            ->add('price', TextField::class, [
                'label' => trans('plugins/real-estate::vacation-rental.price_per_night'),
                'wrapper' => [
                    'class' => 'form-group mb-3 col-md-6',
                ],
                'attr' => [
                    'id' => 'price-number',
                    'placeholder' => trans('plugins/real-estate::vacation-rental.price_per_night'),
                    'class' => 'form-control input-mask-number',
                    'data-thousands-separator' => RealEstateHelper::getThousandSeparatorForInputMask(),
                    'data-decimal-separator' => RealEstateHelper::getDecimalSeparatorForInputMask(),
                ],
            ])
            ->add('currency_id', 'customSelect', [
                'label' => trans('plugins/real-estate::property.form.currency'),
                'wrapper' => [
                    'class' => 'form-group mb-3 col-md-6',
                ],
                'attr' => [
                    'class' => 'select-full',
                ],
                'choices' => $currencies,
            ])
            ->add('rowClose2', 'html', [
                'html' => '</div>',
            ])
            
            // Vacation rental specific fields
            ->add('rowOpen3', 'html', [
                'html' => '<div class="row">',
            ])
            ->add('check_in_time', 'time', [
                'label' => trans('plugins/real-estate::vacation-rental.check_in_time'),
                'wrapper' => [
                    'class' => 'form-group mb-3 col-md-6',
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
                'value' => $this->getModel()->check_in_time ?: '15:00',
            ])
            ->add('check_out_time', 'time', [
                'label' => trans('plugins/real-estate::vacation-rental.check_out_time'),
                'wrapper' => [
                    'class' => 'form-group mb-3 col-md-6',
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
                'value' => $this->getModel()->check_out_time ?: '11:00',
            ])
            ->add('rowClose3', 'html', [
                'html' => '</div>',
            ])
            
            ->add('rowOpen4', 'html', [
                'html' => '<div class="row">',
            ])
            ->add('minimum_stay', 'number', [
                'label' => trans('plugins/real-estate::vacation-rental.minimum_stay'),
                'wrapper' => [
                    'class' => 'form-group mb-3 col-md-4',
                ],
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1,
                    'placeholder' => trans('plugins/real-estate::vacation-rental.minimum_stay_placeholder'),
                ],
                'value' => $this->getModel()->minimum_stay ?: 1,
            ])
            ->add('maximum_stay', 'number', [
                'label' => trans('plugins/real-estate::vacation-rental.maximum_stay'),
                'wrapper' => [
                    'class' => 'form-group mb-3 col-md-4',
                ],
                'attr' => [
                    'class' => 'form-control',
                    'min' => 0,
                    'placeholder' => trans('plugins/real-estate::vacation-rental.maximum_stay_placeholder'),
                ],
            ])
            ->add('maximum_guests', 'number', [
                'label' => trans('plugins/real-estate::vacation-rental.maximum_guests'),
                'wrapper' => [
                    'class' => 'form-group mb-3 col-md-4',
                ],
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1,
                    'placeholder' => trans('plugins/real-estate::vacation-rental.maximum_guests_placeholder'),
                ],
                'value' => $this->getModel()->maximum_guests ?: 2,
            ])
            ->add('rowClose4', 'html', [
                'html' => '</div>',
            ])
            
            ->add('rowOpen5', 'html', [
                'html' => '<div class="row">',
            ])
            ->add('cleaning_fee', TextField::class, [
                'label' => trans('plugins/real-estate::vacation-rental.cleaning_fee'),
                'wrapper' => [
                    'class' => 'form-group mb-3 col-md-6',
                ],
                'attr' => [
                    'class' => 'form-control input-mask-number',
                    'placeholder' => trans('plugins/real-estate::vacation-rental.cleaning_fee_placeholder'),
                    'data-thousands-separator' => RealEstateHelper::getThousandSeparatorForInputMask(),
                    'data-decimal-separator' => RealEstateHelper::getDecimalSeparatorForInputMask(),
                ],
                'value' => $this->getModel()->cleaning_fee ?: 0,
            ])
            ->add('security_deposit', TextField::class, [
                'label' => trans('plugins/real-estate::vacation-rental.security_deposit'),
                'wrapper' => [
                    'class' => 'form-group mb-3 col-md-6',
                ],
                'attr' => [
                    'class' => 'form-control input-mask-number',
                    'placeholder' => trans('plugins/real-estate::vacation-rental.security_deposit_placeholder'),
                    'data-thousands-separator' => RealEstateHelper::getThousandSeparatorForInputMask(),
                    'data-decimal-separator' => RealEstateHelper::getDecimalSeparatorForInputMask(),
                ],
                'value' => $this->getModel()->security_deposit ?: 0,
            ])
            ->add('rowClose5', 'html', [
                'html' => '</div>',
            ])
            
            ->add('house_rules', TextareaField::class, [
                'label' => trans('plugins/real-estate::vacation-rental.house_rules'),
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 3,
                    'placeholder' => trans('plugins/real-estate::vacation-rental.house_rules_placeholder'),
                ],
            ])
            ->add('cancellation_policy', TextareaField::class, [
                'label' => trans('plugins/real-estate::vacation-rental.cancellation_policy'),
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 3,
                    'placeholder' => trans('plugins/real-estate::vacation-rental.cancellation_policy_placeholder'),
                ],
            ])
            
            ->addMetaBoxes([
                'features' => [
                    'title' => trans('plugins/real-estate::vacation-rental.features'),
                    'content' => view(
                        'plugins/real-estate::partials.form-features',
                        compact('selectedFeatures', 'features')
                    )->render(),
                    'priority' => 1,
                ],
            ])
            
            ->add(
                'status',
                SelectField::class,
                StatusFieldOption::make()
                    ->choices(BaseStatusEnum::labels())
                    ->selected((string) $this->model->status ?: BaseStatusEnum::PUBLISHED)
            )
            ->when($this->getModel()->exists, function (FormAbstract $form): void {
                $form->add(
                    'moderation_status',
                    HtmlField::class,
                    HtmlFieldOption::make()
                        ->label(trans('plugins/real-estate::property.moderation_status'))
                        ->content(view('plugins/real-estate::partials.moderation-status', [
                            'model' => $this->getModel(),
                        ])->render())
                );
            })
            ->add('categories[]', CategoryMultiField::class, [
                'label' => trans('plugins/real-estate::vacation-rental.categories'),
                'choices' => get_property_categories_with_children(),
                'value' => old('categories', $selectedCategories),
            ])
            ->add(
                'unique_id',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/real-estate::property.unique_id'))
                    ->placeholder(trans('plugins/real-estate::property.unique_id'))
                    ->value($this->getModel()->getKey() ? $this->getModel()->unique_id : null)
                    ->maxLength(120)
            )
            ->setBreakFieldPoint('status');
            
        // Add availability calendar for vacation rentals
        if ($this->getModel() && $this->getModel()->id) {
            $this->addMetaBoxes([
                'availability' => [
                    'title' => trans('plugins/real-estate::vacation-rental.availability_calendar'),
                    'content' => view('plugins/real-estate::partials.form-vacation-rental-availability', [
                        'property' => $this->getModel(), // The view expects 'property' variable
                    ])->render(),
                    'priority' => 2,
                ],
            ]);
        }
    }
}
