@if ($vacationRental->facilities->isNotEmpty())
    <div @class(['widget-box', $class ?? null])>
        <div class="h7 title fw-6">{{ __('Nearby Facilities') }}</div>
        <div class="facilities-grid">
            @foreach($vacationRental->facilities as $facility)
                <div class="facility-item">
                    <div class="facility-icon">
                        @if ($facility->icon)
                            <x-core::icon :name="$facility->icon" />
                        @else
                            <x-core::icon name="ti ti-map-pin" />
                        @endif
                    </div>
                    <div class="facility-content">
                        <div class="facility-name">{{ $facility->name }}</div>
                        @if ($facility->pivot && $facility->pivot->distance)
                            <div class="facility-distance">
                                <x-core::icon name="ti ti-route" />
                                {{ $facility->pivot->distance }}
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

<style>
.single-vacation-rental-facilities {
    margin-bottom: 30px;
    padding: 24px;
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
}

.facilities-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 16px;
    margin-top: 20px;
}

.facility-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e9ecef;
    transition: all 0.2s ease;
}

.facility-item:hover {
    background: #e9ecef;
    border-color: #007bff;
    transform: translateY(-1px);
}

.facility-icon {
    flex-shrink: 0;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #007bff;
    color: white;
    border-radius: 50%;
    font-size: 16px;
}

.facility-content {
    flex: 1;
    min-width: 0;
}

.facility-name {
    font-weight: 500;
    color: #212529;
    margin-bottom: 4px;
}

.facility-distance {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 14px;
    color: #6c757d;
}

.facility-distance x-core\\:icon {
    font-size: 12px;
}

@media (max-width: 768px) {
    .single-vacation-rental-facilities {
        padding: 16px;
        margin-bottom: 20px;
    }

    .facilities-grid {
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .facility-item {
        padding: 12px;
    }

    .facility-icon {
        width: 28px;
        height: 28px;
        font-size: 14px;
    }
}
</style>
