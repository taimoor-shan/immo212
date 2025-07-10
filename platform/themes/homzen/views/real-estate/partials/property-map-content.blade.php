<template id="property-map-content">
    <a href="__url__" class="map-listing-item">
        <div class="inner-box">
            <div class="image-box">
             
                    <img src="__image__" alt="__name__">
              
                __status__
            </div>
            <div class="content">
                <!-- Property type badge moved to content area -->
                <span class="property-type-badge ">
                    __category__
</span>
                <!-- Original title (hidden but kept for accessibility/SEO) -->
                <div class="title text-prime">
                    <h7 title="__name__" class="line-clamp-2">__name__</h7>
                </div>
                <!-- Duplicate title element showing price instead -->
                <!-- <div class="title">
                    <span class="line-clamp-2">__price__</span>
                </div> -->
                <!-- Meta-list moved to where location was -->
                <ul class="list-info">
                    <li><x-core::icon name="ti ti-bed" />__bedroom__</li>
                    <li><x-core::icon name="ti ti-bath" />__bathroom__</li>
                    <li><x-core::icon name="ti ti-ruler" />__square__</li>
                </ul>
                <!-- Location moved to where meta-list was -->
                <p class="location">
                    <x-core::icon name="ti ti-map-pin" />
                    __location__
                </p>
            </div>
        </div>
    </a>
</template>
