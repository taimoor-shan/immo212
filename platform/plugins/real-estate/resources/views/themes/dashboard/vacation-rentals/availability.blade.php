@extends('plugins/real-estate::themes.dashboard.layouts.master')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Availability Management') }}</h3>
        </div>
        
        <div class="card-body">
            <!-- Property Selection -->
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label">{{ __('Select Property') }}</label>
                    <select name="property_id" class="form-select" onchange="this.form.submit()">
                        <option value="">{{ __('Choose a property...') }}</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}" {{ request('property_id') == $property->id ? 'selected' : '' }}>
                                {{ $property->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                @if($selectedProperty)
                    <div class="col-md-3">
                        <label class="form-label">{{ __('Month') }}</label>
                        <input type="month" name="month" class="form-control" 
                               value="{{ request('month', \Carbon\Carbon::now()->format('Y-m')) }}" 
                               onchange="this.form.submit()">
                    </div>
                @endif
            </form>

            @if($selectedProperty)
                <div class="row mb-4">
                    <div class="col-md-8">
                        <h4>{{ $selectedProperty->name }}</h4>
                        <p class="text-muted">{{ __('Base price: $:price/night', ['price' => number_format($selectedProperty->price, 2)]) }}</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#blockDatesModal">
                            {{ __('Block Dates') }}
                        </button>
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#unblockDatesModal">
                            {{ __('Unblock Dates') }}
                        </button>
                    </div>
                </div>

                @if(!empty($availabilityData))
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Day') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Price/Night') }}</th>
                                    <th>{{ __('Min Stay') }}</th>
                                    <th>{{ __('Notes') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($availabilityData as $date => $dayInfo)
                                    <tr class="{{ $dayInfo['status'] === 'available' ? '' : 'table-' . ($dayInfo['status'] === 'booked' ? 'success' : ($dayInfo['status'] === 'blocked' ? 'warning' : 'danger')) }}">
                                        <td>{{ \Carbon\Carbon::parse($date)->format('M j, Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($date)->format('l') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $dayInfo['status'] === 'available' ? 'success' : ($dayInfo['status'] === 'booked' ? 'primary' : ($dayInfo['status'] === 'blocked' ? 'warning' : 'danger')) }}">
                                                {{ ucfirst($dayInfo['status']) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($dayInfo['price'])
                                                ${{ number_format($dayInfo['price'], 2) }}
                                                @if($dayInfo['price'] != $dayInfo['base_price'])
                                                    <small class="text-muted">({{ number_format($dayInfo['price_modifier'] * 100, 0) }}%)</small>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $dayInfo['minimum_stay'] ?? 1 }} {{ __('night(s)') }}
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $dayInfo['notes'] ?? '' }}</small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                @if(!empty($calendarEvents))
                    <div class="mt-4">
                        <h5>{{ __('Calendar Events') }}</h5>
                        <div class="row">
                            @foreach($calendarEvents as $event)
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <div class="avatar" style="background-color: {{ $event['color'] }}">
                                                        <x-core::icon name="ti ti-calendar-event" class="text-white" />
                                                    </div>
                                                </div>
                                                <div class="flex-fill">
                                                    <div class="font-weight-medium">{{ $event['title'] }}</div>
                                                    <div class="text-muted">
                                                        {{ \Carbon\Carbon::parse($event['start_date'])->format('M j') }} - 
                                                        {{ \Carbon\Carbon::parse($event['end_date'])->format('M j, Y') }}
                                                    </div>
                                                    @if($event['description'])
                                                        <div class="text-muted small">{{ $event['description'] }}</div>
                                                    @endif
                                                </div>
                                                <div class="ms-auto">
                                                    <span class="badge" style="background-color: {{ $event['color'] }}">
                                                        {{ ucfirst($event['event_type']) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @else
                <div class="empty">
                    <div class="empty-icon">
                        <x-core::icon name="ti ti-calendar-time" />
                    </div>
                    <p class="empty-title">{{ __('Select a property') }}</p>
                    <p class="empty-subtitle text-muted">
                        {{ __('Choose a vacation rental property from the dropdown above to manage its availability.') }}
                    </p>
                </div>
            @endif
        </div>
    </div>

    @if($selectedProperty)
        <!-- Block Dates Modal -->
        <div class="modal fade" id="blockDatesModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Block Dates') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="blockDatesForm">
                        <div class="modal-body">
                            <input type="hidden" name="property_id" value="{{ $selectedProperty->id }}">
                            <div class="mb-3">
                                <label class="form-label">{{ __('Start Date') }}</label>
                                <input type="date" name="start_date" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('End Date') }}</label>
                                <input type="date" name="end_date" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('Reason (Optional)') }}</label>
                                <input type="text" name="reason" class="form-control" placeholder="{{ __('e.g., Maintenance, Personal use') }}">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('Block Dates') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Unblock Dates Modal -->
        <div class="modal fade" id="unblockDatesModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Unblock Dates') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="unblockDatesForm">
                        <div class="modal-body">
                            <input type="hidden" name="property_id" value="{{ $selectedProperty->id }}">
                            <div class="mb-3">
                                <label class="form-label">{{ __('Start Date') }}</label>
                                <input type="date" name="start_date" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('End Date') }}</label>
                                <input type="date" name="end_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('Unblock Dates') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            document.getElementById('blockDatesForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                const headers = {};

                if (csrfToken) {
                    headers['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
                }

                fetch('{{ route("public.account.vacation-rentals.block-dates") }}', {
                    method: 'POST',
                    headers: headers,
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        // Show error toast notification
                        if (typeof Botble !== 'undefined' && Botble.showError) {
                            Botble.showError(data.message || '{{ __("An error occurred") }}');
                        } else {
                            alert(data.message || '{{ __("An error occurred") }}');
                        }
                    } else {
                        // Show success toast notification
                        if (data.message) {
                            if (typeof Botble !== 'undefined' && Botble.showSuccess) {
                                Botble.showSuccess(data.message);
                            }
                        }

                        // Reload page after a short delay to show the toast
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Show error toast notification
                    if (typeof Botble !== 'undefined' && Botble.showError) {
                        Botble.showError('{{ __("An error occurred") }}');
                    } else {
                        alert('{{ __("An error occurred") }}');
                    }
                });
            });

            document.getElementById('unblockDatesForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                const csrfToken2 = document.querySelector('meta[name="csrf-token"]');
                const headers2 = {};

                if (csrfToken2) {
                    headers2['X-CSRF-TOKEN'] = csrfToken2.getAttribute('content');
                }

                fetch('{{ route("public.account.vacation-rentals.unblock-dates") }}', {
                    method: 'POST',
                    headers: headers2,
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        // Show error toast notification
                        if (typeof Botble !== 'undefined' && Botble.showError) {
                            Botble.showError(data.message || '{{ __("An error occurred") }}');
                        } else {
                            alert(data.message || '{{ __("An error occurred") }}');
                        }
                    } else {
                        // Show success toast notification
                        if (data.message) {
                            if (typeof Botble !== 'undefined' && Botble.showSuccess) {
                                Botble.showSuccess(data.message);
                            }
                        }

                        // Reload page after a short delay to show the toast
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Show error toast notification
                    if (typeof Botble !== 'undefined' && Botble.showError) {
                        Botble.showError('{{ __("An error occurred") }}');
                    } else {
                        alert('{{ __("An error occurred") }}');
                    }
                });
            });
        </script>
    @endif
@endsection
