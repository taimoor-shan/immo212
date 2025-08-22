@extends('plugins/real-estate::themes.dashboard.layouts.master')

@section('content')
    <div class="mb-3 row row-cards">
        <div class="col-12 col-md-6 col-lg-3 dashboard-widget-item">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('Total Properties') }}</div>
                        <div class="ms-auto">
                            <x-core::icon name="ti ti-bed" class="text-primary" />
                        </div>
                    </div>
                    <div class="h1 mb-3">{{ $totalProperties }}</div>
                    <div class="d-flex mb-2">
                        <div class="text-muted">{{ __('Vacation rental properties') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3 dashboard-widget-item">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('Total Bookings') }}</div>
                        <div class="ms-auto">
                            <x-core::icon name="ti ti-calendar-check" class="text-success" />
                        </div>
                    </div>
                    <div class="h1 mb-3">{{ $totalBookings }}</div>
                    <div class="d-flex mb-2">
                        <div class="text-muted">{{ __('All time bookings') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3 dashboard-widget-item">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('Monthly Revenue') }}</div>
                        <div class="ms-auto">
                            <x-core::icon name="ti ti-currency-dollar" class="text-warning" />
                        </div>
                    </div>
                    <div class="h1 mb-3">${{ number_format($monthlyRevenue, 2) }}</div>
                    <div class="d-flex mb-2">
                        <div class="text-muted">{{ __('This month') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3 dashboard-widget-item">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('Upcoming Check-ins') }}</div>
                        <div class="ms-auto">
                            <x-core::icon name="ti ti-calendar-time" class="text-info" />
                        </div>
                    </div>
                    <div class="h1 mb-3">{{ $upcomingCheckIns->count() }}</div>
                    <div class="d-flex mb-2">
                        <div class="text-muted">{{ __('Next 7 days') }}</div>
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
                                        <th>{{ __('Booking') }}</th>
                                        <th>{{ __('Property') }}</th>
                                        <th>{{ __('Guest') }}</th>
                                        <th>{{ __('Dates') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentBookings as $booking)
                                        <tr>
                                            <td>
                                                <div class="text-muted">#{{ $booking->booking_number }}</div>
                                            </td>
                                            <td>
                                                <div>{{ $booking->property->name }}</div>
                                            </td>
                                            <td>
                                                <div>{{ $booking->guest_name }}</div>
                                                <div class="text-muted">{{ $booking->guest_email }}</div>
                                            </td>
                                            <td>
                                                <div>{{ $booking->check_in_date->format('M j') }} - {{ $booking->check_out_date->format('M j, Y') }}</div>
                                                <div class="text-muted">{{ $booking->nights_count }} {{ __('nights') }}</div>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $booking->status === 'confirmed' ? 'success' : ($booking->status === 'pending' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst($booking->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div>${{ number_format($booking->total_amount, 2) }}</div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('public.account.vacation-rentals.bookings') }}" class="btn btn-primary">
                                {{ __('View All Bookings') }}
                            </a>
                        </div>
                    @else
                        <div class="empty">
                            <div class="empty-icon">
                                <x-core::icon name="ti ti-calendar-x" />
                            </div>
                            <p class="empty-title">{{ __('No bookings yet') }}</p>
                            <p class="empty-subtitle text-muted">
                                {{ __('Your recent bookings will appear here once guests start booking your properties.') }}
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
                            @foreach($upcomingCheckIns as $checkIn)
                                <div class="list-group-item">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="status-dot status-dot-animated bg-green d-block"></span>
                                        </div>
                                        <div class="col text-truncate">
                                            <div class="text-body d-block">{{ $checkIn->guest_name }}</div>
                                            <div class="d-block text-muted text-truncate mt-n1">
                                                {{ $checkIn->property->name }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <div class="text-muted">
                                                {{ $checkIn->check_in_date->format('M j') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('public.account.vacation-rentals.bookings') }}?status=confirmed" class="btn btn-primary btn-sm">
                                {{ __('View All Check-ins') }}
                            </a>
                        </div>
                    @else
                        <div class="empty">
                            <div class="empty-icon">
                                <x-core::icon name="ti ti-calendar-time" />
                            </div>
                            <p class="empty-title">{{ __('No upcoming check-ins') }}</p>
                            <p class="empty-subtitle text-muted">
                                {{ __('Upcoming check-ins will appear here.') }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            @if($vacationRentals->count() > 0)
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Your Properties') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @foreach($vacationRentals->take(5) as $property)
                                <div class="list-group-item">
                                    <div class="row align-items-center">
                                        <div class="col text-truncate">
                                            <div class="text-body d-block">{{ $property->name }}</div>
                                            <div class="d-block text-muted text-truncate mt-n1">
                                                ${{ number_format($property->price, 2) }}/{{ __('night') }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <a href="{{ route('public.account.vacation-rentals.edit', $property->id) }}" class="btn btn-sm btn-outline-primary">
                                                {{ __('Edit') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($vacationRentals->count() > 5)
                            <div class="card-footer">
                                <a href="{{ route('public.account.vacation-rentals.index') }}" class="btn btn-primary btn-sm">
                                    {{ __('View All Vacation Rentals') }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
