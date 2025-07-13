@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('Booking Details') }}</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>{{ __('Booking Number') }}:</strong>
                        </div>
                        <div class="col-sm-9">
                            {{ $booking->booking_number }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>{{ __('Property') }}:</strong>
                        </div>
                        <div class="col-sm-9">
                            @if($booking->property)
                                <a href="{{ route('property.edit', $booking->property->id) }}" target="_blank">
                                    {{ $booking->property->name }}
                                </a>
                            @else
                                <span class="text-muted">{{ __('Property not found') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>{{ __('Guest Name') }}:</strong>
                        </div>
                        <div class="col-sm-9">
                            {{ $booking->guest_name }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>{{ __('Guest Email') }}:</strong>
                        </div>
                        <div class="col-sm-9">
                            <a href="mailto:{{ $booking->guest_email }}">{{ $booking->guest_email }}</a>
                        </div>
                    </div>

                    @if($booking->guest_phone)
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>{{ __('Guest Phone') }}:</strong>
                        </div>
                        <div class="col-sm-9">
                            <a href="tel:{{ $booking->guest_phone }}">{{ $booking->guest_phone }}</a>
                        </div>
                    </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>{{ __('Check-in Date') }}:</strong>
                        </div>
                        <div class="col-sm-9">
                            {{ $booking->check_in_date->format('M d, Y') }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>{{ __('Check-out Date') }}:</strong>
                        </div>
                        <div class="col-sm-9">
                            {{ $booking->check_out_date->format('M d, Y') }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>{{ __('Nights') }}:</strong>
                        </div>
                        <div class="col-sm-9">
                            {{ $booking->nights_count }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>{{ __('Guests') }}:</strong>
                        </div>
                        <div class="col-sm-9">
                            {{ $booking->guests_count }}
                            @if($booking->adults_count || $booking->children_count)
                                ({{ $booking->adults_count }} {{ __('adults') }}, {{ $booking->children_count }} {{ __('children') }})
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>{{ __('Status') }}:</strong>
                        </div>
                        <div class="col-sm-9">
                            @php
                                $statusClass = match($booking->status) {
                                    'confirmed' => 'success',
                                    'pending' => 'warning',
                                    'cancelled' => 'danger',
                                    'completed' => 'info',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge bg-{{ $statusClass }}">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </div>
                    </div>

                    @if($booking->special_requests)
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>{{ __('Special Requests') }}:</strong>
                        </div>
                        <div class="col-sm-9">
                            {{ $booking->special_requests }}
                        </div>
                    </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>{{ __('Created At') }}:</strong>
                        </div>
                        <div class="col-sm-9">
                            {{ $booking->created_at->format('M d, Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('Pricing Details') }}</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-8">{{ __('Base Price per Night') }}:</div>
                        <div class="col-4 text-end">
                            {{ format_price($booking->base_price_per_night, $booking->property->currency ?? null) }}
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-8">{{ __('Total Nights Cost') }}:</div>
                        <div class="col-4 text-end">
                            {{ format_price($booking->total_nights_cost, $booking->property->currency ?? null) }}
                        </div>
                    </div>

                    @if($booking->cleaning_fee > 0)
                    <div class="row mb-2">
                        <div class="col-8">{{ __('Cleaning Fee') }}:</div>
                        <div class="col-4 text-end">
                            {{ format_price($booking->cleaning_fee, $booking->property->currency ?? null) }}
                        </div>
                    </div>
                    @endif

                    @if($booking->service_fee > 0)
                    <div class="row mb-2">
                        <div class="col-8">{{ __('Service Fee') }}:</div>
                        <div class="col-4 text-end">
                            {{ format_price($booking->service_fee, $booking->property->currency ?? null) }}
                        </div>
                    </div>
                    @endif

                    @if($booking->taxes > 0)
                    <div class="row mb-2">
                        <div class="col-8">{{ __('Taxes') }}:</div>
                        <div class="col-4 text-end">
                            {{ format_price($booking->taxes, $booking->property->currency ?? null) }}
                        </div>
                    </div>
                    @endif

                    @if($booking->security_deposit > 0)
                    <div class="row mb-2">
                        <div class="col-8">{{ __('Security Deposit') }}:</div>
                        <div class="col-4 text-end">
                            {{ format_price($booking->security_deposit, $booking->property->currency ?? null) }}
                        </div>
                    </div>
                    @endif

                    <hr>
                    <div class="row">
                        <div class="col-8"><strong>{{ __('Total Amount') }}:</strong></div>
                        <div class="col-4 text-end">
                            <strong>{{ format_price($booking->total_amount, $booking->property->currency ?? null) }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h4 class="card-title">{{ __('Actions') }}</h4>
                </div>
                <div class="card-body">
                    <a href="{{ route('vacation-rental.booking.edit', $booking->id) }}" class="btn btn-primary btn-sm mb-2">
                        <x-core::icon name="ti ti-edit" />
                        {{ __('Edit Booking') }}
                    </a>
                    <br>
                    <a href="{{ route('vacation-rental.bookings') }}" class="btn btn-secondary btn-sm">
                        <x-core::icon name="ti ti-arrow-left" />
                        {{ __('Back to Bookings') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
