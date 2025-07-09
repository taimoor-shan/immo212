<?php

use Botble\Base\Forms\FieldOptions\RadioFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\UiSelectorFieldOption;
use Botble\Base\Forms\Fields\RadioField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\UiSelectorField;
use Botble\Location\Models\City;
use Botble\Location\Models\State;
use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Enums\PropertyStatusEnum;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Models\Property;
use Botble\Shortcode\Compilers\Shortcode as ShortcodeCompiler;
use Carbon\Carbon;
use Botble\Shortcode\Facades\Shortcode;
use Botble\Theme\Facades\Theme;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Theme\Homzen\Forms\ShortcodeForm;

if (! is_plugin_active('location')) {
    return;
}

Event::listen(RouteMatched::class, function (): void {
    Shortcode::register('location', __('Location'), __('Location'), function (ShortcodeCompiler $shortcode) {
        $cityIds = Shortcode::fields()->getIds('city_ids', $shortcode);
        $stateIds = Shortcode::fields()->getIds('state_ids', $shortcode);
        $destination = $shortcode->destination === 'property' ? 'properties' : 'projects';

        $locations = match ($shortcode->type) {
            'city' => City::query()
                ->wherePublished()
                ->whereIn('id', $cityIds)
                ->when(is_plugin_active('real-estate'), function ($query) {
                    $query->whereIn('id', function ($subQuery) {
                        $subQuery->select('city_id')
                            ->from('re_properties')
                            ->whereNotNull('city_id')
                            ->distinct();

                        // Apply active property conditions manually
                        $subQuery->where('moderation_status', ModerationStatusEnum::APPROVED);

                        // Exclude excepted statuses
                        foreach (RealEstateHelper::exceptedPropertyStatuses() as $status) {
                            $subQuery->where('status', '!=', $status);
                        }

                        // Apply not expired conditions
                        $subQuery->where(function ($expiredQuery) {
                            $expiredQuery->where('expire_date', '>=', Carbon::now()->toDateTimeString())
                                ->orWhere('never_expired', true);
                        });
                    });
                })
                ->select(['id', 'name', 'image', 'slug'])
                ->oldest('order')
                ->oldest('name')
                ->get()
                ->when(is_plugin_active('real-estate'), function (Collection $collection) use ($destination): void {
                    $collection->transform(fn (City $city) => $city->setAttribute('url', route("public.$destination-by-city", $city->slug))); // @phpstan-ignore-line
                }),
            'state' => State::query()
                ->wherePublished()
                ->whereIn('id', $stateIds)
                ->select(['id', 'name', 'image', 'slug'])
                ->oldest('order')
                ->oldest('name')
                ->get()
                ->when(is_plugin_active('real-estate'), function (Collection $collection) use ($destination): void {
                    $collection->transform(fn (State $state) => $state->setAttribute('url', route("public.$destination-by-state", $state->slug))); // @phpstan-ignore-line
                }),
            default => collect(),
        };

        if ($locations->isEmpty()) {
            return null;
        }

        return Theme::partial('shortcodes.location.index', compact('shortcode', 'locations'));
    });

    Shortcode::setPreviewImage('location', Theme::asset()->url('images/shortcodes/location/style-1.png'));

    Shortcode::setAdminConfig('location', function (array $attributes) {
        return ShortcodeForm::createFromArray($attributes)
            ->lazyLoading()
            ->add(
                'style',
                UiSelectorField::class,
                UiSelectorFieldOption::make()
                    ->choices(
                        collect(range(1, 5))
                            ->mapWithKeys(fn ($number) => [
                                $number => [
                                    'label' => __('Style :number', ['number' => $number]),
                                    'image' => Theme::asset()->url("images/shortcodes/location/style-$number.png"),
                                ],
                            ])
                            ->all()
                    )
            )
            ->addSectionHeadingFields()
            ->add(
                'type',
                RadioField::class,
                RadioFieldOption::make()
                    ->label(__('Location type'))
                    ->choices([
                        'city' => __('City'),
                        'state' => __('State'),
                    ])
                    ->defaultValue('city')
                    ->selected(Arr::get($attributes, 'type')),
            )
            ->add(
                'city_ids',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(__('Cities'))
                    ->collapsible('type', 'city', Arr::get($attributes, 'type', 'city'))
                    ->searchable()
                    ->multiple()
                    ->choices(
                        City::query()
                            ->wherePublished()
                            ->when(is_plugin_active('real-estate'), function ($query) {
                                $query->whereIn('id', function ($subQuery) {
                                    $subQuery->select('city_id')
                                        ->from('re_properties')
                                        ->whereNotNull('city_id')
                                        ->distinct();

                                    // Apply active property conditions manually
                                    $subQuery->where('moderation_status', ModerationStatusEnum::APPROVED);

                                    // Exclude excepted statuses
                                    foreach (RealEstateHelper::exceptedPropertyStatuses() as $status) {
                                        $subQuery->where('status', '!=', $status);
                                    }

                                    // Apply not expired conditions
                                    $subQuery->where(function ($expiredQuery) {
                                        $expiredQuery->where('expire_date', '>=', Carbon::now()->toDateTimeString())
                                            ->orWhere('never_expired', true);
                                    });
                                });
                            })
                            ->pluck('name', 'id')
                            ->all()
                    )
                    ->selected(explode(',', Arr::get($attributes, 'city_ids', '')))
            )
            ->add(
                'state_ids',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(__('States'))
                    ->collapsible('type', 'state', Arr::get($attributes, 'type', 'city'))
                    ->searchable()
                    ->multiple()
                    ->choices(
                        State::query()
                            ->wherePublished()
                            ->pluck('name', 'id')
                            ->all()
                    )
                    ->selected(explode(',', Arr::get($attributes, 'state_ids', '')))
            )
            ->add(
                'destination',
                RadioField::class,
                RadioFieldOption::make()
                    ->choices([
                        'property' => __('Property'),
                        'project' => __('Project'),
                    ])
                    ->label(__('Destination type'))
                    ->helperText(__('Selecting an option will redirect you to a list of properties or projects page.'))
                    ->defaultValue('property')
                    ->selected(Arr::get($attributes, 'destination')),
            )
            ->addBackgroundColorField(defaultValue: '#f7f7f7')
            ->addSectionButtonAction()
            // Slider fields - only show for carousel styles (1, 4)
            ->add(
                'is_autoplay',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(__('Is autoplay?'))
                    ->choices(['yes' => __('Yes'), 'no' => __('No')])
                    ->collapsible('style', [1, 4], Arr::get($attributes, 'style', 1))
            )
            ->add(
                'autoplay_speed',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(__('Autoplay speed (if autoplay enabled)'))
                    ->choices(
                        array_combine(
                            [2000, 3000, 4000, 5000, 6000, 7000, 8000, 9000, 10000],
                            [2000, 3000, 4000, 5000, 6000, 7000, 8000, 9000, 10000]
                        )
                    )
                    ->collapsible('style', [1, 4], Arr::get($attributes, 'style', 1))
            )
            ->add(
                'is_loop',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(__('Loop?'))
                    ->choices(['yes' => __('Continuously'), 'no' => __('Stop on the last slide')])
                    ->collapsible('style', [1, 4], Arr::get($attributes, 'style', 1))
            );
    });
});
