@extends('plugins/real-estate::themes.dashboard.layouts.master')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Availability Calendar') }}</h3>
        </div>

        <div class="card-body">
            <!-- Property Selection -->
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">{{ __('Select Property') }}</label>
                    <select name="property_id" class="form-select" id="property-select" onchange="this.form.submit()">
                        <option value="">{{ __('Choose a property...') }}</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}" {{ request('property_id') == $property->id ? 'selected' : '' }}>
                                {{ $property->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>

            @if($selectedProperty)
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h4>{{ $selectedProperty->name }}</h4>
                        <p class="text-muted">{{ __('Manage availability for your vacation rental property') }}</p>
                    </div>
                </div>

                <!-- Unified Calendar Component -->
                @include('plugins/real-estate::partials.components.vacation-rental-calendar', [
                    'context' => 'agent',
                    'vacationRental' => $selectedProperty,
                    'showActions' => true,
                    'showLegend' => true,
                    'showBookingForm' => false
                ])
            @else
                <div class="empty">
                    <div class="empty-icon">
                        <x-core::icon name="ti ti-calendar" />
                    </div>
                    <p class="empty-title">{{ __('Select a property') }}</p>
                    <p class="empty-subtitle text-muted">
                        {{ __('Choose a vacation rental property from the dropdown above to manage its availability.') }}
                    </p>
                </div>
            @endif
        </div>
    </div>
@endsection
