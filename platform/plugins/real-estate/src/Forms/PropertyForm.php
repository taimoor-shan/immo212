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
use Botble\Base\Forms\FieldOptions\RepeaterFieldOption;
use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\Fields\AutocompleteField;
use Botble\Base\Forms\Fields\EditorField;
use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\Fields\MediaImagesField;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\OnOffField;
use Botble\Base\Forms\Fields\RepeaterField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use Botble\Location\Fields\Options\SelectLocationFieldOption;
use Botble\Location\Fields\SelectLocationField;
use Botble\RealEstate\Enums\CustomFieldEnum;
use Botble\RealEstate\Enums\PropertyPeriodEnum;
use Botble\RealEstate\Enums\PropertyStatusEnum;
use Botble\RealEstate\Enums\PropertyTypeEnum;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Forms\Fields\CategoryMultiField;
use Botble\RealEstate\Forms\Fronts\Auth\FieldOptions\TextFieldOption;
use Botble\RealEstate\Http\Requests\PropertyRequest;
use Botble\RealEstate\Models\Currency;
use Botble\RealEstate\Models\CustomField;
use Botble\RealEstate\Models\Facility;
use Botble\RealEstate\Models\Feature;
use Botble\RealEstate\Models\Project;
use Botble\RealEstate\Models\Property;
use stdClass;

class PropertyForm extends FormAbstract
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

        $projects = Project::query()
            ->select('name', 'id')
            ->latest()
            ->get()
            ->mapWithKeys(fn (Project $item) => [$item->getKey() => $item->name]) // @phpstan-ignore-line
            ->all();

        $currencies = Currency::query()->latest('is_default')->orderBy('id')->pluck('title', 'id')->all();

        $selectedCategories = [];
        if ($this->getModel()) {
            /**
             * @var Property $property
             */
            $property = $this->getModel();

            $selectedCategories = $property->categories()->pluck('category_id')->all();
        }

        $selectedFeatures = [];
        if ($this->getModel()) {
            /**
             * @var Property $property
             */
            $property = $this->getModel();

            $selectedFeatures = $property->features()->pluck('id')->all();
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
             * @var Property $property
             */
            $property = $this->getModel();

            $selectedFacilities = $property->facilities()->select('re_facilities.id', 'distance')->get();
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

        $squareUnit = setting('real_estate_square_unit', 'm²') ? sprintf('(%s)', setting('real_estate_square_unit', 'm²')) : null;

        if ($this->getModel() && $this->getModel()->exists && $this->getModel()->getKey() && is_in_admin(true)) {
            add_filter('base_action_form_actions_extra', function (?string $html) {
                return $html . view(
                    'plugins/real-estate::partials.forms.duplicate-button',
                    [
                            'url' => route('property.duplicate-property', $this->getModel()->id),
                            'label' => trans('plugins/real-estate::property.duplicate'),
                        ]
                )->render();
            });
        }

        // Add "Back to Project" button if property belongs to a project
        if ($this->getModel() && $this->getModel()->project_id && is_in_admin(true)) {
            add_filter('base_action_form_actions_extra', function (?string $html) {
                $backButton = '<a href="' . route('project.edit', $this->getModel()->project_id) . '" class="btn btn-outline-secondary me-2">
                    <i class="fa fa-arrow-left"></i> Back to Project
                </a>';
                return $backButton . $html;
            });
        }



        $this
            ->model(Property::class)
            ->setValidatorClass(PropertyRequest::class)
            ->template('plugins/real-estate::partials.forms.property-form')

            ->add('name', TextField::class, NameFieldOption::make()->required())
            ->add('type', SelectField::class, [
                'label' => trans('plugins/real-estate::property.form.type'),
                'required' => true,
                'choices' => PropertyTypeEnum::labels(),
                'attr' => [
                    'id' => 'type',
                ],
            ])
            ->add('description', TextareaField::class, DescriptionFieldOption::make())
            ->add(
                'is_featured',
                OnOffField::class,
                OnOffFieldOption::make()
                    ->label(trans('core/base::forms.is_featured'))
                    ->defaultValue(false)
            )
            ->addOpenCollapsible('is_featured', '1', $this->model->is_featured == 1)
            ->add(
                'featured_priority',
                NumberField::class,
                [
                    'label' => trans('plugins/real-estate::property.form.featured_priority'),
                    'attr' => [
                        'placeholder' => trans('plugins/real-estate::property.form.featured_priority'),
                        'min' => 0,
                    ],
                    'help_block' => [
                        'text' => trans('plugins/real-estate::property.form.featured_priority_helper'),
                    ],
                    'wrapper' => [
                        'class' => 'mb-0',
                    ],
                ]
            )
            ->addCloseCollapsible('is_featured', '1')
            ->add(
                'content',
                EditorField::class,
                ContentFieldOption::make()
                    ->label(trans('plugins/real-estate::property.form.content'))
                    ->allowedShortcodes()
            )
            ->add(
                'images[]',
                MediaImagesField::class,
                MediaImagesFieldOption::make()
                    ->label(trans('plugins/real-estate::property.form.images'))
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
                    ->label(trans('plugins/real-estate::property.form.location'))
                    ->placeholder(trans('plugins/real-estate::property.form.location'))
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
                'label' => trans('plugins/real-estate::property.form.square', ['unit' => $squareUnit]),
                'wrapper' => [
                    'class' => 'form-group mb-3 col-md-3',
                ],
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::property.form.square', ['unit' => $squareUnit]),
                ],
            ])
            ->add('rowClose1', 'html', [
                'html' => '</div>',
            ])
            ->add('rowOpen2', 'html', [
                'html' => '<div class="row">',
            ])
            ->add('price', TextField::class, [
                'label' => trans('plugins/real-estate::property.form.price'),
                'wrapper' => [
                    'class' => 'form-group mb-3 col-md-6',
                ],
                'attr' => [
                    'id' => 'price-number',
                    'placeholder' => trans('plugins/real-estate::property.form.price'),
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
            ->add('period', 'customSelect', [
                'label' => trans('plugins/real-estate::property.form.period'),
                'required' => true,
                'wrapper' => [
                    'class' => 'form-group mb-3 period-form-group col-md-4' . ($this->getModel()->type != PropertyTypeEnum::RENT ? ' hidden' : null),
                ],
                'attr' => [
                    'class' => 'select-search-full',
                    'id' => 'period',
                ],
                'choices' => PropertyPeriodEnum::labels(),
            ])
            ->add('vacation_rental_fields_start', 'html', [
                'html' => '<div class="vacation-rental-fields" style="display: none;">',
            ])
            ->add('rowOpen3', 'html', [
                'html' => '<div class="row">',
            ])
            ->add('check_in_time', 'time', [
                'label' => trans('plugins/real-estate::property.form.check_in_time'),
                'wrapper' => [
                    'class' => 'form-group mb-3 col-md-6',
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('check_out_time', 'time', [
                'label' => trans('plugins/real-estate::property.form.check_out_time'),
                'wrapper' => [
                    'class' => 'form-group mb-3 col-md-6',
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('rowClose3', 'html', [
                'html' => '</div>',
            ])
            ->add('rowOpen4', 'html', [
                'html' => '<div class="row">',
            ])
            ->add('minimum_stay', 'number', [
                'label' => trans('plugins/real-estate::property.form.minimum_stay'),
                'wrapper' => [
                    'class' => 'form-group mb-3 col-md-4',
                ],
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1,
                    'placeholder' => trans('plugins/real-estate::property.form.minimum_stay_placeholder'),
                ],
            ])
            ->add('maximum_stay', 'number', [
                'label' => trans('plugins/real-estate::property.form.maximum_stay'),
                'wrapper' => [
                    'class' => 'form-group mb-3 col-md-4',
                ],
                'attr' => [
                    'class' => 'form-control',
                    'min' => 0,
                    'placeholder' => trans('plugins/real-estate::property.form.maximum_stay_placeholder'),
                ],
            ])
            ->add('maximum_guests', 'number', [
                'label' => trans('plugins/real-estate::property.form.maximum_guests'),
                'wrapper' => [
                    'class' => 'form-group mb-3 col-md-4',
                ],
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1,
                    'placeholder' => trans('plugins/real-estate::property.form.maximum_guests_placeholder'),
                ],
            ])
            ->add('rowClose4', 'html', [
                'html' => '</div>',
            ])
            ->add('rowOpen5', 'html', [
                'html' => '<div class="row">',
            ])
            ->add('cleaning_fee', TextField::class, [
                'label' => trans('plugins/real-estate::property.form.cleaning_fee'),
                'wrapper' => [
                    'class' => 'form-group mb-3 col-md-6 monetary-field',
                ],
                'attr' => [
                    'class' => 'form-control input-mask-number',
                    'placeholder' => trans('plugins/real-estate::property.form.cleaning_fee_placeholder'),
                    'data-thousands-separator' => RealEstateHelper::getThousandSeparatorForInputMask(),
                    'data-decimal-separator' => RealEstateHelper::getDecimalSeparatorForInputMask(),
                ],
            ])
            ->add('security_deposit', TextField::class, [
                'label' => trans('plugins/real-estate::property.form.security_deposit'),
                'wrapper' => [
                    'class' => 'form-group mb-3 col-md-6 monetary-field',
                ],
                'attr' => [
                    'class' => 'form-control input-mask-number',
                    'placeholder' => trans('plugins/real-estate::property.form.security_deposit_placeholder'),
                    'data-thousands-separator' => RealEstateHelper::getThousandSeparatorForInputMask(),
                    'data-decimal-separator' => RealEstateHelper::getDecimalSeparatorForInputMask(),
                ],
            ])
            ->add('rowClose5', 'html', [
                'html' => '</div>',
            ])
            ->add('house_rules', TextareaField::class, [
                'label' => trans('plugins/real-estate::property.form.house_rules'),
                'wrapper' => [
                    'class' => 'form-group mb-3',
                ],
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 3,
                    'placeholder' => trans('plugins/real-estate::property.form.house_rules_placeholder'),
                ],
            ])
            ->add('cancellation_policy', 'customSelect', [
                'label' => trans('plugins/real-estate::property.form.cancellation_policy'),
                'wrapper' => [
                    'class' => 'form-group mb-3 policy-field',
                ],
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
            ->add('vacation_rental_fields_end', 'html', [
                'html' => '</div>',
            ])
            ->add('rowClose2', 'html', [
                'html' => '</div>',
            ])
            ->add('never_expired', 'onOff', [
                'label' => trans('plugins/real-estate::property.never_expired'),
                'default_value' => true,
                'help_block' => [
                    'text' => __('You can change Properties Expired Time (days) in Admin → Settings → Real Estate → General'),
                ],
            ])
            ->addOpenCollapsible('never_expired', '1', $this->model->never_expired ?? true)
            ->add('auto_renew', 'onOff', [
                'label' => trans('plugins/real-estate::property.renew_notice', ['days' => RealEstateHelper::propertyExpiredDays()]),
                'default_value' => false,
                'help_block' => [
                    'text' => __('You need to set up a cronjob in Admin → Platform Administration → Cronjob first. Once configured, it will automatically renew posts if the author has sufficient credits.'),
                ],
            ])
            ->addCloseCollapsible('never_expired', '1')
            ->add(
                'private_notes',
                TextareaField::class,
                TextareaFieldOption::make()
                    ->label(trans('plugins/real-estate::property.private_notes'))
                    ->helperText(trans('plugins/real-estate::property.private_notes_helper'))
                    ->rows(2)
                    ->colspan(2)
            )
            // Conditional Floor Plans Section
            ->add('floor_plans_section_start', 'html', [
                'html' => '<div class="floor-plans-conditional-section">
                    <div class="alert alert-info">
                        <strong>Floor Plans:</strong> The interface will automatically adjust based on the number of floors.
                        <br><small>• Single floor (1): Simple image and document upload</small>
                        <br><small>• Multiple floors (2+): Detailed floor-by-floor information</small>
                    </div>',
            ])

            // Single Floor Plan Fields (shown when number_floor = 1)
            ->add('single_floor_plan_start', 'html', [
                'html' => '<div id="single-floor-plan" class="single-floor-fields" style="display: none;">
                    <h5 class="mb-3">Single Floor Plan</h5>',
            ])
            ->add(
                'floor_name',
                TextField::class,
                [
                    'label' => 'Floor Name/Number',
                    'attr' => [
                        'placeholder' => 'e.g., Ground Floor, Floor 1, Floor 2, Penthouse',
                        'class' => 'form-control single-floor-field',
                    ],
                    'help_block' => [
                        'text' => 'Specify which floor this property/unit is located on'
                    ],
                    'wrapper' => [
                        'class' => 'form-group mb-3 single-floor-field',
                    ],
                ]
            )
            ->add(
                'floor_plan_image',
                'mediaImage',
                [
                    'label' => 'Floor Plan Image',
                    'help_block' => [
                        'text' => 'Upload floor plan as image (JPG, PNG, etc.)'
                    ],
                    'wrapper' => [
                        'class' => 'form-group mb-3 single-floor-field',
                    ],
                ]
            )
            ->add(
                'floor_plan_document',
                'mediaFile',
                [
                    'label' => 'Floor Plan Document',
                    'help_block' => [
                        'text' => 'Upload floor plan as document (PDF, DWG, etc.)'
                    ],
                    'wrapper' => [
                        'class' => 'form-group mb-3 single-floor-field',
                    ],
                ]
            )
            ->add('single_floor_plan_end', 'html', [
                'html' => '</div>',
            ])

            // Multiple Floor Plans Repeater (shown when number_floor > 1)
            ->add('multi_floor_plans_start', 'html', [
                'html' => '<div id="multi-floor-plans" class="multi-floor-fields" style="display: none;">
                    <h5 class="mb-3">Multiple Floor Plans</h5>',
            ])
            ->add(
                'floor_plans',
                RepeaterField::class,
                RepeaterFieldOption::make()
                    ->label('Floor Plans')
                    ->fields([
                        [
                            'type' => 'text',
                            'label' => 'Floor Name',
                            'attributes' => [
                                'name' => 'name',
                                'value' => null,
                                'options' => [
                                    'class' => 'form-control',
                                    'placeholder' => 'e.g., Ground Floor, 1st Floor, 2nd Floor',
                                ],
                            ],
                        ],
                        [
                            'type' => 'textarea',
                            'label' => 'Floor Description',
                            'attributes' => [
                                'name' => 'description',
                                'value' => null,
                                'options' => [
                                    'class' => 'form-control',
                                    'rows' => 2,
                                    'placeholder' => 'Optional description of this floor',
                                ],
                            ],
                        ],
                        [
                            'type' => 'mediaImage',
                            'label' => 'Floor Plan Image',
                            'attributes' => [
                                'name' => 'image',
                                'value' => null,
                            ],
                        ],
                        [
                            'type' => 'number',
                            'label' => 'Bedrooms',
                            'attributes' => [
                                'name' => 'bedrooms',
                                'value' => null,
                                'options' => [
                                    'class' => 'form-control',
                                    'min' => 0,
                                    'placeholder' => '0',
                                ],
                            ],
                        ],
                        [
                            'type' => 'number',
                            'label' => 'Bathrooms',
                            'attributes' => [
                                'name' => 'bathrooms',
                                'value' => null,
                                'options' => [
                                    'class' => 'form-control',
                                    'min' => 0,
                                    'placeholder' => '0',
                                ],
                            ],
                        ],
                    ])
                    ->value($this->getModel()->floor_plans ?? [])
                    ->toArray()
            )
            ->add('multi_floor_plans_end', 'html', [
                'html' => '</div>',
            ])
            ->add('floor_plans_section_end', 'html', [
                'html' => '</div>',
            ])
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
                        ['property' => $this->getModel()]
                    )->render(),
                    'priority' => 2,
                    'attributes' => [
                        'style' => 'display: none;',
                        'data-type' => 'vacation_rental',
                    ],
                ],
            ])
            ->add(
                'status',
                SelectField::class,
                StatusFieldOption::make()
                    ->choices(PropertyStatusEnum::labels())
                    ->selected((string) $this->model->status ?: PropertyStatusEnum::SELLING)
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
                'label' => trans('plugins/real-estate::property.form.categories'),
                'choices' => get_property_categories_with_children(),
                'value' => old('categories', $selectedCategories),
            ])
            ->add(
                'unique_id',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/real-estate::property.unique_id'))
                    ->placeholder(trans('plugins/real-estate::property.unique_id'))
                    ->value($this->getModel()->getKey() ? $this->getModel()->unique_id : $this->getModel()->generateUniqueId())
                    ->maxLength(120)
            )
            ->when(! empty($projects), function () use ($projects): void {
                $this
                    ->add('project_id', 'customSelect', [
                        'label' => trans('plugins/real-estate::property.form.project'),
                        'attr' => [
                            'class' => 'select-search-full',
                        ],
                        'choices' => [0 => trans('plugins/real-estate::property.select_project')] + $projects,
                    ]);
            })
            ->setBreakFieldPoint('status')
            ->add(
                'author_id',
                AutocompleteField::class,
                AutocompleteFieldOption::make()
                    ->label(trans('plugins/real-estate::property.account'))
                    ->ajaxUrl(route('account.list'))
                    ->when($this->getModel()->author_id, function (AutocompleteFieldOption $option): void {
                        $option->choices([$this->model->author->id => $this->model->author->name]);
                    })
                    ->emptyValue(trans('plugins/real-estate::property.select_account'))
                    ->allowClear()
            )
            ->when(RealEstateHelper::isEnabledCustomFields() && (! setting('real_estate_show_all_custom_fields_in_form_by_default', false) || $this->getModel()->custom_fields_array), function (FormAbstract $form): void {
                Assets::addScriptsDirectly('vendor/core/plugins/real-estate/js/custom-fields.js');

                $customFields = CustomField::query()->select(['name', 'id', 'type'])->get();

                $form->addMetaBoxes([
                    'custom_fields_box' => [
                        'title' => trans('plugins/real-estate::custom-fields.name'),
                        'content' => view('plugins/real-estate::custom-fields.custom-fields', [
                            'options' => CustomFieldEnum::labels(),
                            'customFields' => $customFields,
                            'model' => $this->model,
                            'ajax' => is_in_admin(true) ? route('real-estate.custom-fields.get-info') : route('public.account.custom-fields.get-info'),
                        ]),
                        'priority' => 0,
                    ],
                ]);
            });
    }
}
