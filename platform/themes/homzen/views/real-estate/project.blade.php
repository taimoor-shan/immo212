@php
    Theme::set('breadcrumbEnabled', 'no');

    Theme::asset()->usePath()->add('fancybox', 'plugins/fancybox/jquery.fancybox.min.css');
    Theme::asset()->container('footer')->usePath()->add('fancybox', 'plugins/fancybox/jquery.fancybox.min.js');
    Theme::asset()->usePath()->add('leaflet', 'plugins/leaflet/leaflet.css');
    Theme::asset()->container('footer')->usePath()->add('leaflet', 'plugins/leaflet/leaflet.js');
    Theme::asset()->usePath()->add('project-properties-table', 'css/project-properties-table.css');
    Theme::layout('full-width');
    Theme::set('pageTitle', $project->name);

    $projectProperties = app(\Botble\RealEstate\Repositories\Interfaces\PropertyInterface::class)
        ->getPropertiesByConditions(
            [
                're_properties.project_id' => $project->getKey(),
            ],
            100, // Get up to 100 properties
            \Botble\RealEstate\Facades\RealEstateHelper::getPropertyRelationsQuery(),
        );
@endphp

@include(Theme::getThemeNamespace('views.real-estate.single-layouts.partials.gallery-slider'), ['model' => $project])

<section class="flat-section  pt-4  flat-property-detail ">
    <div class="container">

        <div class="row">
            <div class="col-lg-8">
                {!! apply_filters('before_single_content_detail', null, $project) !!}
                <div class="header-property-detail px-0 m-0 pt-0 mb-4">
                    <div class="content-top d-flex justify-content-between align-items-center mb-0 pb-0">
                        <div class="box-name">
                            {!! BaseHelper::clean($project->status_html) !!}
                            <h4 class="title link">
                                {!! BaseHelper::clean($project->name) !!}
                            </h4>
                        </div>

                        @if ($project->price_from || $project->price_to)
                            <div class="box-price d-flex align-items-center">
                                <h4>{{ $project->formatted_price }}</h4>
                            </div>
                        @endif
                    </div>
                    @include(Theme::getThemeNamespace('views.real-estate.partials.meta'), ['model' => $project])
                </div>
                @if ($project->content)
                    <div class="single-property-element single-property-desc">
                        <div class="h7 title fw-6">{{ __('Description') }}</div>
                        <div class="text-variant-1">
                            <div class="ck-content single-detail">
                                {!! BaseHelper::clean($project->content) !!}
                            </div>
                        </div>
                    </div>
                @endif
                @if ($videoUrl = $project->getMetaData('video_url', true))
                    <div class="single-property-element single-property-video">
                        <div class="h7 title fw-6">{{ __('Video') }}</div>
                        <div class="img-video">
                            <img src="{{ RvMedia::getImageUrl($project->getMetaData('video_thumbnail', true)) ?: \Botble\Theme\Supports\Youtube::getThumbnail($videoUrl) }}"
                                alt="{{ $project->name }}">
                            <a href="{{ $videoUrl }}" @if(\Botble\Theme\Supports\Youtube::isYoutubeURL($videoUrl))
                            data-fancybox="gallery2" @endif class="btn-video">
                                <x-core::icon name="ti ti-player-play-filled" />
                            </a>
                        </div>
                    </div>
                @endif
                <div class="single-property-element single-property-overview">
                    <div class="h7 title fw-6 mb-4">{{ __('Overview') }}</div>
                    <div class="row row-cols-sm-2 row-cols-lg-3 g-3 g-lg-4 info-box">
                        <div class="col item">
                            <div class="box-icon w-52">
                                <x-core::icon name="ti ti-home" />
                            </div>
                            <div class="content">
                                <span class="label">{{ __('Project ID:') }}</span>
                                <span>{{ $project->unique_id ?: $project->getKey() }}</span>
                            </div>
                        </div>
                        @if($project->categories->isNotEmpty())
                            <div class="col item">
                                <div class="box-icon w-52">
                                    <x-core::icon name="ti ti-category" />
                                </div>
                                <div class="content">
                                    <span class="label">{{ __('Type:') }}</span>
                                    <span>
                                        {{ $project->categories->map(fn($item) => $item->name)->implode(', ') }}
                                    </span>
                                </div>
                            </div>
                        @endif
                        @if ($project->investor->name)
                            <div class="col item">
                                <div class="box-icon w-52">
                                    <x-core::icon name="ti ti-category" />
                                </div>
                                <div class="content">
                                    <span class="label">{{ __('Investor:') }}</span>
                                    <span>{{ $project->investor->name }}</span>
                                </div>
                            </div>
                        @endif
                        @if ($project->number_block)
                            <div class="col item">
                                <div class="box-icon w-52">
                                    <x-core::icon name="ti ti-packages" />
                                </div>
                                <div class="content">
                                    <span class="label">{{ __('Blocks:') }}</span>
                                    <span>{{ number_format($project->number_block) }}</span>
                                </div>
                            </div>
                        @endif
                        @if ($project->number_floor)
                            <div class="col item">
                                <div class="box-icon w-52">
                                    <x-core::icon name="ti ti-stairs" />
                                </div>
                                <div class="content">
                                    <span class="label">{{ __('Floors:') }}</span>
                                    <span>{{ number_format($project->number_floor) }}</span>
                                </div>
                            </div>
                        @endif
                        @if ($project->number_flat)
                            <div class="col item">
                                <div class="box-icon w-52">
                                    <x-core::icon name="ti ti-building" />
                                </div>
                                <div class="content">
                                    <span class="label">{{ __('Flats:') }}</span>
                                    <span>{{ number_format($project->number_flat) }}</span>
                                </div>
                            </div>
                        @endif
                        @if ($project->square)
                            <div class="col item">
                                <div class="box-icon w-52">
                                    <x-core::icon name="ti ti-ruler-2" />
                                </div>
                                <div class="content">
                                    <span class="label">{{ __('Square:') }}</span>
                                    <span>{{ $project->square_text }}</span>
                                </div>
                            </div>
                        @endif
                        @foreach ($project->customFields as $customField)
                            @continue(!$customField->value)
                            <div class="col item">
                                <div class="box-icon w-52">
                                    <x-core::icon name="ti ti-box" />
                                </div>
                                <div class="content">
                                    <span class="label">{!! BaseHelper::clean($customField->name) !!}:</span>
                                    <span>{!! BaseHelper::clean($customField->value) !!}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @if ($project->features->isNotEmpty())
                    <div class="single-property-element single-property-feature">
                        <div class="h7 title fw-6">{{ __('Amenities and features') }}</div>
                        <div class="box-feature">
                            <ul>
                                @foreach ($project->features as $feature)
                                    <li class="feature-item">
                                        @if ($feature->icon)
                                            {!! BaseHelper::renderIcon($feature->icon) !!}
                                        @endif
                                        {{ $feature->name }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
                @if ($project->facilities->isNotEmpty())
                    <div class="single-property-element single-property-nearby">
                        <div class="h7 title fw-6">{{ __('What’s nearby?') }}</div>
                        <p class="">
                            {{ __("Explore nearby amenities to precisely locate your property and identify surrounding conveniences, providing a comprehensive overview of the living environment and the property's convenience.") }}
                        </p>
                        <ul class="grid-3 box-nearby">
                            @foreach ($project->facilities as $facility)
                                <li class="item-nearby">
                                    <span class="label">
                                        @if($facility->icon)
                                            {!! BaseHelper::renderIcon($facility->icon) !!}
                                        @endif
                                        {{ $facility->name }}:
                                    </span>
                                    <span class="fw-6">{{ $facility->pivot->distance }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($projectProperties->isNotEmpty())
                    <div class="single-property-element pt-0 flat-properties-list">

                        <div class="box-title mb-2">
                            <div class="h7 title fw-6">{{ __('Sublistings') }}</div>
                            <!-- <h2 class="title fw-6">{{ __('Available Properties in :name', ['name' => $project->name]) }}</h2>
                            <p class="text-variant-1 mt-3">{{ __('Total :count properties in this project', ['count' => $projectProperties->count()]) }}</p> -->
                        </div>

                        <!-- Properties Table for Desktop -->
                        <div class="properties-table-wrapper d-none d-lg-block">
                            <div class="table-responsive ">
                                <table class="table table-hover align-middle table-striped">
                                    <tbody>
                                        @foreach($projectProperties as $property)
                                            <tr>
                                                @if($property->number_bedroom)
                                                    <td>
                                                        <span class="text-variant-1">{{ $property->number_bedroom }} Bedroom</span>
                                                    </td>
                                                @endif
                                                <td>
                                                    @if($property->floor_name)
                                                        <span class="text-variant-1">{{ $property->floor_name }}</span>
                                                    @elseif($property->number_floor)
                                                        <span class="text-variant-1">Floor {{ $property->number_floor }}</span>
                                                    @else
                                                        <span class="text-muted">--</span>
                                                    @endif
                                                </td>
                                                <td class="fw-6 text-prime">
                                                    {{ format_price($property->price, $property->currency) }}
                                                    @if($property->period && $property->type == 'rent')
                                                        <span class="text-variant-1 fw-normal">/
                                                            {{ $property->period->label() }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($property->square)
                                                        <span>{{ $property->square_text }}</span>
                                                    @else
                                                        <span class="text-muted">--</span>
                                                    @endif
                                                </td>


                                                <td>
                                                    {!! BaseHelper::clean($property->status->toHtml()) !!}
                                                </td>
                                                @if(($property->floor_plan_document) || ($property->floor_plan_image))
                                                    <td>
                                                        @if($property->floor_plan_document)
                                                            <a class="tf-btn secondary sm"
                                                                href="{{ RvMedia::url($property->floor_plan_document) }}"
                                                                target="_blank">
                                                                {{ __('Floor Plan') }}
                                                            </a>
                                                        @elseif($property->floor_plan_image)
                                                            <a class="tf-btn secondary sm"
                                                                href="{{ RvMedia::url($property->floor_plan_image) }}" target="_blank">
                                                                {{ __('Floor Plan') }}
                                                            </a>
                                                        @endif
                                                    </td>
                                                @endif
                                                <td>


                                                    <a href="{{ $property->url }}" class="text-prime">
                                                        {!! BaseHelper::renderIcon('ti ti-chevron-right') !!}
                                                    </a>

                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Properties Grid for Mobile/Tablet -->
                        <!-- <div class="d-lg-none">
                            <div class="row g-4">
                                @foreach($projectProperties as $property)
                                    <div class="col-12 col-md-6">
                                        <div class="property-card bg-surface rounded-3 p-3">
                                            <div class="d-flex gap-3 mb-3">
                                                <div class="property-thumb rounded-2 overflow-hidden flex-shrink-0" style="width: 100px; height: 80px;">
                                                    <a href="{{ $property->url }}">
                                                        {{ RvMedia::image($property->image, $property->name, 'thumb') }}
                                                    </a>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <a href="{{ $property->url }}" class="fw-6 text-dark text-decoration-none d-block mb-1">
                                                        {{ Str::limit($property->name, 40) }}
                                                    </a>
                                                    @if($property->category)
                                                        <span class="badge bg-primary-subtle text-primary">{{ $property->category->name }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="property-details">
                                                <div class="row g-2 mb-3">
                                                    <div class="col-6">
                                                        <div class="text-variant-1 small">{{ __('Price') }}</div>
                                                        <div class="fw-6 text-prime">
                                                            {{ format_price($property->price, $property->currency) }}
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="text-variant-1 small">{{ __('Status') }}</div>
                                                        <div>{!! BaseHelper::clean($property->status->toHtml()) !!}</div>
                                                    </div>
                                                </div>

                                                <div class="d-flex gap-3 mb-3 text-variant-1">
                                                    @if($property->number_bedroom)
                                                        <span><i class="icon icon-bed me-1"></i>{{ $property->number_bedroom }}</span>
                                                    @endif

                                                    @if($property->floor_name || $property->number_floor)
                                                        <span><i class="icon icon-stairs me-1"></i>{{ $property->floor_name ?: 'Floor ' . $property->number_floor }}</span>
                                                    @endif

                                                    @if($property->square)
                                                        <span><i class="icon icon-ruler me-1"></i>{{ $property->square_text }}</span>
                                                    @endif
                                                </div>

                                                @if($property->short_address)
                                                    <div class="text-variant-1 small mb-3">
                                                        <i class="icon icon-mapPin me-1"></i>
                                                        {{ $property->short_address }}
                                                    </div>
                                                @endif

                                                <div class="d-flex gap-2">
                                                    @if($property->floor_plan_image || $property->floor_plan_document)
                                                        <div class="dropdown">
                                                            <button class="tf-btn outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                                {{ __('Floor Plan') }}
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                @if($property->floor_plan_image)
                                                                    <li><a class="dropdown-item" href="{{ RvMedia::url($property->floor_plan_image) }}" target="_blank">
                                                                        <i class="icon icon-image me-2"></i>{{ __('View Image') }}
                                                                    </a></li>
                                                                @endif
                                                                @if($property->floor_plan_document)
                                                                    <li><a class="dropdown-item" href="{{ RvMedia::url($property->floor_plan_document) }}" target="_blank">
                                                                        <i class="icon icon-file me-2"></i>{{ __('Download Document') }}
                                                                    </a></li>
                                                                @endif
                                                            </ul>
                                                        </div>
                                                    @endif
                                                    <a href="{{ $property->url }}" class="tf-btn primary flex-grow-1">
                                                        {{ __('View') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div> -->

                    </div>
                @endif
                <div class="single-property-element single-property-map">
                    <div class="h7 title fw-6">{{ __('Location') }}</div>
                    @if (theme_option('real_estate_show_map_on_single_detail_page', 'yes') === 'yes')
                        @if ($project->latitude && $project->longitude)
                            <div data-bb-toggle="detail-map" id="map" style="min-height: 400px;"
                                data-tile-layer="{{ RealEstateHelper::getMapTileLayer() }}"
                                data-center="{{ json_encode([$project->latitude, $project->longitude]) }}"
                                data-map-icon="{{ $project->map_icon }}"></div>
                        @else
                            <iframe width="100%" style="min-height: 400px"
                                src="https://maps.google.com/maps?q={{ urlencode($project->location) }}%20&t=&z=13&ie=UTF8&iwloc=&output=embed"
                                frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
                        @endif
                    @endif

                    @if ($locationOnMap = ($project->location ?: $project->short_address))
                        @php
                            $mapUrl = 'https://www.google.com/maps/search/' . urlencode($locationOnMap);

                            if ($project->latitude && $project->longitude) {
                                $mapUrl = 'https://maps.google.com/?q=' . $project->latitude . ',' . $project->longitude;
                            }
                        @endphp
                        <ul class="info-map">
                            <li>
                                <div class="fw-6">{{ __('Address') }}</div>
                                <a class="mt-4 text-variant-1" href="{{ $mapUrl }}" target="_blank">
                                    {{ $locationOnMap }}
                                </a>
                            </li>
                        </ul>
                    @endif

                    <!-- @include(Theme::getThemeNamespace('views.real-estate.partials.social-sharing'), ['model' => $project]) -->
                </div>

                {!! apply_filters('after_single_content_detail', null, $project) !!}

                {!! apply_filters(BASE_FILTER_PUBLIC_COMMENT_AREA, null, $project) !!}

                @include(Theme::getThemeNamespace('views.real-estate.single-layouts.partials.reviews'), ['model' => $project])
            </div>
            <div class="col-lg-4">
                <div class="widget-sidebar wrapper-sidebar-right">
                    <div class="widget-box single-property-contact bg-surface">
                        <div class="h7 title fw-6">{{ __('Contact Agency') }}</div>

                        @if (!RealEstateHelper::hideAgentInfoInPropertyDetailPage() && ($account = $project->author))
                            <div class="box-avatar">
                                <div class="avatar avt-100 round">
                                    <a href="{{ $account->url }}" class="d-block">
                                        {{ RvMedia::image($account->avatar?->url ?: $account->avatar_url, $account->name) }}
                                    </a>
                                </div>
                                <div class="info line-clamp-1">
                                    <div class="text-1 name">
                                        <a href="{{ $account->url }}">{{ $account->name }}</a>
                                    </div>
                                    @if ($account->phone && !setting('real_estate_hide_agency_phone', false))
                                        <a href="tel:{{ $account->phone }}" class="info-item">{{ $account->phone }}</a>
                                    @elseif($hotline = theme_option('hotline'))
                                        <a href="tel:{{ $hotline }}" class="info-item">{{ $hotline }}</a>
                                    @endif
                                    @if ($account->email && !setting('real_estate_hide_agency_email', false))
                                        <a href="mailto:{{ $account->email }}" class="info-item">{{ $account->email }}</a>
                                    @endif
                                </div>
                            </div>
                        @endif

                        {!! apply_filters('project_right_details_info', null, $project) !!}

                        {!! apply_filters('before_consult_form', null, $project) !!}

                        {!! \Botble\RealEstate\Forms\Fronts\ConsultForm::create()
    ->formClass('contact-form')
    ->setFormInputWrapperClass('ip-group')
    ->modify('content', 'textarea', ['attr' => ['class' => '']])
    ->modify('submit', 'submit', ['attr' => ['class' => 'tf-btn primary w-100']])
    ->add('type', 'hidden', ['attr' => ['value' => 'project']])
    ->add('data_id', 'hidden', ['attr' => ['value' => $project->getKey()]])
    ->addBefore('content', 'data_name', 'text', ['label' => false, 'attr' => ['value' => $project->name, 'disabled' => true]])
    ->renderForm()
                            !!}

                        {!! apply_filters('after_consult_form', null, $project) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<template id="map-popup-content">
    <div class="map-popup-content">
        <a class="map-popup-content-thumb" href="{{ $project->url }}">
            {{ RvMedia::image($project->image_thumb, $project->name, lazy: false) }}
            {!! BaseHelper::clean($project->status_html) !!}
        </a>
        <div class="map-popup-content__details">
            <h5 class="map-popup-content__title">
                <a href="{{ $project->url }}" target="_blank" class="map-popup-content__link">{{ $project->name }}</a>
            </h5>
            <div class="map-popup-content__price">{{ $project->category_name }}</div>
            <div class="map-popup-content__city">
                <x-core::icon name="ti ti-map-pin" />
                {{ $project->short_address }}
            </div>
        </div>
    </div>
</template>