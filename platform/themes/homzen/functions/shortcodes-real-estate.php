<?php

use Botble\Base\Forms\FieldOptions\MediaImageFieldOption;
use Botble\Base\Forms\FieldOptions\NumberFieldOption;
use Botble\Base\Forms\FieldOptions\OnOffFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\UiSelectorFieldOption;
use Botble\Base\Forms\Fields\MediaImageField;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\OnOffField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\UiSelectorField;
use Botble\RealEstate\Enums\PropertyTypeEnum;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\Category;
use Botble\Location\Models\City;
use Botble\RealEstate\Models\Package;
use Botble\RealEstate\Models\Project;
use Botble\Shortcode\Compilers\Shortcode as ShortcodeCompiler;
use Botble\Shortcode\Facades\Shortcode;
use Botble\Theme\Facades\Theme;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Theme\Homzen\Actions\GetPropertiesAction;
use Theme\Homzen\Actions\GetVacationRentalsAction;
use Theme\Homzen\Forms\ShortcodeForm;

app()->booted(function (): void {
    if (! is_plugin_active('real-estate')) {
        return;
    }

    Event::listen(RouteMatched::class, function (): void {
        Shortcode::register('properties', __('Properties'), __('Properties'), function (ShortcodeCompiler $shortcode): ?string {
            $tabs = collect();

            $categoryIds = Shortcode::fields()->getIds('category_ids', $shortcode) ?: [];
            $cityId = $shortcode->city_id ?: null;
            $authorId = $shortcode->author_id ?: null;

            if ($shortcode->style == 2 && $categoryIds) {
                $tabs = Category::query()
                    ->wherePublished()
                    ->whereIn('id', $categoryIds)
                    ->get()
                    ->mapWithKeys(fn ($item) => [$item->id => $item->name]);

                $properties = app(GetPropertiesAction::class)
                    ->handle(
                        limit: (int) $shortcode->limit ?: 4,
                        type: $shortcode->type,
                        featured: (bool) $shortcode->is_featured,
                        categoryIds: $categoryIds,
                        cityId: $cityId,
                        authorId: $authorId
                    );
            } else {
                $properties = (new GetPropertiesAction())->handle(
                    limit: (int) $shortcode->limit ?: 4,
                    type: $shortcode->type,
                    featured: (bool) $shortcode->is_featured,
                    categoryIds: $categoryIds,
                    cityId: $cityId,
                    authorId: $authorId
                );
            }

            return Theme::partial('shortcodes.properties.index', compact('shortcode', 'properties', 'tabs', 'categoryIds'));
        });

        Shortcode::setPreviewImage('properties', Theme::asset()->url('images/shortcodes/properties/style-2.png'));

        Shortcode::register('vacation-rentals', __('Vacation Rentals'), __('Vacation Rentals'), function (ShortcodeCompiler $shortcode): ?string {
            $categoryIds = Shortcode::fields()->getIds('category_ids', $shortcode) ?: [];
            $cityId = $shortcode->city_id ?: null;
            $authorId = $shortcode->author_id ?: null;
            $isFeatured = (bool) $shortcode->is_featured;

            $vacationRentals = (new GetVacationRentalsAction())->handle(
                limit: (int) $shortcode->limit ?: 4,
                categoryId: null,
                featured: $isFeatured,
                categoryIds: $categoryIds,
                cityId: $cityId,
                authorId: $authorId
            );

            return Theme::partial('shortcodes.vacation-rentals.index', compact('shortcode', 'vacationRentals', 'categoryIds'));
        });

        Shortcode::setPreviewImage('vacation-rentals', Theme::asset()->url('images/shortcodes/vacation-rentals/style-8.png'));

        Shortcode::setAdminConfig('vacation-rentals', function (array $attributes): ShortcodeForm {
            return ShortcodeForm::createFromArray($attributes)
                ->lazyLoading()
                ->add(
                    'style',
                    UiSelectorField::class,
                    UiSelectorFieldOption::make()
                        ->label(__('Style'))
                        ->choices([
                            8 => [
                                'label' => __('Style :number', ['number' => 8]),
                                'image' => Theme::asset()->url('images/shortcodes/vacation-rentals/style-8.png'),
                            ],
                        ])
                        ->selected(Arr::get($attributes, 'style', 8))
                        ->defaultValue(8)
                )
                ->addSectionHeadingFields()
                ->add(
                    'category_ids',
                    SelectField::class,
                    SelectFieldOption::make()
                        ->label(__('Categories'))
                        ->multiple()
                        ->searchable()
                        ->choices(
                            Category::query()
                                ->wherePublished()
                                ->pluck('name', 'id')
                                ->all()
                        )
                        ->selected(explode(',', Arr::get($attributes, 'category_ids', '')))
                )
                ->add(
                    'is_featured',
                    OnOffField::class,
                    OnOffFieldOption::make()
                        ->label(__('Only show featured vacation rentals'))
                )
                ->add(
                    'city_id',
                    SelectField::class,
                    SelectFieldOption::make()
                        ->label(__('City'))
                        ->helperText(__('Select a specific city to filter vacation rentals.'))
                        ->searchable()
                        ->choices(
                            ['' => __('All Cities')] +
                            (is_plugin_active('location') ?
                                City::query()
                                    ->wherePublished()
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->all() : []
                            )
                        )
                        ->selected(Arr::get($attributes, 'city_id', ''))
                )
                ->add(
                    'author_id',
                    SelectField::class,
                    SelectFieldOption::make()
                        ->label(__('Agent'))
                        ->helperText(__('Select a specific agent to filter vacation rentals.'))
                        ->searchable()
                        ->choices(
                            ['' => __('All Agents')] +
                            Account::query()
                                ->orderBy('first_name')
                                ->orderBy('last_name')
                                ->get()
                                ->mapWithKeys(function ($account) {
                                    $name = trim($account->first_name . ' ' . $account->last_name);
                                    return [$account->id => $name ?: $account->username];
                                })
                                ->all()
                        )
                        ->selected(Arr::get($attributes, 'author_id', ''))
                )
                ->addLimitField()
                ->addSectionButtonAction()
                ->addBackgroundColorField();
        });

        Shortcode::setAdminConfig('properties', function (array $attributes): ShortcodeForm {
            return ShortcodeForm::createFromArray($attributes)
                ->lazyLoading()
                ->add(
                    'style',
                    UiSelectorField::class,
                    UiSelectorFieldOption::make()
                        ->label(__('Style'))
                        ->choices(
                            collect(range(1, 8))
                                ->mapWithKeys(fn ($number) => [
                                    $number => [
                                        'label' => __('Style :number', ['number' => $number]),
                                        'image' => Theme::asset()->url("images/shortcodes/properties/style-$number.png"),
                                    ],
                                ])
                                ->all()
                        )
                        ->selected(Arr::get($attributes, 'style', 1))
                        ->defaultValue(1)
                )
                ->addSectionHeadingFields()
                ->add(
                    'category_ids',
                    SelectField::class,
                    SelectFieldOption::make()
                        ->label(__('Categories'))
                        ->helperText(__('Select categories to display as tabs.'))
                        ->multiple()
                        ->searchable()
                        ->choices(
                            Category::query()
                                ->wherePublished()
                                ->pluck('name', 'id')
                                ->all()
                        )
                        ->selected(explode(',', Arr::get($attributes, 'category_ids', '')))
                )
                ->add(
                    'type',
                    SelectField::class,
                    SelectFieldOption::make()
                        ->choices(['' => __('All'), ...PropertyTypeEnum::labels()]),
                )
                ->add(
                    'is_featured',
                    OnOffField::class,
                    OnOffFieldOption::make()
                        ->label(__('Only show featured properties'))
                )
                ->add(
                    'city_id',
                    SelectField::class,
                    SelectFieldOption::make()
                        ->label(__('City'))
                        ->helperText(__('Select a specific city to filter properties.'))
                        ->searchable()
                        ->choices(
                            ['' => __('All Cities')] +
                            (is_plugin_active('location') ?
                                City::query()
                                    ->wherePublished()
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->all() : []
                            )
                        )
                        ->selected(Arr::get($attributes, 'city_id', ''))
                )
                ->add(
                    'author_id',
                    SelectField::class,
                    SelectFieldOption::make()
                        ->label(__('Agent'))
                        ->helperText(__('Select a specific agent to filter properties.'))
                        ->searchable()
                        ->choices(
                            ['' => __('All Agents')] +
                            Account::query()
                                ->orderBy('first_name')
                                ->orderBy('last_name')
                                ->get()
                                ->mapWithKeys(function ($account) {
                                    $name = trim($account->first_name . ' ' . $account->last_name);
                                    return [$account->id => $name ?: $account->username];
                                })
                                ->all()
                        )
                        ->selected(Arr::get($attributes, 'author_id', ''))
                )
                ->addLimitField()
                ->addSectionButtonAction()
                ->add(
                    'background_image',
                    MediaImageField::class,
                    MediaImageFieldOption::make()
                        ->label(__('Background Image'))
                        ->helperText(__('Select background image for this section.'))
                        ->collapsible('style', '6', Arr::get($attributes, 'style', '1'))
                )
                ->addBackgroundColorField();
        });

        Shortcode::register('property-categories', __('Property Categories'), __('Property Categories'), function (ShortcodeCompiler $shortcode): ?string {
            if (! $categoryIds = Shortcode::fields()->getIds('category_ids', $shortcode)) {
                return null;
            }

            $categories = Category::query()
                ->wherePublished()
                ->whereIn('id', $categoryIds)
                ->with('slugable')
                ->withCount('properties')
                ->get();

            if ($categories->isEmpty()) {
                return null;
            }

            return Theme::partial('shortcodes.property-categories.index', compact('shortcode', 'categories'));
        });

        Shortcode::setPreviewImage('property-categories', Theme::asset()->url('images/shortcodes/property-categories/style-1.png'));

        Shortcode::setAdminConfig('property-categories', function (array $attributes): ShortcodeForm {
            return ShortcodeForm::createFromArray($attributes)
                ->lazyLoading()
                ->add(
                    'style',
                    UiSelectorField::class,
                    UiSelectorFieldOption::make()
                        ->label(__('Style'))
                        ->choices([
                            1 => [
                                'label' => __('Style :number', ['number' => 1]),
                                'image' => Theme::asset()->url('images/shortcodes/property-categories/style-1.png'),
                            ],
                            2 => [
                                'label' => __('Style :number', ['number' => 2]),
                                'image' => Theme::asset()->url('images/shortcodes/property-categories/style-2.png'),
                            ],
                            3 => [
                                'label' => __('Grid'),
                                'image' => Theme::asset()->url('images/shortcodes/property-categories/style-3.png'),
                            ],
                        ])
                        ->selected(Arr::get($attributes, 'style', 1))
                        ->defaultValue(1)
                )
                ->addSectionHeadingFields()
                ->add(
                    'category_ids',
                    SelectField::class,
                    SelectFieldOption::make()
                        ->label(__('Categories'))
                        ->multiple()
                        ->searchable()
                        ->choices(
                            Category::query()
                                ->wherePublished()
                                ->pluck('name', 'id')
                                ->all()
                        )
                        ->selected(explode(',', Arr::get($attributes, 'category_ids', '')))
                )
                ->addBackgroundColorField()
                ->addSectionButtonAction()
                ->addSliderFields();
        });

        Shortcode::register('pricing-plan', __('Pricing Plan'), __('Pricing Plan'), function (ShortcodeCompiler $shortcode): ?string {
            if (! $packageIds = Shortcode::fields()->getIds('package_ids', $shortcode)) {
                return null;
            }

            $packages = Package::query()
                ->wherePublished()
                ->whereIn('id', $packageIds)
                ->get();

            if ($packages->isEmpty()) {
                return null;
            }

            return Theme::partial('shortcodes.pricing-plan.index', compact('shortcode', 'packages'));
        });

        Shortcode::setPreviewImage('pricing-plan', Theme::asset()->url('images/shortcodes/pricing-plan.png'));

        Shortcode::setAdminConfig('pricing-plan', function (array $attributes): ShortcodeForm {
            return ShortcodeForm::createFromArray($attributes)
                ->lazyLoading()
                ->addSectionHeadingFields()
                ->add(
                    'package_ids',
                    SelectField::class,
                    SelectFieldOption::make()
                        ->label(__('Packages'))
                        ->multiple()
                        ->searchable()
                        ->choices(
                            Package::query()
                                ->wherePublished()
                                ->pluck('name', 'id')
                                ->all()
                        )
                        ->selected(explode(',', Arr::get($attributes, 'package_ids', '')))
                );
        });

        Shortcode::register('agents', __('Agents'), __('Agents'), function (ShortcodeCompiler $shortcode): ?string {
            if (! $accountIds = Shortcode::fields()->getIds('account_ids', $shortcode)) {
                return null;
            }

            $accounts = Account::query()
                ->whereIn('id', $accountIds)
                ->withCount('properties')
                ->orderByDesc('is_featured')
                ->oldest('first_name')
                ->get();

            if ($accounts->isEmpty()) {
                return null;
            }

            return Theme::partial('shortcodes.agents.index', compact('shortcode', 'accounts'));
        });

        Shortcode::setPreviewImage('agents', Theme::asset()->url('images/shortcodes/agents/style-1.png'));

        Shortcode::setAdminConfig('agents', function (array $attributes) {
            return ShortcodeForm::createFromArray($attributes)
                ->lazyLoading()
                ->add(
                    'style',
                    UiSelectorField::class,
                    UiSelectorFieldOption::make()
                        ->choices(
                            collect(range(1, 2))
                                ->mapWithKeys(fn ($number) => [
                                    $number => [
                                        'label' => __('Style :number', ['number' => $number]),
                                        'image' => Theme::asset()->url("images/shortcodes/agents/style-$number.png"),
                                    ],
                                ])
                                ->all()
                        )
                )
                ->addSectionHeadingFields()
                ->add(
                    'account_ids',
                    SelectField::class,
                    SelectFieldOption::make()
                        ->searchable()
                        ->multiple()
                        ->choices(Account::query()->pluck('username', 'id')->all())
                        ->selected(explode(',', Arr::get($attributes, 'account_ids', '')))
                        ->label(__('Choose agents'))
                )
                ->add(
                    'items_per_row',
                    NumberField::class,
                    NumberFieldOption::make()
                        ->label(__('Items per row'))
                        ->defaultValue(4)
                        ->collapsible('style', '1', Arr::get($attributes, 'style', '1'))
                )
                ->addBackgroundColorField();
        });

        Shortcode::register('featured-projects', __('Featured projects'), __('Featured projects'), function (ShortcodeCompiler $shortcode) {
            if (! RealEstateHelper::isEnabledProjects()) {
                return null;
            }

            $projects = collect();

            if (is_plugin_active('real-estate')) {
                $projects = Project::query()
                    ->where([
                            're_projects.is_featured' => true,
                        ] + RealEstateHelper::getProjectDisplayQueryConditions())
                    ->with(RealEstateHelper::getProjectRelationsQuery())
                    ->orderBy('re_projects.created_at', 'DESC')
                    ->limit((int) $shortcode->limit ?: (int) theme_option('number_of_featured_projects', 6))
                    ->get();
            }

            if ($projects->isEmpty()) {
                return null;
            }

            return Theme::partial('shortcodes.featured-projects.index', [
                'title' => $shortcode->title,
                'subtitle' => $shortcode->subtitle,
                'projects' => $projects,
                'shortcode' => $shortcode,
            ]);
        });

        if (RealEstateHelper::isEnabledProjects()) {
            Shortcode::setAdminConfig('featured-projects', function (array $attributes) {
                return ShortcodeForm::createFromArray($attributes)
                    ->lazyLoading()
                    ->addSectionHeadingFields()
                    ->addLimitField();
            });
        }
    });
});
