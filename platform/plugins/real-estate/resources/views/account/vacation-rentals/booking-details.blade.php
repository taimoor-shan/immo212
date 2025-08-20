@extends('plugins/real-estate::themes.dashboard.layouts.master')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ trans('plugins/real-estate::vacation-rental.booking_details') }}</h4>
                        <div class="card-actions">
                            <a href="{{ route('public.account.vacation-rentals.bookings') }}" class="btn btn-secondary">
                                <i class="ti ti-arrow-left"></i> {{ __('Back to Bookings') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="text-primary text-decoration-underline">{{ trans('plugins/real-estate::vacation-rental.booking_information') }}</h4>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>{{ trans('plugins/real-estate::vacation-rental.booking_number') }}:</strong></td>
                                        <td>{{ $booking->booking_number }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ trans('plugins/real-estate::vacation-rental.vacation_rental') }}:</strong></td>
                                        <td>{{ $booking->vacationRental->name ?? '—' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ trans('plugins/real-estate::vacation-rental.status') }}:</strong></td>
                                        <td>
                                            <span class="badge text-success-fg bg-{{ $booking->status === 'confirmed' ? 'success' : ($booking->status === 'cancelled' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($booking->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ trans('plugins/real-estate::vacation-rental.check_in_date') }}:</strong></td>
                                        <td>{{ $booking->check_in_date->format('M d, Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ trans('plugins/real-estate::vacation-rental.check_out_date') }}:</strong></td>
                                        <td>{{ $booking->check_out_date->format('M d, Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ trans('plugins/real-estate::vacation-rental.nights') }}:</strong></td>
                                        <td>{{ $booking->nights_count }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ trans('plugins/real-estate::vacation-rental.guests') }}:</strong></td>
                                        <td>{{ $booking->guests_count }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h4 class="text-primary text-decoration-underline">{{ trans('plugins/real-estate::vacation-rental.guest_information') }}</h4>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>{{ trans('plugins/real-estate::vacation-rental.guest_name') }}:</strong></td>
                                        <td>{{ $booking->guest_name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ trans('plugins/real-estate::vacation-rental.guest_email') }}:</strong></td>
                                        <td>{{ $booking->guest_email }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ trans('plugins/real-estate::vacation-rental.guest_phone') }}:</strong></td>
                                        <td>{{ $booking->guest_phone ?? '—' }}</td>
                                    </tr>
                                </table>

                                <h4 class="mt-4 text-primary text-decoration-underline">{{ trans('plugins/real-estate::vacation-rental.pricing_details') }}</h4>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>{{ trans('plugins/real-estate::vacation-rental.total_amount') }}:</strong></td>
                                        <td>
                                            @if($booking->vacationRental && $booking->vacationRental->currency)
                                                {{ format_price($booking->total_amount, $booking->vacationRental->currency) }}
                                            @else
                                                {{ format_price($booking->total_amount) }}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('Booking Date') }}:</strong></td>
                                        <td>{{ $booking->created_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        @if($booking->special_requests)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h5>{{ trans('plugins/real-estate::vacation-rental.special_requests') }}</h5>
                                    <div class="alert alert-info">
                                        {{ $booking->special_requests }}
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($booking->status === 'pending')
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h5>{{ __('Actions') }}</h5>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-success" onclick="updateBookingStatus('confirmed')">
                                            <i class="ti ti-check"></i> {{ __('Confirm Booking') }}
                                        </button>
                                        <button type="button" class="btn btn-danger" onclick="updateBookingStatus('cancelled')">
                                            <i class="ti ti-x"></i> {{ __('Cancel Booking') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateBookingStatus(status) {
            if (!confirm('{{ __("Are you sure you want to update this booking status?") }}')) {
                return;
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            const headers = {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            };

            if (csrfToken) {
                headers['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
            }

            fetch(`{{ route('public.account.vacation-rentals.bookings.update-status', $booking->id) }}`, {
                method: 'PUT',
                headers: headers,
                body: JSON.stringify({ status: status })
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.message || '{{ __("An error occurred") }}');
                } else {
                    alert(data.message || '{{ __("Booking status updated successfully") }}');
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('{{ __("An error occurred while updating the booking status") }}');
            });
        }
    </script>
@endsection
