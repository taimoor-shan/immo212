@extends('plugins/real-estate::themes.dashboard.layouts.master')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Vacation Rental Bookings') }}</h3>
        </div>
        
        <div class="card-body">
            <!-- Filters -->
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label">{{ __('Status') }}</label>
                    <select name="status" class="form-select">
                        <option value="">{{ __('All Statuses') }}</option>
                        @foreach(\Botble\RealEstate\Models\VacationRentalBooking::getStatuses() as $key => $status)
                            <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">{{ __('Property') }}</label>
                    <select name="property_id" class="form-select">
                        <option value="">{{ __('All Properties') }}</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}" {{ request('property_id') == $property->id ? 'selected' : '' }}>
                                {{ $property->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label">{{ __('From Date') }}</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                
                <div class="col-md-2">
                    <label class="form-label">{{ __('To Date') }}</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
                    </div>
                </div>
            </form>

            @if($bookings->count() > 0)
                <div class="table-responsive">
                    <table class="table table-vcenter">
                        <thead>
                            <tr>
                                <th>{{ __('Booking #') }}</th>
                                <th>{{ __('Property') }}</th>
                                <th>{{ __('Guest') }}</th>
                                <th>{{ __('Check-in') }}</th>
                                <th>{{ __('Check-out') }}</th>
                                <th>{{ __('Nights') }}</th>
                                <th>{{ __('Guests') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Payment') }}</th>
                                <th>{{ __('Total') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookings as $booking)
                                <tr>
                                    <td>
                                        <div class="text-muted">#{{ $booking->booking_number }}</div>
                                        <div class="text-muted small">{{ $booking->created_at->format('M j, Y') }}</div>
                                    </td>
                                    <td>
                                        <div>{{ $booking->property->name }}</div>
                                    </td>
                                    <td>
                                        <div>{{ $booking->guest_name }}</div>
                                        <div class="text-muted small">{{ $booking->guest_email }}</div>
                                        @if($booking->guest_phone)
                                            <div class="text-muted small">{{ $booking->guest_phone }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $booking->check_in_date->format('M j, Y') }}</div>
                                        <div class="text-muted small">{{ $booking->check_in_date->format('l') }}</div>
                                    </td>
                                    <td>
                                        <div>{{ $booking->check_out_date->format('M j, Y') }}</div>
                                        <div class="text-muted small">{{ $booking->check_out_date->format('l') }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $booking->nights_count }}</span>
                                    </td>
                                    <td>
                                        <div>{{ $booking->guests_count }} {{ __('total') }}</div>

                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $booking->status === 'confirmed' ? 'success' : ($booking->status === 'pending' ? 'warning' : ($booking->status === 'cancelled' ? 'danger' : 'secondary')) }}">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $booking->payment_status === 'paid' ? 'success' : ($booking->payment_status === 'partial' ? 'warning' : ($booking->payment_status === 'refunded' ? 'info' : 'secondary')) }}">
                                            {{ ucfirst($booking->payment_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div>${{ number_format($booking->total_amount, 2) }}</div>
                                        @if($booking->security_deposit > 0)
                                            <div class="text-muted small">+${{ number_format($booking->security_deposit, 2) }} {{ __('deposit') }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            @if($booking->status === 'pending')
                                                <button type="button" class="btn btn-sm btn-success" onclick="updateBookingStatus({{ $booking->id }}, 'confirmed')">
                                                    {{ __('Confirm') }}
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="updateBookingStatus({{ $booking->id }}, 'cancelled')">
                                                    {{ __('Cancel') }}
                                                </button>
                                            @elseif($booking->status === 'confirmed')
                                                <button type="button" class="btn btn-sm btn-warning" onclick="updateBookingStatus({{ $booking->id }}, 'cancelled')">
                                                    {{ __('Cancel') }}
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-footer">
                    {{ $bookings->appends(request()->query())->links() }}
                </div>
            @else
                <div class="empty">
                    <div class="empty-icon">
                        <x-core::icon name="ti ti-calendar-x" />
                    </div>
                    <p class="empty-title">{{ __('No bookings found') }}</p>
                    <p class="empty-subtitle text-muted">
                        @if(request()->hasAny(['status', 'property_id', 'date_from', 'date_to']))
                            {{ __('No bookings match your current filters. Try adjusting your search criteria.') }}
                        @else
                            {{ __('You don\'t have any bookings yet. Bookings will appear here once guests start booking your vacation rental properties.') }}
                        @endif
                    </p>
                    @if(request()->hasAny(['status', 'property_id', 'date_from', 'date_to']))
                        <div class="empty-action">
                            <a href="{{ route('public.account.vacation-rentals.bookings') }}" class="btn btn-primary">
                                {{ __('Clear Filters') }}
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <script>
        function updateBookingStatus(bookingId, status) {
            if (!confirm('{{ __("Are you sure you want to update this booking status?") }}')) {
                return;
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            const headers = {
                'Content-Type': 'application/json'
            };

            if (csrfToken) {
                headers['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
            }

            fetch(`{{ route('public.account.vacation-rentals.bookings.update-status', '') }}/${bookingId}/status`, {
                method: 'PUT',
                headers: headers,
                body: JSON.stringify({ status: status })
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.message || '{{ __("An error occurred") }}');
                } else {
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('{{ __("An error occurred") }}');
            });
        }
    </script>
@endsection
