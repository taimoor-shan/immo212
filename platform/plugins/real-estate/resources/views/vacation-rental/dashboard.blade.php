@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="row">
        <div class="col-md-6 col-xl-3">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-primary text-white avatar">
                                <x-core::icon name="ti ti-building" />
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">
                                {{ number_format($totalProperties) }}
                            </div>
                            <div class="text-muted">
                                {{ __('Total Properties') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-xl-3">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-success text-white avatar">
                                <x-core::icon name="ti ti-calendar-check" />
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">
                                {{ number_format($totalBookings) }}
                            </div>
                            <div class="text-muted">
                                {{ __('Total Bookings') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-xl-3">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-info text-white avatar">
                                <x-core::icon name="ti ti-users" />
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">
                                {{ number_format($activeBookings) }}
                            </div>
                            <div class="text-muted">
                                {{ __('Active Bookings') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-xl-3">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-warning text-white avatar">
                                <x-core::icon name="ti ti-currency-dollar" />
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">
                                {{ format_price($monthlyRevenue) }}
                            </div>
                            <div class="text-muted">
                                {{ __('Monthly Revenue') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Recent Bookings') }}</h3>
                </div>
                <div class="card-body">
                    @if($recentBookings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-vcenter">
                                <thead>
                                    <tr>
                                        <th>{{ __('Booking #') }}</th>
                                        <th>{{ __('Property') }}</th>
                                        <th>{{ __('Guest') }}</th>
                                        <th>{{ __('Check-in') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentBookings as $booking)
                                        <tr>
                                            <td>
                                                <a href="{{ route('vacation-rental.booking.show', $booking->id) }}">
                                                    {{ $booking->booking_number }}
                                                </a>
                                            </td>
                                            <td>{{ $booking->property?->name ?? 'N/A' }}</td>
                                            <td>{{ $booking->guest_name }}</td>
                                            <td>{{ $booking->check_in_date->format('M d, Y') }}</td>
                                            <td>
                                                @php
                                                    $statusClass = match($booking->status) {
                                                        'pending' => 'warning',
                                                        'confirmed' => 'success',
                                                        'cancelled' => 'danger',
                                                        'completed' => 'info',
                                                        default => 'secondary'
                                                    };
                                                @endphp
                                                <span class="badge bg-{{ $statusClass }}">{{ ucfirst($booking->status) }}</span>
                                            </td>
                                            <td>{{ format_price($booking->total_amount, $booking->property?->currency) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty">
                            <div class="empty-icon">
                                <x-core::icon name="ti ti-calendar-x" />
                            </div>
                            <p class="empty-title">{{ __('No bookings yet') }}</p>
                            <p class="empty-subtitle text-muted">
                                {{ __('Bookings will appear here once guests start making reservations.') }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Upcoming Check-ins') }}</h3>
                </div>
                <div class="card-body">
                    @if($upcomingCheckIns->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($upcomingCheckIns as $booking)
                                <div class="list-group-item">
                                    <div class="row align-items-center">
                                        <div class="col text-truncate">
                                            <div class="text-body d-block">{{ $booking->guest_name }}</div>
                                            <div class="d-block text-muted text-truncate mt-n1">
                                                {{ $booking->property?->name }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <div class="text-muted">
                                                {{ $booking->check_in_date->format('M d') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty">
                            <div class="empty-icon">
                                <x-core::icon name="ti ti-calendar" />
                            </div>
                            <p class="empty-title">{{ __('No upcoming check-ins') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            @if($propertiesNeedingAttention->count() > 0)
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Properties Needing Attention') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @foreach($propertiesNeedingAttention as $property)
                                <div class="list-group-item">
                                    <div class="row align-items-center">
                                        <div class="col text-truncate">
                                            <div class="text-body d-block">{{ $property->name }}</div>
                                            <div class="d-block text-muted text-truncate mt-n1">
                                                @if($property->moderation_status === 'pending')
                                                    {{ __('Pending approval') }}
                                                @elseif(!$property->check_in_time)
                                                    {{ __('Missing check-in time') }}
                                                @elseif(!$property->check_out_time)
                                                    {{ __('Missing check-out time') }}
                                                @elseif(!$property->minimum_stay)
                                                    {{ __('Missing minimum stay') }}
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <a href="{{ route('property.edit', $property->id) }}" class="btn btn-sm btn-outline-primary">
                                                {{ __('Edit') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
