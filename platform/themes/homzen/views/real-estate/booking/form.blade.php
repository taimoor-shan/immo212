@php
    Theme::layout('default');
    Theme::set('pageTitle', __('Complete Your Booking'));
@endphp

<!-- Breadcrumb -->
<div class="flat-title-page">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="title-page">
                        <h2 class="text-center">{{ __('Complete Your Booking') }}</h2>
                        <p class="text-center text-muted">{{ $property->name }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Display validation errors -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <h4>{{ __('Please fix the following errors:') }}</h4>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Display session messages -->
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="booking-form-wrapper">

                    <!-- Booking Summary -->
                    <div class="booking-summary-section">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="booking-details-card">
                                    <div class="card-header">
                                        <h4 class="h5 fw-6">{{ __('Booking Details') }}</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="booking-info-list">
                                            <div class="info-item">
                                                <div class="info-label">{{ __('Check-in') }}</div>
                                                <div class="info-value">{{ $checkInDate->format('M j, Y') }}</div>
                                            </div>
                                            <div class="info-item">
                                                <div class="info-label">{{ __('Check-out') }}</div>
                                                <div class="info-value">{{ $checkOutDate->format('M j, Y') }}</div>
                                            </div>
                                            <div class="info-item">
                                                <div class="info-label">{{ __('Nights') }}</div>
                                                <div class="info-value">{{ $pricing['nights'] }}</div>
                                            </div>
                                            <div class="info-item">
                                                <div class="info-label">{{ __('Guests') }}</div>
                                                <div class="info-value">{{ $guests }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="price-breakdown-card">
                                    <div class="card-header">
                                        <h4 class="h5 fw-6">{{ __('Price Breakdown') }}</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="price-breakdown-list">
                                            <div class="price-item">
                                                <span class="price-label">{{ $pricing['nights'] }} {{ __('nights') }} × ${{ number_format($pricing['base_price_per_night'], 2) }}</span>
                                                <span class="price-value">${{ number_format($pricing['total_nights_cost'], 2) }}</span>
                                            </div>
                                            @if($pricing['cleaning_fee'] > 0)
                                                <div class="price-item">
                                                    <span class="price-label">{{ __('Cleaning fee') }}</span>
                                                    <span class="price-value">${{ number_format($pricing['cleaning_fee'], 2) }}</span>
                                                </div>
                                            @endif
                                            @if($pricing['service_fee'] > 0)
                                                <div class="price-item">
                                                    <span class="price-label">{{ __('Service fee') }}</span>
                                                    <span class="price-value">${{ number_format($pricing['service_fee'], 2) }}</span>
                                                </div>
                                            @endif
                                            @if($pricing['taxes'] > 0)
                                                <div class="price-item">
                                                    <span class="price-label">{{ __('Taxes') }}</span>
                                                    <span class="price-value">${{ number_format($pricing['taxes'], 2) }}</span>
                                                </div>
                                            @endif
                                            <div class="price-divider"></div>
                                            <div class="price-item total-price">
                                                <span class="price-label fw-6">{{ __('Total') }}</span>
                                                <span class="price-value fw-6">${{ number_format($pricing['total_amount'], 2) }}</span>
                                            </div>
                                            @if($pricing['security_deposit'] > 0)
                                                <div class="price-item deposit-info">
                                                    <span class="price-label">{{ __('Security deposit (refundable)') }}</span>
                                                    <span class="price-value">${{ number_format($pricing['security_deposit'], 2) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Form -->
                    <div class="booking-form-section">
                        <form method="POST" action="{{ route('public.vacation-rental.booking.process') }}" id="booking-form" class="booking-form">
                            @csrf
                            <input type="hidden" name="property_id" value="{{ $property->id }}">
                            <input type="hidden" name="check_in_date" value="{{ $checkInDate->format('Y-m-d') }}">
                            <input type="hidden" name="check_out_date" value="{{ $checkOutDate->format('Y-m-d') }}">
                            <input type="hidden" name="guests_count" value="{{ $guests }}">

                            <!-- Guest Information -->
                            <div class="form-section">
                                <div class="section-header">
                                    <h4 class="h5 fw-6">{{ __('Guest Information') }}</h4>
                                </div>
                                <div class="section-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="ip-group">
                                                <label class="form-label">{{ __('Full Name') }} <span class="text-danger">*</span></label>
                                                <input type="text" name="guest_name" class="form-control" required value="{{ old('guest_name') }}" placeholder="{{ __('Enter your full name') }}">
                                                @error('guest_name')
                                                    <div class="error-message">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="ip-group">
                                                <label class="form-label">{{ __('Email Address') }} <span class="text-danger">*</span></label>
                                                <input type="email" name="guest_email" class="form-control" required value="{{ old('guest_email') }}" placeholder="{{ __('Enter your email address') }}">
                                                @error('guest_email')
                                                    <div class="error-message">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="ip-group">
                                                <label class="form-label">{{ __('Phone Number') }}</label>
                                                <input type="tel" name="guest_phone" class="form-control" value="{{ old('guest_phone') }}" placeholder="{{ __('Enter your phone number') }}">
                                                @error('guest_phone')
                                                    <div class="error-message">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="ip-group">
                                                <label class="form-label">{{ __('Adults') }}</label>
                                                <input type="number" name="adults_count" class="form-control" min="0" max="20" value="{{ old('adults_count', $guests) }}">
                                                @error('adults_count')
                                                    <div class="error-message">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="ip-group">
                                                <label class="form-label">{{ __('Children') }}</label>
                                                <input type="number" name="children_count" class="form-control" min="0" max="20" value="{{ old('children_count', 0) }}">
                                                @error('children_count')
                                                    <div class="error-message">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Special Requests -->
                            <div class="form-section">
                                <div class="section-header">
                                    <h4 class="h5 fw-6">{{ __('Special Requests') }}</h4>
                                </div>
                                <div class="section-body">
                                    <div class="ip-group">
                                        <textarea name="special_requests" class="form-control" rows="4" placeholder="{{ __('Any special requests or notes for your stay...') }}">{{ old('special_requests') }}</textarea>
                                        @error('special_requests')
                                            <div class="error-message">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Method -->
                            <div class="form-section">
                                <div class="section-header">
                                    <h4 class="h5 fw-6">{{ __('Payment Method') }}</h4>
                                </div>
                                <div class="section-body">
                                    <div class="payment-methods-list">
                                        @php
                                            $supportedPaymentMethods = get_payment_methods();
                                        @endphp

                                        @if($supportedPaymentMethods)
                                            @foreach($supportedPaymentMethods as $method => $data)
                                                @if($data['status'] == 1)
                                                    <div class="payment-method-item">
                                                        <input class="payment-radio" type="radio" name="payment_method" id="payment_{{ $method }}" value="{{ $method }}" {{ $loop->first ? 'checked' : '' }}>
                                                        <label class="payment-label" for="payment_{{ $method }}">
                                                            <div class="payment-info">
                                                                <div class="payment-name">{{ $data['name'] }}</div>
                                                                @if(!empty($data['description']))
                                                                    <div class="payment-description">{{ $data['description'] }}</div>
                                                                @endif
                                                            </div>
                                                        </label>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @else
                                            <div class="alert alert-warning">
                                                {{ __('No payment methods available. Please contact support.') }}
                                            </div>
                                        @endif

                                        @error('payment_method')
                                            <div class="error-message">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Terms and Conditions -->
                            <div class="form-section">
                                <div class="section-body">
                                    <div class="terms-checkbox">
                                        <input class="tf-checkbox" type="checkbox" name="terms_accepted" id="terms_accepted" value="1" required {{ old('terms_accepted') ? 'checked' : '' }}>
                                        <label class="checkbox-label" for="terms_accepted">
                                            {{ __('I agree to the') }}
                                            <a href="#" target="_blank" class="text-primary">{{ __('Terms and Conditions') }}</a>
                                            {{ __('and') }}
                                            <a href="#" target="_blank" class="text-primary">{{ __('Cancellation Policy') }}</a>
                                            <span class="text-danger">*</span>
                                        </label>
                                    </div>
                                    @error('terms_accepted')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- House Rules -->
                            @if($property->house_rules)
                                <div class="form-section">
                                    <div class="section-header">
                                        <h4 class="h5 fw-6">{{ __('House Rules') }}</h4>
                                    </div>
                                    <div class="section-body">
                                        <div class="house-rules-content">
                                            {!! nl2br(e($property->house_rules)) !!}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Submit Button -->
                            <div class="form-submit">
                                <button type="submit" class="tf-btn primary w-100" id="submit-booking">
                                    <span class="btn-text">{{ __('Confirm Booking & Pay $:amount', ['amount' => number_format($pricing['total_amount'], 2)]) }}</span>
                                    <span class="btn-loading" style="display: none;">
                                        <i class="fas fa-spinner fa-spin"></i> {{ __('Processing...') }}
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Booking Form Wrapper */
        .booking-form-wrapper {
            margin-top: 2rem;
            margin-bottom: 3rem;
        }

        /* Booking Summary Section */
        .booking-summary-section {
            margin-bottom: 3rem;
        }

        .booking-details-card,
        .price-breakdown-card {
            background: #ffffff;
            border-radius: 6px;
            box-shadow: 0px 10px 25px rgba(54, 95, 104, 0.1);
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .booking-details-card .card-header,
        .price-breakdown-card .card-header {
            background: var(--primary-color);
            color: #ffffff;
            padding: 1.25rem 1.5rem;
            border-bottom: none;
        }

        .booking-details-card .card-body,
        .price-breakdown-card .card-body {
            padding: 1.5rem;
        }

        .booking-info-list .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e9ecef;
        }

        .booking-info-list .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 500;
            color: #63697d;
        }

        .info-value {
            font-weight: 500;
            color: #082479;
        }

        .price-breakdown-list .price-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e9ecef;
        }

        .price-breakdown-list .price-item:last-child {
            border-bottom: none;
        }

        .price-label {
            color: #63697d;
        }

        .price-value {
            font-weight: 500;
            color: #082479;
        }

        .price-divider {
            height: 1px;
            background: #dee2e6;
            margin: 1rem 0;
        }

        .total-price {
            background: #f3f5fa;
            margin: 0 -1.5rem;
            padding: 1rem 1.5rem !important;
            border-bottom: none !important;
        }

        .total-price .price-label,
        .total-price .price-value {
            font-size: 1.1rem;
            font-weight: 500;
            color: var(--primary-color);
        }

        .deposit-info {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        /* Form Sections */
        .booking-form-section {
            background: #ffffff;
            border-radius: 6px;
            box-shadow: 0px 10px 25px rgba(54, 95, 104, 0.1);
            overflow: hidden;
        }

        .form-section {
            border-bottom: 1px solid #e9ecef;
        }

        .form-section:last-child {
            border-bottom: none;
        }

        .section-header {
            background: #f3f5fa;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #e9ecef;
        }

        .section-body {
            padding: 1.5rem;
        }

        /* Form Controls */
        .ip-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: 500;
            color: #082479;
            margin-bottom: 0.5rem;
            display: block;
        }

        .error-message {
            color: #c72929;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        /* Payment Methods */
        .payment-methods-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .payment-method-item {
            position: relative;
        }

        .payment-radio {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .payment-label {
            display: block;
            padding: 1rem 1.25rem;
            border: 2px solid #e9ecef;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #ffffff;
        }

        .payment-label:hover {
            border-color: var(--primary-color);
            background: #f3f5fa;
        }

        .payment-radio:checked + .payment-label {
            border-color: var(--primary-color);
            background: #f3f5fa;
        }

        .payment-name {
            font-weight: 500;
            color: #082479;
            margin-bottom: 0.25rem;
        }

        .payment-description {
            font-size: 0.875rem;
            color: #63697d;
        }

        /* Terms Checkbox */
        .terms-checkbox {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .checkbox-label {
            font-size: 0.9rem;
            color: #63697d;
            line-height: 1.5;
            cursor: pointer;
        }

        /* House Rules */
        .house-rules-content {
            background: #f3f5fa;
            padding: 1.25rem;
            border-radius: 6px;
            border-left: 4px solid var(--primary-color);
            color: #63697d;
            line-height: 1.6;
        }

        /* Submit Button */
        .form-submit {
            padding: 2rem 1.5rem;
            background: #f8f9fa;
            text-align: center;
        }

        .form-submit .tf-btn {
            font-size: 1.1rem;
            font-weight: 500;
            padding: 1rem 2rem;
            min-height: 56px;
            position: relative;
        }

        .btn-loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .booking-details-card,
            .price-breakdown-card {
                margin-bottom: 1.5rem;
            }

            .section-header,
            .section-body {
                padding: 1rem;
            }

            .form-submit {
                padding: 1.5rem 1rem;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('booking-form');
            const submitBtn = document.getElementById('submit-booking');
            const btnText = submitBtn.querySelector('.btn-text');
            const btnLoading = submitBtn.querySelector('.btn-loading');

            form.addEventListener('submit', function(e) {
                console.log('Form submission started');

                // Show loading state
                btnText.style.display = 'none';
                btnLoading.style.display = 'inline-block';
                submitBtn.disabled = true;

                // Basic validation
                const requiredFields = form.querySelectorAll('[required]');
                let isValid = true;

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('is-invalid');
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    console.log('Form validation failed');
                    // Reset button state
                    btnText.style.display = 'inline-block';
                    btnLoading.style.display = 'none';
                    submitBtn.disabled = false;

                    alert('{{ __("Please fill in all required fields.") }}');
                    return;
                }

                console.log('Form validation passed, submitting...');
                // If validation passes, form will submit normally
            });

            // Reset form validation on input
            const inputs = form.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    this.classList.remove('is-invalid');
                });
            });
        });
    </script>
