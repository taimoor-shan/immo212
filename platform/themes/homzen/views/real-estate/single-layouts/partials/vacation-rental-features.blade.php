@if ($vacationRental->features->isNotEmpty())
    <div @class(['widget-box', $class ?? null])>
        <div class="h7 title fw-6">{{ __('Amenities & Features') }}</div>
        <div class="features-grid">
            @foreach($vacationRental->features as $feature)
                <div class="feature-item">
                    <div class="feature-icon">
                        @if ($feature->icon)
                            <x-core::icon :name="$feature->icon" />
                        @else
                            <x-core::icon name="ti ti-check" />
                        @endif
                    </div>
                    <div class="feature-content">
                        <div class="feature-name">{{ $feature->name }}</div>
                        @if ($feature->description)
                            <div class="feature-description">{{ $feature->description }}</div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

<style>
.single-vacation-rental-features {
    margin-bottom: 30px;
    padding: 24px;
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 16px;
    margin-top: 20px;
}

.feature-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 12px;
    background: #f8f9fa;
    border-radius: 6px;
    transition: background-color 0.2s ease;
}

.feature-item:hover {
    background: #e9ecef;
}

.feature-icon {
    flex-shrink: 0;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #007bff;
    font-size: 18px;
}

.feature-content {
    flex: 1;
    min-width: 0;
}

.feature-name {
    font-weight: 500;
    color: #212529;
    margin-bottom: 2px;
}

.feature-description {
    font-size: 14px;
    color: #6c757d;
    line-height: 1.4;
}

@media (max-width: 768px) {
    .single-vacation-rental-features {
        padding: 16px;
        margin-bottom: 20px;
    }

    .features-grid {
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .feature-item {
        padding: 10px;
    }
}

@media (max-width: 576px) {
    .features-grid {
        grid-template-columns: 1fr;
    }
}
</style>
