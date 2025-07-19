<div class="header-property-detail pt-4">
    <div class="content-top d-flex justify-content-between align-items-center">
        <div class="box-name">
            <!-- {!! BaseHelper::clean($property->status_html) !!} -->
            <h2 class="section-title">
                {!! BaseHelper::clean($property->name) !!}
            </h2>
            <ul class="d-flex align-items-center gap-2 text-dBlue">
            @if ($property->number_bedroom)
                <li class="meta-item">
                    {{ $property->number_bedroom }} Bedrooms
                </li>
            @endif
            @if ($property->number_bathroom)
            <li class="meta-item">
             |
            </li>
            @endif
            @if ($property->square)
                <li class="meta-item">
                    {{ $property->square_text }}
                </li>
            @endif
            </ul>
            @if ($property->short_address)
                <p class="meta-item">
                    {{ $property->short_address }}
                </p>
            @endif
        </div>

        <div class="box-price d-flex flex-column align-items-start align-items-md-end">
            <h4>{{ $property->price_html }}</h4>
            @if (RealEstateHelper::isEnabledWishlist())
            <ul class="iconText">
                <li>
                    <button type="button" class="tf-btn secondary sm" data-type="{{ $property instanceof \Botble\RealEstate\Models\Property ? 'property' : 'project' }}"
                            data-bb-toggle="add-to-wishlist"
                            data-id="{{ $property->getKey() }}"
                            data-add-message="{{ __('Added ":name" to wishlist successfully!', ['name' => $property->name]) }}"
                            data-remove-message="{{ __('Removed ":name" from wishlist successfully!', ['name' => $property->name]) }}"
                    >
                        <x-core::icon name="ti ti-heart" />
                        <span>Save</span>
                    </button>
                </li>
            </ul>
        @endif
        </div>
    </div>
</div>
