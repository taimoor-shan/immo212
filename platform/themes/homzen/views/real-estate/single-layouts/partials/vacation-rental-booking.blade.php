@if($property->type == \Botble\RealEstate\Enums\PropertyTypeEnum::VACATION_RENTAL)
    <div @class(['widget-box single-property-vacation-rental-booking', $class ?? null])>
        <div class="h7 title fw-6">{{ __('Book This Property') }}</div>
        
        @if (! RealEstateHelper::hideAgentInfoInPropertyDetailPage() && ($account = $property->author))
            <div class="box-avatar">
                <div class="avatar avt-100 round">
                    <a href="{{ $account->url }}" class="d-block">
                        {{ RvMedia::image($account->avatar?->url ?: $account->avatar_url, $account->name) }}
                    </a>
                </div>
                <div class="info line-clamp-1">
                    <div class="text-1 name">
                        <a href="{{ $account->url }}">{{ $account->name }}</a>
                    </div>
                    @if ($account->phone && ! setting('real_estate_hide_agency_phone', false))
                        <a href="tel:{{ $account->phone }}" class="info-item">{{ $account->phone }}</a>
                    @elseif($hotline = theme_option('hotline'))
                        <a href="tel:{{ $hotline }}" class="info-item">{{ $hotline }}</a>
                    @endif
                    @if ($account->email && ! setting('real_estate_hide_agency_email', false))
                        <a href="mailto:{{ $account->email }}" class="info-item">{{ $account->email }}</a>
                    @endif
                </div>
            </div>
        @endif

        <!-- Pricing Information -->
        <div class="vacation-rental-pricing mb-3">
            <div class="price-display">
                <span class="price-amount">{{ format_price($property->price, $property->currency) }}</span>
                <span class="price-period">/ {{ __('night') }}</span>
            </div>
            @if($property->minimum_stay)
                <div class="minimum-stay-info">
                    <small class="text-muted">{{ __('Minimum stay: :nights nights', ['nights' => $property->minimum_stay]) }}</small>
                </div>
            @endif
        </div>

        {!! apply_filters('property_right_details_info', null, $property) !!}

        {!! apply_filters('before_vacation_rental_booking_form', null, $property) !!}

        <!-- Availability Calendar Widget (if needed) -->
        <div class="availability-calendar-widget mb-3" id="availability-calendar-{{ $property->id }}">
            <!-- Calendar will be loaded here via JavaScript -->
        </div>

        {!! \Botble\RealEstate\Forms\Fronts\VacationRentalBookingInquiryForm::create()
            ->formClass('vacation-rental-booking-form')
            ->setFormInputWrapperClass('ip-group')
            ->modify('content', 'textarea', ['attr' => ['class' => 'form-control']])
            ->modify('submit', 'submit', ['attr' => ['class' => 'tf-btn primary w-100']])
            ->modify('property_id', 'hidden', ['attr' => ['value' => $property->getKey()]])
            ->renderForm()
        !!}

        {!! apply_filters('after_vacation_rental_booking_form', null, $property) !!}

        <!-- Booking Information -->
        <div class="booking-info mt-3">
            <div class="info-items">
                @if($property->maximum_guests)
                    <div class="info-item">
                        <x-core::icon name="ti ti-users" class="text-primary" />
                        <span>{{ __('Maximum :count guests', ['count' => $property->maximum_guests]) }}</span>
                    </div>
                @endif
                @if($property->check_in_time)
                    <div class="info-item">
                        <x-core::icon name="ti ti-clock" class="text-primary" />
                        <span>{{ __('Check-in: :time', ['time' => $property->check_in_time]) }}</span>
                    </div>
                @endif
                @if($property->check_out_time)
                    <div class="info-item">
                        <x-core::icon name="ti ti-clock" class="text-primary" />
                        <span>{{ __('Check-out: :time', ['time' => $property->check_out_time]) }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('footer')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.querySelector('.vacation-rental-booking-form');
                const propertyId = {{ $property->id }};
                const checkInInput = form.querySelector('input[name="check_in_date"]');
                const checkOutInput = form.querySelector('input[name="check_out_date"]');
                const guestsInput = form.querySelector('input[name="guests_count"]');

                // Initialize date pickers with availability checking
                if (checkInInput && checkOutInput) {
                    // Set minimum dates
                    const today = new Date().toISOString().split('T')[0];
                    checkInInput.setAttribute('min', today);
                    
                    checkInInput.addEventListener('change', function() {
                        const checkInDate = new Date(this.value);
                        const nextDay = new Date(checkInDate);
                        nextDay.setDate(nextDay.getDate() + 1);
                        checkOutInput.setAttribute('min', nextDay.toISOString().split('T')[0]);
                        
                        // Clear check-out if it's before new check-in
                        if (checkOutInput.value && new Date(checkOutInput.value) <= checkInDate) {
                            checkOutInput.value = '';
                        }
                    });
                }

                // Set maximum guests if specified
                @if($property->maximum_guests)
                    if (guestsInput) {
                        guestsInput.setAttribute('max', {{ $property->maximum_guests }});
                    }
                @endif

                // Form validation
                form.addEventListener('submit', function(e) {
                    const checkIn = checkInInput.value;
                    const checkOut = checkOutInput.value;
                    const guests = guestsInput.value;

                    if (!checkIn || !checkOut || !guests) {
                        e.preventDefault();
                        alert('{{ __("Please fill in all required fields.") }}');
                        return;
                    }

                    // Validate minimum stay
                    @if($property->minimum_stay)
                        const checkInDate = new Date(checkIn + 'T00:00:00');
                        const checkOutDate = new Date(checkOut + 'T00:00:00');

                        // Calculate nights properly by using date difference
                        const timeDiff = checkOutDate.getTime() - checkInDate.getTime();
                        const nights = Math.floor(timeDiff / (1000 * 60 * 60 * 24));

                        if (nights < {{ $property->minimum_stay }}) {
                            e.preventDefault();
                            alert('{{ __("Minimum stay is :nights nights.", ["nights" => $property->minimum_stay]) }}');
                            return;
                        }
                    @endif

                    // Validate guest count
                    @if($property->maximum_guests)
                        if (parseInt(guests) > {{ $property->maximum_guests }}) {
                            e.preventDefault();
                            alert('{{ __("Maximum :count guests allowed.", ["count" => $property->maximum_guests]) }}');
                            return;
                        }
                    @endif
                });
            });
        </script>
    @endpush
@endif
