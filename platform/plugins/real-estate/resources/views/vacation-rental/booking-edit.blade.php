@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('Edit Booking') }}</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('vacation-rental.booking.update', $booking->id) }}">
                        @csrf
                        @method('PUT')

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
                                <strong>{{ __('Vacation Rental') }}:</strong>
                            </div>
                            <div class="col-sm-9">
                                @if($booking->vacationRental)
                                    <a href="{{ route('vacation-rental.edit', $booking->vacationRental->id) }}" target="_blank">
                                        {{ $booking->vacationRental->name }}
                                    </a>
                                @else
                                    <span class="text-muted">{{ __('Vacation Rental not found') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <strong>{{ __('Check-in Date') }}:</strong>
                            </div>
                            <div class="col-sm-9">
                                {{ $booking->check_in_date->format('M d, Y') }}
                                <small class="text-muted">({{ __('Cannot be changed') }})</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <strong>{{ __('Check-out Date') }}:</strong>
                            </div>
                            <div class="col-sm-9">
                                {{ $booking->check_out_date->format('M d, Y') }}
                                <small class="text-muted">({{ __('Cannot be changed') }})</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <strong>{{ __('Guests') }}:</strong>
                            </div>
                            <div class="col-sm-9">
                                {{ $booking->guests_count }}

                                <small class="text-muted">({{ __('Cannot be changed') }})</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">{{ __('Status') }}</label>
                            <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                                @foreach(\Botble\RealEstate\Models\VacationRentalBooking::getStatuses() as $key => $label)
                                    <option value="{{ $key }}" {{ $booking->status === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="guest_name" class="form-label">{{ __('Guest Name') }}</label>
                            <input type="text" name="guest_name" id="guest_name" 
                                   class="form-control @error('guest_name') is-invalid @enderror" 
                                   value="{{ old('guest_name', $booking->guest_name) }}" required>
                            @error('guest_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="guest_email" class="form-label">{{ __('Guest Email') }}</label>
                            <input type="email" name="guest_email" id="guest_email" 
                                   class="form-control @error('guest_email') is-invalid @enderror" 
                                   value="{{ old('guest_email', $booking->guest_email) }}" required>
                            @error('guest_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="guest_phone" class="form-label">{{ __('Guest Phone') }}</label>
                            <input type="text" name="guest_phone" id="guest_phone" 
                                   class="form-control @error('guest_phone') is-invalid @enderror" 
                                   value="{{ old('guest_phone', $booking->guest_phone) }}">
                            @error('guest_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="special_requests" class="form-label">{{ __('Special Requests') }}</label>
                            <textarea name="special_requests" id="special_requests" 
                                      class="form-control @error('special_requests') is-invalid @enderror" 
                                      rows="4">{{ old('special_requests', $booking->special_requests) }}</textarea>
                            @error('special_requests')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <strong>{{ __('Total Amount') }}:</strong>
                            </div>
                            <div class="col-sm-9">
                                {{ format_price($booking->total_amount, $booking->vacationRental->currency ?? null) }}
                                <small class="text-muted">({{ __('Cannot be changed') }})</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <strong>{{ __('Created At') }}:</strong>
                            </div>
                            <div class="col-sm-9">
                                {{ $booking->created_at->format('M d, Y H:i') }}
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('vacation-rental.admin.bookings') }}" class="btn btn-secondary">
                                <x-core::icon name="ti ti-arrow-left" />
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <x-core::icon name="ti ti-device-floppy" />
                                {{ __('Update Booking') }}
                            </button>
                        </div>
                    </form>
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
                            {{ format_price($booking->base_price_per_night, $booking->vacationRental->currency ?? null) }}
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-8">{{ __('Total Nights Cost') }}:</div>
                        <div class="col-4 text-end">
                            {{ format_price($booking->total_nights_cost, $booking->vacationRental->currency ?? null) }}
                        </div>
                    </div>

                    @if($booking->cleaning_fee > 0)
                    <div class="row mb-2">
                        <div class="col-8">{{ __('Cleaning Fee') }}:</div>
                        <div class="col-4 text-end">
                            {{ format_price($booking->cleaning_fee, $booking->vacationRental->currency ?? null) }}
                        </div>
                    </div>
                    @endif

                    @if($booking->service_fee > 0)
                    <div class="row mb-2">
                        <div class="col-8">{{ __('Service Fee') }}:</div>
                        <div class="col-4 text-end">
                            {{ format_price($booking->service_fee, $booking->vacationRental->currency ?? null) }}
                        </div>
                    </div>
                    @endif

                    @if($booking->taxes > 0)
                    <div class="row mb-2">
                        <div class="col-8">{{ __('Taxes') }}:</div>
                        <div class="col-4 text-end">
                            {{ format_price($booking->taxes, $booking->vacationRental->currency ?? null) }}
                        </div>
                    </div>
                    @endif

                    @if($booking->security_deposit > 0)
                    <div class="row mb-2">
                        <div class="col-8">{{ __('Security Deposit') }}:</div>
                        <div class="col-4 text-end">
                            {{ format_price($booking->security_deposit, $booking->vacationRental->currency ?? null) }}
                        </div>
                    </div>
                    @endif

                    <hr>
                    <div class="row">
                        <div class="col-8"><strong>{{ __('Total Amount') }}:</strong></div>
                        <div class="col-4 text-end">
                            <strong>{{ format_price($booking->total_amount, $booking->vacationRental->currency ?? null) }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h4 class="card-title">{{ __('Quick Actions') }}</h4>
                </div>
                <div class="card-body">
                    <a href="{{ route('vacation-rental.booking.show', $booking->id) }}" class="btn btn-info btn-sm mb-2">
                        <x-core::icon name="ti ti-eye" />
                        {{ __('View Details') }}
                    </a>
                    <br>
                    <form method="POST" action="{{ route('vacation-rental.booking.destroy', $booking->id) }}" 
                          style="display: inline-block;" 
                          onsubmit="return confirm('{{ __('Are you sure you want to delete this booking?') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">
                            <x-core::icon name="ti ti-trash" />
                            {{ __('Delete Booking') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
