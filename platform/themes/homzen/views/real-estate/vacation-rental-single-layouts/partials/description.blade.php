@if ($vacationRental->content || $vacationRental->private_notes)
    <div @class(['single-property-desc', $class ?? null])>
        @if($vacationRental->content)
            <div class="h7 title fw-6">{{ __('Description') }}</div>
            <div class="body-2 text-variant-1">
                <div class="ck-content single-detail">
                    {!! BaseHelper::clean($vacationRental->content) !!}
                </div>
            </div>
        @endif

        @if($vacationRental->can_see_private_notes && $vacationRental->private_notes)
            <div class="bd-callout bd-callout-info">
                <div class="h7 title fw-6 mb-2">{{ __('Private Notes') }}</div>

                {!! BaseHelper::clean(nl2br($vacationRental->private_notes)) !!}
            </div>
        @endif
    </div>
@endif

<div @class(['single-property-overview', $class ?? null])>
    <div class="h7 title fw-6">{{ __('Overview') }}</div>
    <div class="row row-cols-sm-2 row-cols-lg-3 g-3 g-lg-4 info-box">
        <div class="col item">
            <div class="box-icon w-52">
                <x-core::icon name="ti ti-home" />
            </div>
            <div class="content">
                <span class="label">{{ __('Vacation Rental ID:') }}</span>
                <span>{{ $vacationRental->unique_id ?: $vacationRental->getKey() }}</span>
            </div>
        </div>
        @if ($vacationRental->categories->isNotEmpty())
            <div class="col item">
                <div class="box-icon w-52">
                    <x-core::icon name="ti ti-category" />
                </div>
                <div class="content">
                    <span class="label">{{ __('Type:') }}</span>
                    <span>
                        {{ $vacationRental->categories->map(fn ($item) => $item->name)->implode(', ') }}
                    </span>
                </div>
            </div>
        @endif
        @if ($vacationRental->number_bedroom)
            <div class="col item">
                <div class="box-icon w-52">
                    <x-core::icon name="ti ti-bed" />
                </div>
                <div class="content">
                    <span class="label">{{ __('Bedrooms:') }}</span>
                    <span>{{ number_format($vacationRental->number_bedroom) }}</span>
                </div>
            </div>
        @endif
        @if ($vacationRental->number_bathroom)
            <div class="col item">
                <div class="box-icon w-52">
                    <x-core::icon name="ti ti-bath" />
                </div>
                <div class="content">
                    <span class="label">{{ __('Bathrooms:') }}</span>
                    <span>{{ number_format($vacationRental->number_bathroom) }}</span>
                </div>
            </div>
        @endif
        @if ($vacationRental->number_floor)
            <div class="col item">
                <div class="box-icon w-52">
                    <x-core::icon name="ti ti-stairs" />
                </div>
                <div class="content">
                    <span class="label">{{ __('Floors:') }}</span>
                    <span>{{ number_format($vacationRental->number_floor) }}</span>
                </div>
            </div>
        @endif
        @if ($vacationRental->square)
            <div class="col item">
                <div class="box-icon w-52">
                    <x-core::icon name="ti ti-ruler-2" />
                </div>
                <div class="content">
                    <span class="label">{{ __('Square:') }}</span>
                    <span>{{ $vacationRental->square_text }}</span>
                </div>
            </div>
        @endif
        @foreach ($vacationRental->customFields as $customField)
            @continue(! $customField->value)
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
