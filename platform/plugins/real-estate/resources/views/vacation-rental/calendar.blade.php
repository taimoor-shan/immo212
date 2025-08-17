@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Admin Calendar Management') }}</h3>
        </div>
        
        <div class="card-body">
            <!-- Property Selection -->
            <form method="GET" class="row g-3 mb-4" id="property-selection-form">
                <div class="col-md-8">
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
                
                @if($selectedProperty)
                    <div class="col-md-4">
                        <label class="form-label">{{ __('Actions') }}</label>
                        <div class="d-flex gap-2">
                            <a href="{{ route('vacation-rental.admin.bookings', ['property_id' => $selectedProperty->id]) }}" class="btn btn-outline-primary">
                                <i class="ti ti-calendar-check"></i>
                                {{ __('View Bookings') }}
                            </a>
                        </div>
                    </div>
                @endif
            </form>

            @if($selectedProperty)
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h4>{{ $selectedProperty->name }}</h4>
                        <p class="text-muted">{{ __('Manage availability and view bookings for this property') }}</p>
                    </div>
                </div>

                <!-- Unified Calendar Component -->
                @include('plugins/real-estate::partials.components.vacation-rental-calendar', [
                    'context' => 'admin',
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
                        {{ __('Choose a vacation rental property from the dropdown above to view its calendar.') }}
                    </p>
                </div>
            @endif
        </div>
    </div>
@endsection
