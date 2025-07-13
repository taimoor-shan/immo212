<!-- Booking Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bookingModalLabel">{{ __('Complete Your Booking') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Display validation errors -->
                <div id="booking-errors" class="alert alert-danger" style="display: none;">
                    <ul id="booking-errors-list"></ul>
                </div>

                <!-- Display session messages -->
                <div id="booking-success" class="alert alert-success" style="display: none;"></div>

                <form method="POST" action="{{ route('public.vacation-rental.booking.process') }}" id="booking-modal-form">
                    @csrf
                    <input type="hidden" name="property_id" value="{{ $property->id }}">
                    <input type="hidden" name="check_in_date" id="modal-check-in">
                    <input type="hidden" name="check_out_date" id="modal-check-out">
                    <input type="hidden" name="guests_count" id="modal-guests">

                    <!-- Booking Summary -->
                    <div class="booking-summary mb-4">
                        <h6>{{ __('Booking Details') }}</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <strong>{{ __('Property') }}:</strong>
                                    <span>{{ $property->name }}</span>
                                </div>
                                <div class="detail-item">
                                    <strong>{{ __('Check-in') }}:</strong>
                                    <span id="summary-checkin"></span>
                                </div>
                                <div class="detail-item">
                                    <strong>{{ __('Check-out') }}:</strong>
                                    <span id="summary-checkout"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <strong>{{ __('Guests') }}:</strong>
                                    <span id="summary-guests"></span>
                                </div>
                                <div class="detail-item">
                                    <strong>{{ __('Nights') }}:</strong>
                                    <span id="summary-nights"></span>
                                </div>
                                <div class="detail-item">
                                    <strong>{{ __('Total Amount') }}:</strong>
                                    <span id="summary-total" class="fw-bold text-primary"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Guest Information -->
                    <div class="guest-info mb-4">
                        <h6>{{ __('Guest Information') }}</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Full Name') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="guest_name" class="form-control" required placeholder="{{ __('Enter your full name') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Email Address') }} <span class="text-danger">*</span></label>
                                    <input type="email" name="guest_email" class="form-control" required placeholder="{{ __('Enter your email') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Phone Number') }}</label>
                                    <input type="tel" name="guest_phone" class="form-control" placeholder="{{ __('Enter your phone number') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Adults') }}</label>
                                    <input type="number" name="adults_count" class="form-control" min="0" value="1">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Children') }}</label>
                                    <input type="number" name="children_count" class="form-control" min="0" value="0">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Special Requests -->
                    <div class="special-requests mb-4">
                        <h6>{{ __('Special Requests') }}</h6>
                        <textarea name="special_requests" class="form-control" rows="3" placeholder="{{ __('Any special requests or notes for your stay...') }}"></textarea>
                    </div>

                    <!-- Payment Method -->
                    <div class="payment-method mb-4">
                        <h6>{{ __('Payment Method') }}</h6>
                        @php
                            $supportedPaymentMethods = get_payment_methods();
                        @endphp

                        @if($supportedPaymentMethods)
                            @foreach($supportedPaymentMethods as $method => $data)
                                @if($data['status'] == 1)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="payment_method" id="modal_payment_{{ $method }}" value="{{ $method }}" {{ $loop->first ? 'checked' : '' }}>
                                        <label class="form-check-label" for="modal_payment_{{ $method }}">
                                            <div class="payment-info">
                                                <div class="payment-name fw-semibold">{{ $data['name'] }}</div>
                                                @if(!empty($data['description']))
                                                    <div class="payment-description text-muted small">{{ $data['description'] }}</div>
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
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="terms mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="terms_accepted" id="modal-terms" value="1" required>
                            <label class="form-check-label" for="modal-terms">
                                {{ __('I agree to the') }}
                                <a href="#" target="_blank" class="text-primary">{{ __('Terms and Conditions') }}</a>
                                {{ __('and') }}
                                <a href="#" target="_blank" class="text-primary">{{ __('Cancellation Policy') }}</a>
                                <span class="text-danger">*</span>
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" form="booking-modal-form" class="btn btn-primary" id="modal-submit-booking">
                    <span class="btn-text">{{ __('Confirm Booking & Pay') }}</span>
                    <span class="btn-loading" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i> {{ __('Processing...') }}
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.detail-item {
    margin-bottom: 0.5rem;
}

.detail-item strong {
    display: inline-block;
    width: 100px;
}

.payment-info {
    margin-left: 0.5rem;
}

.payment-description {
    font-size: 0.875rem;
}

.btn-loading {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const bookingForm = document.getElementById('booking-modal-form');
    const submitBtn = document.getElementById('modal-submit-booking');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnLoading = submitBtn.querySelector('.btn-loading');
    const errorsDiv = document.getElementById('booking-errors');
    const errorsList = document.getElementById('booking-errors-list');
    const successDiv = document.getElementById('booking-success');

    // Handle form submission
    bookingForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Show loading state
        btnText.style.display = 'none';
        btnLoading.style.display = 'inline-block';
        submitBtn.disabled = true;
        
        // Hide previous errors
        errorsDiv.style.display = 'none';
        successDiv.style.display = 'none';

        // Submit form via AJAX
        const formData = new FormData(bookingForm);
        
        fetch(bookingForm.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                // Show validation errors
                if (data.message) {
                    errorsList.innerHTML = '<li>' + data.message + '</li>';
                    errorsDiv.style.display = 'block';
                }
            } else {
                // Success - redirect to payment or success page
                if (data.data && data.data.checkoutUrl) {
                    window.location.href = data.data.checkoutUrl;
                } else if (data.data && data.data.redirect) {
                    window.location.href = data.data.redirect;
                } else {
                    successDiv.innerHTML = data.message || '{{ __("Booking successful!") }}';
                    successDiv.style.display = 'block';
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            errorsList.innerHTML = '<li>{{ __("An error occurred. Please try again.") }}</li>';
            errorsDiv.style.display = 'block';
        })
        .finally(() => {
            // Reset button state
            btnText.style.display = 'inline-block';
            btnLoading.style.display = 'none';
            submitBtn.disabled = false;
        });
    });
});

// Function to populate modal with booking data
window.populateBookingModal = function(checkIn, checkOut, guests, pricing) {
    document.getElementById('modal-check-in').value = checkIn;
    document.getElementById('modal-check-out').value = checkOut;
    document.getElementById('modal-guests').value = guests;
    
    document.getElementById('summary-checkin').textContent = checkIn;
    document.getElementById('summary-checkout').textContent = checkOut;
    document.getElementById('summary-guests').textContent = guests;
    
    if (pricing) {
        document.getElementById('summary-nights').textContent = pricing.nights;
        document.getElementById('summary-total').textContent = '$' + pricing.total_amount.toFixed(2);
        document.querySelector('.btn-text').textContent = '{{ __("Confirm Booking & Pay $") }}' + pricing.total_amount.toFixed(2);
    }
};
</script>
