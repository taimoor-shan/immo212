@if ($vacationRental->description)
    <div @class(['single-vacation-rental-description', $class ?? null])>
        <div class="h7 title fw-6">{{ __('About This Vacation Rental') }}</div>
        <div class="description-content">
            <div class="text-variant-1">
                {!! BaseHelper::clean($vacationRental->description) !!}
            </div>
        </div>
        
        @if ($vacationRental->house_rules)
            <div class="house-rules mt-4">
                <div class="h7 title fw-6 mb-3">{{ __('House Rules') }}</div>
                <div class="house-rules-content">
                    <div class="text-variant-1">
                        {!! nl2br(e($vacationRental->house_rules)) !!}
                    </div>
                </div>
            </div>
        @endif
        
        @if ($vacationRental->cancellation_policy)
            <div class="cancellation-policy mt-4">
                <div class="h7 title fw-6 mb-3">{{ __('Cancellation Policy') }}</div>
                <div class="policy-content">
                    <div class="text-variant-1">
                        <span class="policy-type">{{ ucfirst(str_replace('_', ' ', $vacationRental->cancellation_policy)) }}</span>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endif

<style>
.single-vacation-rental-description {
    margin-bottom: 30px;
    padding: 24px;
    background: #f8f9fa;
    border-radius: 8px;
}

.description-content {
    margin-top: 16px;
    line-height: 1.6;
}

.house-rules {
    border-top: 1px solid #e9ecef;
    padding-top: 20px;
}

.house-rules-content {
    background: #fff5f5;
    padding: 16px;
    border-radius: 6px;
    border-left: 4px solid #dc3545;
}

.cancellation-policy {
    border-top: 1px solid #e9ecef;
    padding-top: 20px;
}

.policy-content {
    background: #f0f9ff;
    padding: 16px;
    border-radius: 6px;
    border-left: 4px solid #007bff;
}

.policy-type {
    font-weight: 600;
    color: #007bff;
    text-transform: capitalize;
}

@media (max-width: 768px) {
    .single-vacation-rental-description {
        padding: 16px;
        margin-bottom: 20px;
    }
}
</style>
