@if ($vacationRental->latitude && $vacationRental->longitude)
    <div @class(['single-vacation-rental-map', $class ?? null])>
        <div class="h7 title fw-6">{{ __('Location') }}</div>
        <div class="map-container">
            <div id="vacation-rental-map" 
                 data-latitude="{{ $vacationRental->latitude }}" 
                 data-longitude="{{ $vacationRental->longitude }}"
                 data-name="{{ addslashes($vacationRental->name) }}"
                 data-address="{{ addslashes($vacationRental->short_address ?: $vacationRental->location) }}"
                 data-price="{{ $vacationRental->price_format }}"
                 data-url="{{ $vacationRental->url }}"
                 data-image="{{ $vacationRental->image_thumb }}">
            </div>
        </div>
        
        @if ($vacationRental->short_address || $vacationRental->location)
            <div class="location-info">
                <div class="address">
                    <x-core::icon name="ti ti-map-pin" />
                    <span>{{ $vacationRental->short_address ?: $vacationRental->location }}</span>
                </div>
                @if ($vacationRental->latitude && $vacationRental->longitude)
                    <div class="coordinates">
                        <x-core::icon name="ti ti-gps" />
                        <span>{{ $vacationRental->latitude }}, {{ $vacationRental->longitude }}</span>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof L !== 'undefined') {
                const mapElement = document.getElementById('vacation-rental-map');
                const lat = parseFloat(mapElement.dataset.latitude);
                const lng = parseFloat(mapElement.dataset.longitude);
                const name = mapElement.dataset.name;
                const address = mapElement.dataset.address;
                const price = mapElement.dataset.price;
                const url = mapElement.dataset.url;
                const image = mapElement.dataset.image;
                
                // Initialize map
                const map = L.map('vacation-rental-map').setView([lat, lng], 15);
                
                // Add tile layer
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);
                
                // Create custom marker icon
                const customIcon = L.divIcon({
                    className: 'custom-vacation-rental-marker',
                    html: `
                        <div class="marker-content">
                            <div class="marker-price">${price}</div>
                            <div class="marker-arrow"></div>
                        </div>
                    `,
                    iconSize: [120, 40],
                    iconAnchor: [60, 40]
                });
                
                // Create popup content
                const popupContent = `
                    <div class="vacation-rental-popup">
                        <div class="popup-image">
                            <img src="${image}" alt="${name}" />
                        </div>
                        <div class="popup-content">
                            <h6 class="popup-title">${name}</h6>
                            <p class="popup-address">${address}</p>
                            <div class="popup-price">${price} <span>per night</span></div>
                            <a href="${url}" class="popup-link">{{ __('View Details') }}</a>
                        </div>
                    </div>
                `;
                
                // Add marker with popup
                L.marker([lat, lng], { icon: customIcon })
                    .addTo(map)
                    .bindPopup(popupContent, {
                        maxWidth: 300,
                        className: 'vacation-rental-popup-container'
                    });
            }
        });
    </script>
@endif

<style>
.single-vacation-rental-map {
    margin-bottom: 30px;
}

.map-container {
    margin-top: 16px;
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid #e9ecef;
}

#vacation-rental-map {
    height: 400px;
    width: 100%;
}

.location-info {
    margin-top: 16px;
    padding: 16px;
    background: #f8f9fa;
    border-radius: 8px;
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
}

.address,
.coordinates {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #6c757d;
    font-size: 14px;
}

.address x-core\\:icon,
.coordinates x-core\\:icon {
    color: #007bff;
    font-size: 16px;
}

/* Custom marker styles */
.custom-vacation-rental-marker {
    background: transparent;
    border: none;
}

.marker-content {
    position: relative;
    background: #007bff;
    color: white;
    padding: 8px 12px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 14px;
    box-shadow: 0 2px 8px rgba(0, 123, 255, 0.3);
    white-space: nowrap;
}

.marker-arrow {
    position: absolute;
    bottom: -5px;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 0;
    border-left: 6px solid transparent;
    border-right: 6px solid transparent;
    border-top: 6px solid #007bff;
}

/* Popup styles */
.vacation-rental-popup {
    display: flex;
    gap: 12px;
    min-width: 280px;
}

.popup-image {
    flex-shrink: 0;
    width: 80px;
    height: 80px;
    border-radius: 6px;
    overflow: hidden;
}

.popup-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.popup-content {
    flex: 1;
}

.popup-title {
    margin: 0 0 4px 0;
    font-size: 16px;
    font-weight: 600;
    color: #212529;
}

.popup-address {
    margin: 0 0 8px 0;
    font-size: 14px;
    color: #6c757d;
}

.popup-price {
    margin: 0 0 8px 0;
    font-size: 16px;
    font-weight: 600;
    color: #007bff;
}

.popup-price span {
    font-size: 12px;
    font-weight: normal;
    color: #6c757d;
}

.popup-link {
    display: inline-block;
    padding: 4px 12px;
    background: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
}

.popup-link:hover {
    background: #0056b3;
    color: white;
}

@media (max-width: 768px) {
    #vacation-rental-map {
        height: 300px;
    }
    
    .location-info {
        flex-direction: column;
        gap: 8px;
    }
    
    .vacation-rental-popup {
        flex-direction: column;
        min-width: 200px;
    }
    
    .popup-image {
        width: 100%;
        height: 120px;
    }
}
</style>
