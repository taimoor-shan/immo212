@php
    // Check if we have single floor plan data or multiple floor plans
    $hasSingleFloorPlan = $property->floor_plan_image || $property->floor_plan_document || $property->floor_name;
    $hasMultipleFloorPlans = $property->formatted_floor_plans->isNotEmpty();
    $showFloorPlans = $hasSingleFloorPlan || $hasMultipleFloorPlans;
@endphp

@if ($showFloorPlans)
    <div @class(['single-property-floor', $class ?? null])>
        <div class="h7 title fw-6">{{ __('Floor plans') }}</div>
        @if ($hasSingleFloorPlan && !$hasMultipleFloorPlans)
            {{-- Single Floor Plan Display --}}
            <div class="single-floor-plan-display">
                <div class="floor-item single-floor">
                    <div class="floor-header">
                        <div class="inner-left">
                            <span class="fw-6">
                                {{ $property->floor_name ?: __('Floor Plan') }}
                            </span>
                        </div>
                        @if ($property->number_bedroom || $property->number_bathroom)
                            <ul class="inner-right">
                                @if ($property->number_bedroom)
                                    <li class="d-flex align-items-center gap-8">
                                        <x-core::icon name="ti ti-bed" />
                                        {{ $property->number_bedroom == 1 ? __('1 bedroom') : __(':count bedrooms', ['count' => $property->number_bedroom]) }}
                                    </li>
                                @endif
                                @if ($property->number_bathroom)
                                    <li class="d-flex align-items-center gap-8">
                                        <x-core::icon name="ti ti-bath" />
                                        {{ $property->number_bathroom == 1 ? __('1 bathroom') : __(':count bathrooms', ['count' => $property->number_bathroom]) }}
                                    </li>
                                @endif
                            </ul>
                        @endif
                    </div>
                    <div class="floor-content">
                        @if ($property->floor_plan_image)
                            <div class="box-img mb-3">
                                <a href="#" data-fancybox="floor-plan-{{ $property->slug }}" data-src="{{ RvMedia::getImageUrl($property->floor_plan_image) }}">
                                    {{ RvMedia::image($property->floor_plan_image, $property->floor_name ?: __('Floor Plan')) }}
                                </a>
                            </div>
                        @endif
                        @if ($property->floor_plan_document)
                            <div class="floor-document">
                                <a href="{{ RvMedia::getImageUrl($property->floor_plan_document) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <x-core::icon name="ti ti-download" />
                                    {{ __('Download Floor Plan') }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @if ($hasMultipleFloorPlans)
            {{-- Multiple Floor Plans Display (Accordion) --}}
            <ul class="box-floor" id="parent-floor">
                @foreach ($property->formatted_floor_plans as $floorPlan)
                    @php
                        $slug = Str::slug($floorPlan['name']) . '-' . $loop->index;
                    @endphp

                    <li class="floor-item">
                        <div class="floor-header" data-bs-target="#floor-{{ $slug }}" data-bs-toggle="collapse" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="floor-{{ $slug }}">
                            <div class="inner-left">
                                <i class="icon icon-arr-r"></i>
                                <span class="fw-6">{!! BaseHelper::clean($floorPlan['name']) !!}</span>
                            </div>
                            <ul class="inner-right">
                                @if ($floorPlan['bedrooms'])
                                    <li class="d-flex align-items-center gap-8">
                                        <x-core::icon name="ti ti-bed" />
                                        {{ $floorPlan['bedrooms'] }}
                                    </li>
                                @endif

                                @if ($floorPlan['bathrooms'])
                                    <li class="d-flex align-items-center gap-8">
                                        <x-core::icon name="ti ti-bath" />
                                        {{ $floorPlan['bathrooms'] }}
                                    </li>
                                @endif
                            </ul>
                        </div>
                        <div id="floor-{{ $slug }}" class="collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#parent-floor">
                            <div class="faq-body">
                                @if ($floorPlan['description'])
                                    <div class="box-desc text-variant-1 mb-3">
                                        {!! BaseHelper::clean($floorPlan['description']) !!}
                                    </div>
                                @endif
                                @if ($floorPlan['image'])
                                    <div class="box-img">
                                        <a href="#" data-fancybox="floor-plan-{{ $property->slug }}" data-src="{{ RvMedia::getImageUrl($floorPlan['image']) }}">
                                            {{ RvMedia::image($floorPlan['image'], $floorPlan['name']) }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endif
