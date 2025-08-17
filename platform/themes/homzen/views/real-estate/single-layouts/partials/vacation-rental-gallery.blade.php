@php
    $images = [];
    if (! empty($vacationRental->images)) {
        foreach ($vacationRental->images as $image) {
            $images[] = RvMedia::getImageUrl($image, null, false, RvMedia::getDefaultImage());
        }
    }
    
    if (empty($images) && $vacationRental->image) {
        $images[] = RvMedia::getImageUrl($vacationRental->image, null, false, RvMedia::getDefaultImage());
    }
@endphp

@if (count($images) > 0)
    <div class="single-vacation-rental-gallery">
        <div class="gallery-container">
            @if (count($images) == 1)
                {{-- Single image layout --}}
                <div class="single-image">
                    <a href="{{ $images[0] }}" data-fancybox="vacation-rental-gallery" class="gallery-item">
                        <img src="{{ $images[0] }}" alt="{{ $vacationRental->name }}" class="img-fluid">
                        <div class="gallery-overlay">
                            <x-core::icon name="ti ti-zoom-in" />
                        </div>
                    </a>
                </div>
            @elseif (count($images) <= 4)
                {{-- Grid layout for 2-4 images --}}
                <div class="gallery-grid gallery-grid-{{ count($images) }}">
                    @foreach ($images as $index => $image)
                        <div class="gallery-item-wrapper">
                            <a href="{{ $image }}" data-fancybox="vacation-rental-gallery" class="gallery-item">
                                <img src="{{ $image }}" alt="{{ $vacationRental->name }}" class="img-fluid">
                                <div class="gallery-overlay">
                                    <x-core::icon name="ti ti-zoom-in" />
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                {{-- Grid layout for 5+ images with "View all" button --}}
                <div class="gallery-grid gallery-grid-5">
                    @foreach (array_slice($images, 0, 5) as $index => $image)
                        <div class="gallery-item-wrapper {{ $index === 4 ? 'view-all-trigger' : '' }}">
                            <a href="{{ $image }}" data-fancybox="vacation-rental-gallery" class="gallery-item">
                                <img src="{{ $image }}" alt="{{ $vacationRental->name }}" class="img-fluid">
                                <div class="gallery-overlay">
                                    @if ($index === 4 && count($images) > 5)
                                        <div class="view-all-text">
                                            <x-core::icon name="ti ti-photo" />
                                            <span>{{ __('View all :count photos', ['count' => count($images)]) }}</span>
                                        </div>
                                    @else
                                        <x-core::icon name="ti ti-zoom-in" />
                                    @endif
                                </div>
                            </a>
                        </div>
                    @endforeach
                    
                    {{-- Hidden images for fancybox --}}
                    @foreach (array_slice($images, 5) as $image)
                        <a href="{{ $image }}" data-fancybox="vacation-rental-gallery" style="display: none;"></a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endif

<style>
.single-vacation-rental-gallery {
    margin-bottom: 30px;
}

.gallery-container {
    border-radius: 12px;
    overflow: hidden;
}

.single-image {
    position: relative;
    height: 400px;
}

.gallery-grid {
    display: grid;
    gap: 8px;
    height: 400px;
}

.gallery-grid-2 {
    grid-template-columns: 1fr 1fr;
}

.gallery-grid-3 {
    grid-template-columns: 2fr 1fr 1fr;
    grid-template-rows: 1fr 1fr;
}

.gallery-grid-3 .gallery-item-wrapper:first-child {
    grid-row: 1 / 3;
}

.gallery-grid-4 {
    grid-template-columns: 2fr 1fr 1fr;
    grid-template-rows: 1fr 1fr;
}

.gallery-grid-4 .gallery-item-wrapper:first-child {
    grid-row: 1 / 3;
}

.gallery-grid-5 {
    grid-template-columns: 2fr 1fr 1fr;
    grid-template-rows: 1fr 1fr;
}

.gallery-grid-5 .gallery-item-wrapper:first-child {
    grid-row: 1 / 3;
}

.gallery-item-wrapper {
    position: relative;
    overflow: hidden;
}

.gallery-item {
    display: block;
    position: relative;
    width: 100%;
    height: 100%;
    overflow: hidden;
}

.gallery-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.gallery-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    color: white;
    font-size: 24px;
}

.gallery-item:hover .gallery-overlay {
    opacity: 1;
}

.gallery-item:hover img {
    transform: scale(1.05);
}

.view-all-text {
    text-align: center;
    font-size: 16px;
    font-weight: 500;
}

.view-all-text x-core\\:icon {
    display: block;
    margin-bottom: 8px;
    font-size: 24px;
}

@media (max-width: 768px) {
    .gallery-grid {
        height: 300px;
    }
    
    .single-image {
        height: 300px;
    }
    
    .gallery-grid-3,
    .gallery-grid-4,
    .gallery-grid-5 {
        grid-template-columns: 1fr 1fr;
        grid-template-rows: 1fr 1fr;
    }
    
    .gallery-grid-3 .gallery-item-wrapper:first-child,
    .gallery-grid-4 .gallery-item-wrapper:first-child,
    .gallery-grid-5 .gallery-item-wrapper:first-child {
        grid-row: 1 / 2;
    }
}
</style>
