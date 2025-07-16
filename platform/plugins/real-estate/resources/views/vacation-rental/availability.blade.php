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

                                    <div id="availability-calendar" class="calendar-container">
                                        <!-- Calendar will be rendered here -->
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">{{ __('Quick Actions') }}</h4>
                                    </div>
                                    <div class="card-body">
                                        <form id="block-dates-form" class="mb-3">
                                            @csrf
                                            <input type="hidden" name="property_id" value="{{ $selectedProperty->id }}">
                                            
                                            <div class="form-group mb-3">
                                                <label for="start_date" class="form-label">{{ __('Start Date') }}</label>
                                                <input type="date" name="start_date" id="start_date" class="form-control" required>
                                            </div>
                                            
                                            <div class="form-group mb-3">
                                                <label for="end_date" class="form-label">{{ __('End Date') }}</label>
                                                <input type="date" name="end_date" id="end_date" class="form-control" required>
                                            </div>
                                            
                                            <div class="form-group mb-3">
                                                <label for="reason" class="form-label">{{ __('Reason') }}</label>
                                                <input type="text" name="reason" id="reason" class="form-control" placeholder="{{ __('Optional reason') }}">
                                            </div>
                                            
                                            <div class="btn-group w-100">
                                                <button type="submit" name="action" value="block" class="btn btn-warning">
                                                    {{ __('Block Dates') }}
                                                </button>
                                                <button type="submit" name="action" value="unblock" class="btn btn-success">
                                                    {{ __('Unblock Dates') }}
                                                </button>
                                            </div>
                                        </form>
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

@push('footer')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if($selectedProperty && !empty($availabilityData))
                // Render calendar with availability data
                const availabilityData = @json($availabilityData);
                renderAvailabilityCalendar(availabilityData);
            @endif

            // Handle block/unblock dates form
            const blockDatesForm = document.getElementById('block-dates-form');
            if (blockDatesForm) {
                blockDatesForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    const action = e.submitter.value;
                    
                    const url = action === 'block' 
                        ? '{{ route("vacation-rental.block-dates") }}'
                        : '{{ route("vacation-rental.unblock-dates") }}';
                    
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    const headers = {};

                    if (csrfToken) {
                        headers['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
                    }

                    fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: headers
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            alert(data.message || 'An error occurred');
                        } else {
                            alert(data.message || 'Operation completed successfully');
                            window.location.reload();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while processing your request');
                    });
                });
            }
        });

        function renderAvailabilityCalendar(data) {
            // Simple calendar rendering - you can enhance this with a proper calendar library
            const calendarContainer = document.getElementById('availability-calendar');
            if (!calendarContainer) return;

            let calendarHtml = '<div class="simple-calendar">';
            
            // Add calendar grid here based on availability data
            // This is a simplified version - you might want to use a proper calendar library
            
            calendarHtml += '</div>';
            calendarContainer.innerHTML = calendarHtml;
        }
    </script>
@endpush
