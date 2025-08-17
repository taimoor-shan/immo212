<div class="header-vacation-rental-detail pt-4">
    <div class="content-top d-flex justify-content-between align-items-center">
        <div class="box-name">
            <span class="flag-tag vacation-rental mb-2">{{ __('Vacation Rental') }}</span>
            <h2 class="section-title">
                {!! BaseHelper::clean($vacationRental->name) !!}
            </h2>
            <ul class="d-flex align-items-center gap-2 text-dBlue">
                @if ($vacationRental->minimum_stay)
                    <li class="meta-item">
                        {{ $vacationRental->minimum_stay }} {{ $vacationRental->minimum_stay == 1 ? __('night min') : __('nights min') }}
                    </li>
                @endif
                @if ($vacationRental->maximum_guests)
                    <li class="meta-item">
                        |
                    </li>
                    <li class="meta-item">
                        {{ $vacationRental->maximum_guests }} {{ __('guests max') }}
                    </li>
                @endif
                @if ($vacationRental->price)
                    <li class="meta-item">
                        |
                    </li>
                    <li class="meta-item">
                        {{ $vacationRental->price_format }} {{ __('per night') }}
                    </li>
                @endif
            </ul>
            @if ($vacationRental->short_address)
                <p class="meta-item">
                    <x-core::icon name="ti ti-map-pin" />
                    {{ $vacationRental->short_address }}
                </p>
            @endif
        </div>
        <div class="box-price">
            <div class="d-flex align-items-center">
                <h3 class="price">{{ $vacationRental->price_format }}</h3>
                <span class="text-variant-1">{{ __('per night') }}</span>
            </div>
            @if ($vacationRental->cleaning_fee || $vacationRental->security_deposit)
                <div class="additional-fees mt-2">
                    @if ($vacationRental->cleaning_fee)
                        <div class="fee-item">
                            <span class="fee-label">{{ __('Cleaning fee:') }}</span>
                            <span class="fee-amount">{{ format_price($vacationRental->cleaning_fee) }}</span>
                        </div>
                    @endif
                    @if ($vacationRental->security_deposit)
                        <div class="fee-item">
                            <span class="fee-label">{{ __('Security deposit:') }}</span>
                            <span class="fee-amount">{{ format_price($vacationRental->security_deposit) }}</span>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
    <div class="content-bottom">
        <div class="info-box">
            <div class="inner">
                <div class="stats">
                    @if ($vacationRental->minimum_stay)
                        <div class="stat-item">
                            <x-core::icon name="ti ti-calendar" />
                            <span>{{ $vacationRental->minimum_stay }} {{ $vacationRental->minimum_stay == 1 ? __('night') : __('nights') }} {{ __('minimum') }}</span>
                        </div>
                    @endif
                    @if ($vacationRental->maximum_guests)
                        <div class="stat-item">
                            <x-core::icon name="ti ti-users" />
                            <span>{{ $vacationRental->maximum_guests }} {{ __('guests maximum') }}</span>
                        </div>
                    @endif
                    @if ($vacationRental->check_in_time)
                        <div class="stat-item">
                            <x-core::icon name="ti ti-clock" />
                            <span>{{ __('Check-in:') }} {{ $vacationRental->check_in_time }}</span>
                        </div>
                    @endif
                    @if ($vacationRental->check_out_time)
                        <div class="stat-item">
                            <x-core::icon name="ti ti-clock" />
                            <span>{{ __('Check-out:') }} {{ $vacationRental->check_out_time }}</span>
                        </div>
                    @endif
                </div>
                <div class="actions">
                    @if (RealEstateHelper::isEnabledWishlist())
                        <button type="button" class="box-icon w-52 h-52 round"
                                data-type="vacation-rental"
                                data-bb-toggle="add-to-wishlist"
                                data-id="{{ $vacationRental->getKey() }}"
                                title="{{ __('Add to wishlist') }}">
                            <x-core::icon name="ti ti-heart" />
                        </button>
                    @endif
                    <button type="button" class="box-icon w-52 h-52 round" onclick="copyToClipboard('{{ $vacationRental->url }}')" title="{{ __('Copy link') }}">
                        <x-core::icon name="ti ti-share" />
                    </button>
                    <button type="button" class="box-icon w-52 h-52 round" onclick="window.print()" title="{{ __('Print') }}">
                        <x-core::icon name="ti ti-printer" />
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.flag-tag.vacation-rental {
    background-color: #28a745;
    color: white;
    padding: 4px 12px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.additional-fees {
    font-size: 14px;
}

.fee-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 2px;
}

.fee-label {
    color: #6c757d;
}

.fee-amount {
    font-weight: 500;
    color: #212529;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
    font-size: 14px;
    color: #6c757d;
}

.stat-item:last-child {
    margin-bottom: 0;
}

.stat-item x-core\\:icon {
    color: #007bff;
}
</style>
