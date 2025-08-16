<div @class(['single-property-vacation-rental-info', $class ?? null])>
    <div class="h7 title fw-6">{{ __('Vacation Rental Information') }}</div>
    
    <div class="vacation-rental-details mt-3">
        <div class="row g-3">
            <!-- Check-in/Check-out Times -->
            @if($vacationRental->check_in_time || $vacationRental->check_out_time)
                <div class="col-md-6">
                    <div class="info-card">
                        <div class="info-header">
                            <x-core::icon name="ti ti-clock" class="text-primary" />
                            <span class="fw-6">{{ __('Check-in & Check-out') }}</span>
                        </div>
                        <div class="info-content">
                            @if($vacationRental->check_in_time)
                                <div class="info-item">
                                    <span class="label">{{ __('Check-in:') }}</span>
                                    <span class="value">{{ $vacationRental->check_in_time }}</span>
                                </div>
                            @endif
                            @if($vacationRental->check_out_time)
                                <div class="info-item">
                                    <span class="label">{{ __('Check-out:') }}</span>
                                    <span class="value">{{ $vacationRental->check_out_time }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Stay Requirements -->
            @if($vacationRental->minimum_stay || $vacationRental->maximum_stay || $vacationRental->maximum_guests)
                <div class="col-md-6">
                    <div class="info-card">
                        <div class="info-header">
                            <x-core::icon name="ti ti-calendar-stats" class="text-success" />
                            <span class="fw-6">{{ __('Stay Requirements') }}</span>
                        </div>
                        <div class="info-content">
                            @if($vacationRental->minimum_stay)
                                <div class="info-item">
                                    <span class="label">{{ __('Minimum stay:') }}</span>
                                    <span class="value">{{ $vacationRental->minimum_stay }} {{ $vacationRental->minimum_stay == 1 ? __('night') : __('nights') }}</span>
                                </div>
                            @endif
                            @if($vacationRental->maximum_stay)
                                <div class="info-item">
                                    <span class="label">{{ __('Maximum stay:') }}</span>
                                    <span class="value">
                                        @if($vacationRental->maximum_stay == 0)
                                            {{ __('No limit') }}
                                        @else
                                            {{ $vacationRental->maximum_stay }} {{ $vacationRental->maximum_stay == 1 ? __('night') : __('nights') }}
                                        @endif
                                    </span>
                                </div>
                            @endif
                            @if($vacationRental->maximum_guests)
                                <div class="info-item">
                                    <span class="label">{{ __('Maximum guests:') }}</span>
                                    <span class="value">{{ $vacationRental->maximum_guests }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Pricing Information -->
            @if($vacationRental->cleaning_fee || $vacationRental->security_deposit)
                <div class="col-md-6">
                    <div class="info-card">
                        <div class="info-header">
                            <x-core::icon name="ti ti-currency-dollar" class="text-warning" />
                            <span class="fw-6">{{ __('Additional Fees') }}</span>
                        </div>
                        <div class="info-content">
                            @if($vacationRental->cleaning_fee)
                                <div class="info-item">
                                    <span class="label">{{ __('Cleaning fee:') }}</span>
                                    <span class="value">{{ number_format($vacationRental->cleaning_fee, 2) }}</span>
                                </div>
                            @endif
                            @if($vacationRental->security_deposit)
                                <div class="info-item">
                                    <span class="label">{{ __('Security deposit:') }}</span>
                                    <span class="value">{{ number_format($vacationRental->security_deposit, 2) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Cancellation Policy -->
            @if($vacationRental->cancellation_policy)
                <div class="col-md-6">
                    <div class="info-card">
                        <div class="info-header">
                            <x-core::icon name="ti ti-shield-check" class="text-info" />
                            <span class="fw-6">{{ __('Cancellation Policy') }}</span>
                        </div>
                        <div class="info-content">
                            <div class="info-item">
                                <span class="value">{{ ucfirst(str_replace('_', ' ', $vacationRental->cancellation_policy)) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- House Rules -->
        @if($vacationRental->house_rules)
            <div class="house-rules mt-4">
                <div class="info-header mb-3">
                    <x-core::icon name="ti ti-list-check" class="text-danger" />
                    <span class="fw-6">{{ __('House Rules') }}</span>
                </div>
                <div class="house-rules-content">
                    <div class="text-variant-1">
                        {!! nl2br(e($vacationRental->house_rules)) !!}
                    </div>
                </div>
            </div>
        @endif

        <!-- Booking Summary -->
        <div class="booking-summary mt-4">
            <div class="info-header mb-3">
                <x-core::icon name="ti ti-info-circle" class="text-primary" />
                <span class="fw-6">{{ __('Booking Information') }}</span>
            </div>
            <div class="booking-info-grid">
                <div class="row g-2 text-sm">
                    <div class="col-md-4">
                        <div class="booking-info-item">
                            <span>{{ __('Base price: :price/night', ['price' => $vacationRental->price_format]) }}</span>
                        </div>
                    </div>
                    @if($vacationRental->minimum_stay)
                        <div class="col-md-4">
                            <div class="booking-info-item">
                                <x-core::icon name="ti ti-calendar" class="me-1" />
                                <span>{{ __('Min :nights nights', ['nights' => $vacationRental->minimum_stay]) }}</span>
                            </div>
                        </div>
                    @endif
                    @if($vacationRental->maximum_guests)
                        <div class="col-md-4">
                            <div class="booking-info-item">
                                <x-core::icon name="ti ti-users" class="me-1" />
                                <span>{{ __('Max :guests guests', ['guests' => $vacationRental->maximum_guests]) }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .vacation-rental-details .info-card {
        border: 1px solid #e9ecef;
        border-radius: 6px;
        padding: 1rem;
        background-color: #f8f9fa;
        height: 100%;
    }
    
    .vacation-rental-details .info-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #dee2e6;
    }
    
    .vacation-rental-details .info-content .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.25rem 0;
    }
    
    .vacation-rental-details .info-content .info-item .label {
        color: #6c757d;
        font-size: 0.9rem;
    }
    
    .vacation-rental-details .info-content .info-item .value {
        font-weight: 500;
        color: #212529;
    }
    
    .vacation-rental-details .house-rules {
        border: 1px solid #e9ecef;
        border-radius: 6px;
        padding: 1rem;
        background-color: #fff5f5;
    }
    
    .vacation-rental-details .house-rules-content {
        color: #495057;
        line-height: 1.6;
    }
    
    .vacation-rental-details .booking-summary {
        border: 1px solid #e9ecef;
        border-radius: 6px;
        padding: 1rem;
        background-color: #f0f9ff;
    }
    
    .vacation-rental-details .booking-info-item {
        display: flex;
        align-items: center;
        color: #495057;
        margin-bottom: 0.5rem;
    }
    
    .vacation-rental-details .booking-info-item:last-child {
        margin-bottom: 0;
    }
    
    @media (max-width: 768px) {
        .vacation-rental-details .info-card {
            margin-bottom: 1rem;
        }
        
        .vacation-rental-details .info-content .info-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.25rem;
        }
    }
</style>
