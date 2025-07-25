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
            <ul class="iconText d-flex gap-3">
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
                <li>
                    <div class="property-share-dropdown" style="position: relative; display: inline-block;">
                        <button type="button" class="tf-btn secondary sm" id="shareDropdownBtn">
                            <x-core::icon name="ti ti-share" />
                            <span>Share</span>
                        </button>
                        <div class="share-dropdown-menu" id="shareDropdownMenu" style="display: none; position: absolute; top: 100%; right: 0; background: #ffffff; border: 1px solid #a8beea; border-radius: 6px; padding: 12px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); z-index: 1000; min-width: 200px; margin-top: 5px;">
                            <ul style="list-style: none; margin: 0; padding: 0;">
                                <li style="margin-bottom: 8px;">
                                    <a href="mailto:?subject={{ urlencode($property->name) }}&body={{ urlencode('Check out this property: ' . $property->url) }}"
                                       style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; text-decoration: none; color: #082479; border-radius: 4px; transition: all 0.3s ease;"
                                       onmouseover="this.style.backgroundColor='#e4eaf5'"
                                       onmouseout="this.style.backgroundColor='transparent'">
                                        <x-core::icon name="ti ti-mail" style="width: 16px; height: 16px;" />
                                        <span>Share by email</span>
                                    </a>
                                </li>
                                <li style="margin-bottom: 8px;">
                                    <a href="https://x.com/intent/tweet?url={{ urlencode($property->url) }}&text={{ urlencode($property->name) }}"
                                       target="_blank"
                                       style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; text-decoration: none; color: #082479; border-radius: 4px; transition: all 0.3s ease;"
                                       onmouseover="this.style.backgroundColor='#e4eaf5'"
                                       onmouseout="this.style.backgroundColor='transparent'">
                                        <x-core::icon name="ti ti-brand-x" style="width: 16px; height: 16px;" />
                                        <span>Share on Twitter</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="https://www.facebook.com/sharer.php?u={{ urlencode($property->url) }}"
                                       target="_blank"
                                       style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; text-decoration: none; color: #082479; border-radius: 4px; transition: all 0.3s ease;"
                                       onmouseover="this.style.backgroundColor='#e4eaf5'"
                                       onmouseout="this.style.backgroundColor='transparent'">
                                        <x-core::icon name="ti ti-brand-facebook" style="width: 16px; height: 16px;" />
                                        <span>Share on Facebook</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>
            </ul>
        @endif
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
