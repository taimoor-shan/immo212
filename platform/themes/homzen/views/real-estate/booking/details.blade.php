@php
    Theme::layout('default');
    Theme::set('pageTitle', __('Booking Details'));
    Theme::set('breadcrumbEnabled', 'no');
@endphp

<!-- Breadcrumb -->
<!-- <div class="flat-title-page">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="title-page">
                    <h2 class="text-center">{{ __('Booking Details') }}</h2>
                    <p class="text-center text-muted">{{ __('Booking #:number', ['number' => $booking->booking_number]) }}</p>
                </div>
            </div>
        </div>
    </div>
</div> -->

<div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="card-title mb-0">{{ __('Booking Details') }}</h3>
                                <p class="text-muted mb-0">{{ __('Booking #:number', ['number' => $booking->booking_number]) }}</p>
                            </div>
                            <div>
                                <span class="badge bg-{{ $booking->status === 'confirmed' ? 'success' : ($booking->status === 'pending' ? 'warning' : 'secondary') }} fs-6">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="row">
                            <!-- Property Information -->
                            <div class="col-md-6">
                                <div class="section">
                                    <h5>{{ __('Property Information') }}</h5>
                                    <div class="property-info">
                                        <div class="property-image mb-3">
                                            {{ RvMedia::image($booking->property->image, $booking->property->name, 'medium-rectangle') }}
                                        </div>
                                        <h6>{{ $booking->property->name }}</h6>
                                        @if($booking->property->short_address)
                                            <p class="text-muted">
                                                <i class="fas fa-map-marker-alt"></i> {{ $booking->property->short_address }}
                                            </p>
                                        @endif
                                        <a href="{{ $booking->property->url }}" class="btn btn-outline-primary btn-sm">
                                            {{ __('View Property') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Booking Information -->
                            <div class="col-md-6">
                                <div class="section">
                                    <h5>{{ __('Booking Information') }}</h5>
                                    <div class="booking-details">
                                        <div class="detail-row">
                                            <span class="label">{{ __('Check-in:') }}</span>
                                            <span class="value">{{ $booking->check_in_date->format('l, M j, Y') }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="label">{{ __('Check-out:') }}</span>
                                            <span class="value">{{ $booking->check_out_date->format('l, M j, Y') }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="label">{{ __('Duration:') }}</span>
                                            <span class="value">{{ $booking->nights_count }} {{ $booking->nights_count == 1 ? __('night') : __('nights') }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="label">{{ __('Guests:') }}</span>
                                            <span class="value">
                                                {{ $booking->guests_count }} {{ __('total') }}
                                                @if($booking->adults_count || $booking->children_count)
                                                    ({{ $booking->adults_count }} {{ __('adults') }}, {{ $booking->children_count }} {{ __('children') }})
                                                @endif
                                            </span>
                                        </div>
                                        @if($booking->property->check_in_time)
                                            <div class="detail-row">
                                                <span class="label">{{ __('Check-in time:') }}</span>
                                                <span class="value">{{ $booking->property->check_in_time }}</span>
                                            </div>
                                        @endif
                                        @if($booking->property->check_out_time)
                                            <div class="detail-row">
                                                <span class="label">{{ __('Check-out time:') }}</span>
                                                <span class="value">{{ $booking->property->check_out_time }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                  
                        
                        <div class="row">
                            <!-- Guest Information -->
                            <div class="col-md-6">
                                <div class="section">
                                    <h5>{{ __('Guest Information') }}</h5>
                                    <div class="guest-details">
                                        <div class="detail-row">
                                            <span class="label">{{ __('Name:') }}</span>
                                            <span class="value">{{ $booking->guest_name }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="label">{{ __('Email:') }}</span>
                                            <span class="value">{{ $booking->guest_email }}</span>
                                        </div>
                                        @if($booking->guest_phone)
                                            <div class="detail-row">
                                                <span class="label">{{ __('Phone:') }}</span>
                                                <span class="value">{{ $booking->guest_phone }}</span>
                                            </div>
                                        @endif
                                        <div class="detail-row">
                                            <span class="label">{{ __('Booking Date:') }}</span>
                                            <span class="value">{{ $booking->created_at->format('M j, Y \a\t g:i A') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Payment Information -->
                            <div class="col-md-6">
                                <div class="section">
                                    <h5>{{ __('Payment Information') }}</h5>
                                    <div class="payment-details">
                                        <div class="price-breakdown">
                                            <div class="price-row">
                                                <span>{{ $booking->nights_count }} {{ __('nights') }} × ${{ number_format($booking->base_price_per_night, 2) }}</span>
                                                <span>${{ number_format($booking->total_nights_cost, 2) }}</span>
                                            </div>
                                            @if($booking->cleaning_fee > 0)
                                                <div class="price-row">
                                                    <span>{{ __('Cleaning fee') }}</span>
                                                    <span>${{ number_format($booking->cleaning_fee, 2) }}</span>
                                                </div>
                                            @endif
                                            @if($booking->service_fee > 0)
                                                <div class="price-row">
                                                    <span>{{ __('Service fee') }}</span>
                                                    <span>${{ number_format($booking->service_fee, 2) }}</span>
                                                </div>
                                            @endif
                                            @if($booking->taxes > 0)
                                                <div class="price-row">
                                                    <span>{{ __('Taxes') }}</span>
                                                    <span>${{ number_format($booking->taxes, 2) }}</span>
                                                </div>
                                            @endif
                                         
                                            <div class="price-row total">
                                                <strong>{{ __('Total Amount') }}</strong>
                                                <strong>${{ number_format($booking->total_amount, 2) }}</strong>
                                            </div>
                                            @if($booking->security_deposit > 0)
                                                <div class="price-row deposit">
                                                    <span>{{ __('Security Deposit (Refundable)') }}</span>
                                                    <span>${{ number_format($booking->security_deposit, 2) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="payment-status mt-3">
                                            <div class="detail-row">
                                                <span class="label">{{ __('Payment Status:') }}</span>
                                                <span class="value">
                                                    <span class="badge bg-{{ $booking->payment_status === 'paid' ? 'success' : ($booking->payment_status === 'pending' ? 'warning' : 'secondary') }}">
                                                        {{ ucfirst($booking->payment_status) }}
                                                    </span>
                                                </span>
                                            </div>
                                            @if($booking->payment_reference)
                                                <div class="detail-row">
                                                    <span class="label">{{ __('Payment Reference:') }}</span>
                                                    <span class="value">{{ $booking->payment_reference }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Special Requests -->
                        @if($booking->special_requests)
                            <hr>
                            <div class="section">
                                <h5>{{ __('Special Requests') }}</h5>
                                <div class="special-requests">
                                    <p>{{ $booking->special_requests }}</p>
                                </div>
                            </div>
                        @endif
                        
                        <!-- House Rules -->
                        @if($booking->property->house_rules)
                            <hr>
                            <div class="section">
                                <h5>{{ __('House Rules') }}</h5>
                                <div class="house-rules">
                                    {!! nl2br(e($booking->property->house_rules)) !!}
                                </div>
                            </div>
                        @endif
                        
                        <!-- Action Buttons -->
                        <hr>
                        <div class="action-buttons text-center">
                            <a href="{{ $booking->property->url }}" class="btn btn-outline-primary me-3">
                                {{ __('View Property') }}
                            </a>
                            <a href="{{ route('public.properties') }}" class="btn btn-outline-secondary">
                                {{ __('Browse More Properties') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .section {
            margin-bottom: 2rem;
        }
        
        .section h5 {
            margin-bottom: 1rem;
            color: #495057;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 0.5rem;
        }
        
        .detail-row, .price-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f8f9fa;
        }
        
        .detail-row:last-child, .price-row:last-child {
            border-bottom: none;
        }
        
        .detail-row .label, .price-row span:first-child {
            color: #6c757d;
            font-weight: 500;
        }
        
        .detail-row .value, .price-row span:last-child {
            color: #495057;
        }
        
        .price-row.total {
            font-size: 1.1rem;
            border-top: 2px solid #dee2e6;
            margin-top: 0.5rem;
            padding-top: 1rem;
        }
        
        .price-row.deposit {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .property-image img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 6px;
        }
        
        .special-requests {
            background-color: #f8f9fa;
            border-radius: 6px;
            padding: 1rem;
            color: #495057;
        }
        
        .house-rules {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 1rem;
            color: #856404;
        }
        
        .action-buttons .btn {
            margin-bottom: 0.5rem;
        }
        
        @media (max-width: 768px) {
            .detail-row, .price-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.25rem;
            }
            
            .action-buttons .btn {
                display: block;
                width: 100%;
                margin-bottom: 0.75rem;
            }
        }
    </style>
