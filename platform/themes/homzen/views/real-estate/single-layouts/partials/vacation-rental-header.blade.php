<div class="header-property-detail pt-4">
    <div class="content-top d-flex justify-content-between align-items-center">
        <div class="box-name">
            <!-- {!! BaseHelper::clean($vacationRental->status_html) !!} -->
            <h2 class="section-title">
                {!! BaseHelper::clean($vacationRental->name) !!}
            </h2>
            @if ($vacationRental->short_address)
                <p class="meta-item">

                    {{ $vacationRental->short_address }}
                </p>
            @endif
            <ul class="d-flex align-items-center gap-2 text-dBlue">
                @if ($vacationRental->minimum_stay)
                    <li class="meta-item">
                        {{ $vacationRental->minimum_stay }}
                        {{ $vacationRental->minimum_stay == 1 ? __('night min') : __('nights min') }}
                    </li>
                @endif
                @if ($vacationRental->maximum_guests)
                    <li class="meta-item">
                        |
                    </li>
                    <li class="meta-item">
                        {{ $vacationRental->maximum_guests }} {{ __('guests max') }}
                    </li>
                @endif
                @if ($vacationRental->number_bedroom)
                    <li class="meta-item">
                        |
                    </li>
                    <li class="meta-item">
                        {{ $vacationRental->number_bedroom }} {{ __('beds.') }}
                    </li>
                @endif
            </ul>

        </div>

        <div class="box-price d-flex flex-column align-items-start align-items-md-end">
            <div class="d-flex align-items-center">

                <h4 class="price">{{ $vacationRental->price_format }}</h4>
                <span class="text-variant-1">/{{ __('per night') }}</span>
            </div>

            <ul class="iconText d-flex gap-3">
                @if (RealEstateHelper::isEnabledWishlist())
                    <li>
                        <button type="button" class="roundBtn" data-type="vacation_rental"
                            data-bb-toggle="add-to-wishlist" data-id="{{ $vacationRental->getKey() }}"
                            data-add-message="{{ __('Added ":name" to wishlist successfully!', ['name' => $vacationRental->name]) }}"
                            data-remove-message="{{ __('Removed ":name" from wishlist successfully!', ['name' => $vacationRental->name]) }}">
                            <i class="fa-regular fa-heart"></i>

                        </button>
                    </li>
                @endif
                <li>
                    @include(Theme::getThemeNamespace('views.real-estate.partials.social-sharing'), [
                        'model' => $vacationRental,
                    ])
                </li>
            </ul>
        </div>
    </div>
</div>
