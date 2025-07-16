@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="vacation-rental-dashboard">
        <!-- Statistics Cards -->
        <div class="row mb-4">
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

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('Quick Actions') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <a href="{{ route('vacation-rental.properties') }}" class="btn btn-primary btn-block">
                                    <x-core::icon name="ti ti-building" class="me-2" />
                                    {{ __('Manage Properties & Availability') }}
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="{{ route('vacation-rental.bookings') }}" class="btn btn-success btn-block">
                                    <x-core::icon name="ti ti-calendar-check" class="me-2" />
                                    {{ __('View Bookings') }}
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="{{ route('property.create') }}?type=vacation_rental" class="btn btn-info btn-block">
                                    <x-core::icon name="ti ti-plus" class="me-2" />
                                    {{ __('Add New Property') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('Recent Bookings') }}</h4>
                    </div>
                    <div class="card-body">
                        @if($recentBookings->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($recentBookings as $booking)
                                    <div class="list-group-item d-flex justify-content-between align-items-start">
                                        <div class="ms-2 me-auto">
                                            <div class="fw-bold">{{ $booking->property->name ?? 'N/A' }}</div>
                                            <small class="text-muted">{{ $booking->guest_name }}</small>
                                        </div>
                                        <span class="badge bg-primary rounded-pill">
                                            {{ format_price($booking->total_amount) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="empty">
                                <div class="empty-icon">
                                    <x-core::icon name="ti ti-calendar-x" />
                                </div>
                                <p class="empty-title">{{ __('No recent bookings') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('Upcoming Check-ins') }}</h4>
                    </div>
                    <div class="card-body">
                        @if($upcomingCheckIns->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($upcomingCheckIns as $booking)
                                    <div class="list-group-item d-flex justify-content-between align-items-start">
                                        <div class="ms-2 me-auto">
                                            <div class="fw-bold">{{ $booking->property->name ?? 'N/A' }}</div>
                                            <small class="text-muted">{{ $booking->guest_name }}</small>
                                        </div>
                                        <span class="badge bg-success text-success-fg rounded-pill">
                                            {{ $booking->check_in_date->format('M j') }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="empty">
                                <div class="empty-icon">
                                    <x-core::icon name="ti ti-calendar-check" />
                                </div>
                                <p class="empty-title">{{ __('No upcoming check-ins') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
