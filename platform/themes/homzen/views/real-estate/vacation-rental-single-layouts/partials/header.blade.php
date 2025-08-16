<div class="header-property-detail pt-4">
    <div class="content-top d-flex justify-content-between align-items-center">
        <div class="box-name">
            <h2 class="section-title">
                {!! BaseHelper::clean($vacationRental->name) !!}
            </h2>
            <ul class="d-flex align-items-center gap-2 text-dBlue">
            @if ($vacationRental->number_bedroom)
                <li class="meta-item">
                    {{ $vacationRental->number_bedroom }} Bedrooms
                </li>
            @endif
            @if ($vacationRental->number_bathroom)
            <li class="meta-item">
             |
            </li>
            @endif
            @if ($vacationRental->number_bathroom)
                <li class="meta-item">
                    {{ $vacationRental->number_bathroom }} Bathrooms
                </li>
            @endif
            @if ($vacationRental->square)
                <li class="meta-item">
                    | {{ $vacationRental->square_text }}
                </li>
            @endif
            </ul>
            @if ($vacationRental->short_address)
                <p class="meta-item">
                    {{ $vacationRental->short_address }}
                </p>
            @endif
        </div>

        <div class="box-price d-flex flex-column align-items-start align-items-md-end">
            <h4>{{ $vacationRental->price_html }}</h4>
            @if (RealEstateHelper::isEnabledWishlist())
            <ul class="iconText d-flex gap-3">
                <li>
                    <button type="button" class="tf-btn secondary sm" data-type="vacation-rental"
                            data-bb-toggle="add-to-wishlist"
                            data-id="{{ $vacationRental->getKey() }}"
                            data-add-message="{{ __('Added \":name\" to wishlist successfully!', ['name' => $vacationRental->name]) }}"
                            data-remove-message="{{ __('Removed \":name\" from wishlist successfully!', ['name' => $vacationRental->name]) }}"
                    >
                        <x-core::icon name="ti ti-heart" />
                        <span>Save</span>
                    </button>
                </li>
                <li>
                    @include(Theme::getThemeNamespace('views.real-estate.partials.social-sharing'), ['model' => $vacationRental])
                </li>
            </ul>
        @endif
        </div>
    </div>
</div>
