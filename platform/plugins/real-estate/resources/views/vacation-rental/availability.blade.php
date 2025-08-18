@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Availability Management') }}</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('vacation-rental.availability') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="property_id" class="form-label">{{ __('Select Property') }}</label>
                                    <select name="property_id" id="property_id" class="form-select">
                                        <option value="">{{ __('-- Select Property --') }}</option>
                                        @foreach($properties as $property)
                                            <option value="{{ $property->id }}" {{ request('property_id') == $property->id ? 'selected' : '' }}>
                                                {{ $property->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="month" class="form-label">{{ __('Month') }}</label>
                                    <input type="month" name="month" id="month" class="form-control" value="{{ request('month', now()->format('Y-m')) }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary w-100">{{ __('Load') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    @if($selectedProperty)
                        <div class="row">
                            <div class="col-md-8">
                                <div class="availability-calendar">
                                    <h4>{{ __('Availability Calendar for :property', ['property' => $selectedProperty->name]) }}</h4>

                                    <div class="calendar-legend mb-3">
                                        <div class="row">
                                            <div class="col-auto">
                                                <span class="badge bg-success text-success-fg me-2">{{ __('Available') }}</span>
                                            </div>
                                            <div class="col-auto">
                                                <span class="badge bg-danger me-2">{{ __('Booked') }}</span>
                                            </div>
                                            <div class="col-auto">
                                                <span class="badge bg-warning me-2">{{ __('Blocked') }}</span>
                                            </div>
                                            <div class="col-auto">
                                                <span class="badge bg-secondary me-2">{{ __('Maintenance') }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Admin Calendar Component -->
                                    @include('plugins/real-estate::partials.admin-calendar', [
                                        'vacationRental' => $selectedProperty
                                    ])
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">{{ __('Calendar Instructions') }}</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-info">
                                            <h6>{{ __('How to use the calendar:') }}</h6>
                                            <ul class="mb-0">
                                                <li>{{ __('Click on dates to select them') }}</li>
                                                <li>{{ __('Use the action buttons to block/unblock dates') }}</li>
                                                <li>{{ __('Set maintenance periods as needed') }}</li>
                                                <li>{{ __('Add optional reasons for blocking') }}</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                @if(!empty($calendarEvents))
                                    <div class="card mt-3">
                                        <div class="card-header">
                                            <h4 class="card-title">{{ __('Upcoming Events') }}</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="list-group list-group-flush">
                                                @foreach($calendarEvents['events'] as $event)
                                                    <div class="list-group-item">
                                                        <div class="row align-items-center">
                                                            <div class="col text-truncate">
                                                                <div class="text-body d-block">{{ $event['title'] }}</div>
                                                                <div class="d-block text-muted text-truncate mt-n1">
                                                                    {{ \Carbon\Carbon::parse($event['start_date'])->format('M d') }} -
                                                                    {{ \Carbon\Carbon::parse($event['end_date'])->format('M d') }}
                                                                </div>
                                                            </div>
                                                            <div class="col-auto">
                                                                <span class="badge" style="background-color: {{ $event['color'] }}">
                                                                    {{ ucfirst($event['event_type']) }}
                                                                </span>
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
                    @else
                        <div class="empty">
                            <div class="empty-icon">
                                <x-core::icon name="ti ti-calendar" />
                            </div>
                            <p class="empty-title">{{ __('Select a property to view availability') }}</p>
                            <p class="empty-subtitle text-muted">
                                {{ __('Choose a vacation rental property from the dropdown above to manage its availability.') }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- Admin calendar functionality is handled by admin-calendar.js --}}
