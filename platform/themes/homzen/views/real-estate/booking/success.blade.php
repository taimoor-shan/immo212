@php
    Theme::layout('default');
    Theme::set('pageTitle', __('Booking Confirmed'));
    Theme::set('breadcrumbEnabled', 'no');
@endphp


<div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body text-center">
                        <!-- Success Icon -->
                        <div class="success-icon mb-4">
                            <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                        </div>
                        
                        <!-- Success Message -->
                        <h2 class="text-success mb-3">{{ __('Booking Confirmed!') }}</h2>
                        <p class="lead mb-4">{{ __('Thank you for your booking. Your reservation has been confirmed.') }}</p>
                        
                        <!-- Booking Details -->
                        <div class="booking-confirmation">
                            <div class="row">
                                <div class="col-md-6 mx-auto">
                                    <div class="confirmation-details">
                                        <h5 class="mb-3">{{ __('Booking Details') }}</h5>
                                        
                                        <div class="detail-item">
                                            <strong>{{ __('Booking Number:') }}</strong>
                                            <span class="booking-number">{{ $booking->booking_number }}</span>
                                        </div>
                                        
                                        <div class="detail-item">
                                            <strong>{{ __('Property:') }}</strong>
                                            <span>{{ $booking->property->name }}</span>
                                        </div>
                                        
                                        <div class="detail-item">
                                            <strong>{{ __('Guest:') }}</strong>
                                            <span>{{ $booking->guest_name }}</span>
                                        </div>
                                        
                                        <div class="detail-item">
                                            <strong>{{ __('Check-in:') }}</strong>
                                            <span>{{ $booking->check_in_date->format('M j, Y') }}</span>
                                        </div>
                                        
                                        <div class="detail-item">
                                            <strong>{{ __('Check-out:') }}</strong>
                                            <span>{{ $booking->check_out_date->format('M j, Y') }}</span>
                                        </div>
                                        
                                        <div class="detail-item">
                                            <strong>{{ __('Nights:') }}</strong>
                                            <span>{{ $booking->nights_count }}</span>
                                        </div>
                                        
                                        <div class="detail-item">
                                            <strong>{{ __('Guests:') }}</strong>
                                            <span>{{ $booking->guests_count }}</span>
                                        </div>
                                        
                                        <div class="detail-item total">
                                            <strong>{{ __('Total Amount:') }}</strong>
                                            <strong class="text-primary">${{ number_format($booking->total_amount, 2) }}</strong>
                                        </div>
                                        
                                        <div class="detail-item">
                                            <strong>{{ __('Status:') }}</strong>
                                            <span class="badge bg-success text-success-fg">{{ ucfirst($booking->status) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Next Steps -->
                        <div class="next-steps mt-4">
                            <h5>{{ __('What\'s Next?') }}</h5>
                            <div class="row text-start">
                                <div class="col-md-4">
                                    <div class="step-item">
                                        <div class="step-icon">
                                            <i class="fas fa-envelope text-primary"></i>
                                        </div>
                                        <div class="step-content">
                                            <h6>{{ __('Confirmation Email') }}</h6>
                                            <p class="small text-muted">{{ __('You\'ll receive a confirmation email with all booking details.') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="step-item">
                                        <div class="step-icon">
                                            <i class="fas fa-key text-primary"></i>
                                        </div>
                                        <div class="step-content">
                                            <h6>{{ __('Check-in Instructions') }}</h6>
                                            <p class="small text-muted">{{ __('You\'ll receive check-in instructions 24 hours before arrival.') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="step-item">
                                        <div class="step-icon">
                                            <i class="fas fa-phone text-primary"></i>
                                        </div>
                                        <div class="step-content">
                                            <h6>{{ __('Host Contact') }}</h6>
                                            <p class="small text-muted">{{ __('The property owner will contact you if needed.') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Important Information -->
                        @if($booking->property->check_in_time || $booking->property->check_out_time)
                            <div class="important-info mt-4">
                                <h5>{{ __('Important Information') }}</h5>
                                <div class="info-box">
                                    @if($booking->property->check_in_time)
                                        <div class="info-item">
                                            <strong>{{ __('Check-in Time:') }}</strong> {{ $booking->property->check_in_time }}
                                        </div>
                                    @endif
                                    @if($booking->property->check_out_time)
                                        <div class="info-item">
                                            <strong>{{ __('Check-out Time:') }}</strong> {{ $booking->property->check_out_time }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                        
                        <!-- Action Buttons -->
                        <div class="action-buttons mt-4">
                            <a href="{{ route('public.vacation-rental.booking.details', $booking->booking_number) }}" class="btn btn-primary me-3">
                                {{ __('View Booking Details') }}
                            </a>
                            <a href="{{ $booking->property->url }}" class="btn btn-outline-primary me-3">
                                {{ __('View Property') }}
                            </a>
                            <a href="{{ route('public.properties') }}" class="btn btn-outline-secondary">
                                {{ __('Browse More Properties') }}
                            </a>
                        </div>
                        
                        <!-- Contact Information -->
                        <div class="contact-info mt-4">
                            <p class="text-muted">
                                {{ __('Questions about your booking?') }}
                                <a href="mailto:support@example.com">{{ __('Contact us') }}</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .confirmation-details {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            text-align: left;
        }
        
        .detail-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .detail-item:last-child {
            border-bottom: none;
        }
        
        .detail-item.total {
            border-top: 2px solid #dee2e6;
            margin-top: 0.5rem;
            padding-top: 1rem;
            font-size: 1.1rem;
        }
        
        .booking-number {
            font-family: 'Courier New', monospace;
            background-color: #e9ecef;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-weight: bold;
        }
        
        .step-item {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .step-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        
        .step-content h6 {
            margin-bottom: 0.5rem;
        }
        
        .info-box {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 1rem;
            color: #856404;
        }
        
        .info-box .info-item {
            margin-bottom: 0.5rem;
        }
        
        .info-box .info-item:last-child {
            margin-bottom: 0;
        }
        
        .action-buttons .btn {
            margin-bottom: 0.5rem;
        }
        
        @media (max-width: 768px) {
            .action-buttons .btn {
                display: block;
                width: 100%;
                margin-bottom: 0.75rem;
            }
            
            .step-item {
                margin-bottom: 1.5rem;
            }
        }
    </style>
