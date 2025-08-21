<div class="header-property-detail pt-4">
    <div class="content-top d-flex justify-content-between align-items-center">
        <div class="box-name">
            <!-- {!! BaseHelper::clean($property->status_html) !!} -->
            <h2 class="section-title">
                {!! BaseHelper::clean($property->name) !!}
            </h2>
            @if ($property->short_address)
                <p class="meta-item">
                    {{ $property->short_address }}
                </p>
            @endif
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

        </div>

        <div class="box-price d-flex flex-column align-items-start align-items-md-end">
            <h4>{{ $property->price_html }}</h4>

            <ul class="iconText d-flex gap-3">
                 @if (RealEstateHelper::isEnabledWishlist())
                <li>
                    <button type="button" class="tf-btn secondary sm" data-type="property"
                            data-bb-toggle="add-to-wishlist"
                            data-id="{{ $property->getKey() }}"
                            data-add-message="{{ __('Added ":name" to wishlist successfully!', ['name' => $property->name]) }}"
                            data-remove-message="{{ __('Removed ":name" from wishlist successfully!', ['name' => $property->name]) }}"
                    >
                        <x-core::icon name="ti ti-heart" />
                        <span>Save</span>
                    </button>
                </li>
                        @endif
                <li>
                @include(Theme::getThemeNamespace('views.real-estate.partials.social-sharing'), ['model' => $property])
                </li>
            </ul>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const shareBtn = document.getElementById('shareDropdownBtn');
    const shareMenu = document.getElementById('shareDropdownMenu');

    if (shareBtn && shareMenu) {
        // Toggle dropdown on button click
        shareBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            if (shareMenu.style.display === 'none' || shareMenu.style.display === '') {
                shareMenu.style.display = 'block';
            } else {
                shareMenu.style.display = 'none';
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!shareBtn.contains(e.target) && !shareMenu.contains(e.target)) {
                shareMenu.style.display = 'none';
            }
        });

        // Prevent dropdown from closing when clicking inside menu
        shareMenu.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
});
</script>
