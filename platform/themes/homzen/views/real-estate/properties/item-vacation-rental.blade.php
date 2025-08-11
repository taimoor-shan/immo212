@php
    $class ??= null;
    $itemsPerRow ??= 3;
@endphp

<div @class(['property-item homeya-box vacation-rental-card', $class]) @if ($property->latitude && $property->longitude) data-lat="{{ $property->latitude }}" data-lng="{{ $property->longitude }}" @endif>
    <div class="archive-top">
        <a href="{{ $property->url }}" class="images-group">
            <div class="images-style">
                {{ RvMedia::image($property->image, $property->name, 'medium-rectangle') }}
            </div>
            <div class="top">
                <div class="d-flex gap-8">
                    <span class="flag-tag vacation-rental">{{ __('Vacation Rental') }}</span>
                </div>
                @if (RealEstateHelper::isEnabledWishlist())
                    <div class="d-flex gap-4">
                        <button type="button" class="box-icon w-32"
                                data-type="property"
                                data-bb-toggle="add-to-wishlist"
                                data-id="{{ $property->getKey() }}"
                                data-add-message="{{ __('Added ":name" to wishlist successfully!', ['name' => $property->name]) }}"
                                data-remove-message="{{ __('Removed ":name" from wishlist successfully!', ['name' => $property->name]) }}"
                        >
                            <x-core::icon name="ti ti-heart" />
                        </button>
                    </div>
                @endif
            </div>
        </a>
        <div class="content">
            @if($property->category)
                <div class="property-type-badge">
                    <span class="">{{ $property->category->name }}</span>
                </div>
            @endif
            
            <!-- Property Title -->
            <!-- <{{ $class === 'lg' ? 'h5' : 'div' }} @class(['text-capitalize', 'h7 fw-5' => $class !== 'lg'])>
                <a href="{{ $property->url }}" class="link line-clamp-1" title="{{ $property->name }}">{!! BaseHelper::clean($property->name) !!}</a>
            </{{ $class === 'lg' ? 'h5' : 'div' }}> -->
            
            <!-- Price per night -->
            <div class="vacation-rental-price">
                <span class="price-amount">{{ format_price($property->price, $property->currency) }}</span>
                <span class="price-period">/ {{ __('night') }}</span>
            </div>
            
            <!-- Location -->
            @if($property->short_address)
                <div class="desc">
                    <i class="icon icon-mapPin"></i>
                    <p class="line-clamp-1">{{ $property->short_address }}</p>
                </div>
            @endif
            
            <!-- Vacation Rental Specific Info -->
            <!-- <div class="vacation-rental-info">
                <div class="row g-2">
                    @if($property->maximum_guests)
                        <div class="col-6">
                            <div class="info-item">
                                <i class="icon icon-users"></i>
                                <span>{{ $property->maximum_guests }} {{ __('guests') }}</span>
                            </div>
                        </div>
                    @endif
                    @if($property->minimum_stay)
                        <div class="col-6">
                            <div class="info-item">
                                <i class="icon icon-calendar"></i>
                                <span>{{ $property->minimum_stay }}{{ __('n min') }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div> -->
            
            <!-- Standard Property Meta -->
            <ul class="meta-list">
                @if($property->number_bedroom)
                    <li class="item">
                        <i class="icon icon-bed"></i>
                        <span>{{ number_format($property->number_bedroom) }}</span>
                    </li>
                @endif
                @if($property->number_bathroom)
                    <li class="item">
                        <i class="icon icon-bathtub"></i>
                        <span>{{ number_format($property->number_bathroom) }}</span>
                    </li>
                @endif
                @if($property->square)
                    <li class="item">
                        <i class="icon icon-ruler"></i>
                        <span>{{ $property->square_text }}</span>
                    </li>
                @endif
            </ul>
            
            <!-- Additional Vacation Rental Features -->
            @if($property->cleaning_fee || $property->security_deposit)
                <div class="vacation-rental-fees">
                    @if($property->cleaning_fee)
                        <div class="fee-item">
                            <span class="fee-label">{{ __('Cleaning:') }}</span>
                            <span class="fee-amount">${{ number_format($property->cleaning_fee, 0) }}</span>
                        </div>
                    @endif
                    @if($property->security_deposit)
                        <div class="fee-item">
                            <span class="fee-label">{{ __('Deposit:') }}</span>
                            <span class="fee-amount">${{ number_format($property->security_deposit, 0) }}</span>
                        </div>
                    @endif
                </div>
            @endif
            
            @if($class === 'lg' && $property->description)
                <p class="note">{!! Str::limit(BaseHelper::clean($property->description)) !!}</p>
            @endif
            
            <!-- Quick Booking CTA -->
            <div class="vacation-rental-cta">
                <a href="{{ $property->url }}#availability" class="btn-vacation-rental">
                    {{ __('Check Availability') }}
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .vacation-rental-card {
        border: 1px solid #e9ecef;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .vacation-rental-card:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }
    
    .vacation-rental-card .flag-tag.vacation-rental {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-size: 0.75rem;
        font-weight: 500;
        padding: 4px 8px;
        border-radius: 4px;
    }
    
    .vacation-rental-card .vacation-rental-price {
        display: flex;
        align-items: baseline;
        gap: 4px;
        margin: 0 0 8px;
    }
    
    .vacation-rental-card .price-amount {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--primary-color);
    }
    
    .vacation-rental-card .price-period {
        font-size: 0.875rem;
        color: #6b7280;
        font-weight: 500;
    }
    
    .vacation-rental-card .vacation-rental-info {
        margin: 12px 0;
        padding: 8px;
        background-color: #f8fafc;
        border-radius: 6px;
    }
    
    .vacation-rental-card .info-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.875rem;
        color: #4b5563;
    }
    
    .vacation-rental-card .info-item i {
        font-size: 14px;
        color: #6366f1;
    }
    
    .vacation-rental-card .vacation-rental-fees {
        display: flex;
        gap: 12px;
        margin: 8px 0;
        font-size: 0.8rem;
    }
    
    .vacation-rental-card .fee-item {
        display: flex;
        align-items: center;
        gap: 4px;
    }
    
    .vacation-rental-card .fee-label {
        color: #6b7280;
    }
    
    .vacation-rental-card .fee-amount {
        color: #374151;
        font-weight: 500;
    }
    
    .vacation-rental-card .vacation-rental-cta {
        margin-top: 12px;
    }
    
    .vacation-rental-card .btn-vacation-rental {
        display: inline-block;
        width: 100%;
        padding: 8px 16px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        text-decoration: none;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 500;
        text-align: center;
        transition: all 0.3s ease;
    }
    
    .vacation-rental-card .btn-vacation-rental:hover {
        background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
        color: white;
        text-decoration: none;
        transform: translateY(-1px);
    }
    
    @media (max-width: 768px) {
        .vacation-rental-card .vacation-rental-info .row {
            gap: 8px;
        }
        
        .vacation-rental-card .vacation-rental-fees {
            flex-direction: column;
            gap: 4px;
        }
    }
</style>
