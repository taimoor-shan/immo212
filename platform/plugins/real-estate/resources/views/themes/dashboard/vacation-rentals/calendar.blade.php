@extends('plugins/real-estate::themes.dashboard.layouts.master')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Availability Calendar') }}</h3>
        </div>
        
        <div class="card-body">
            <!-- Property Selection -->
            <form method="GET" class="row g-3 mb-4" id="property-selection-form">
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
                
                @if($selectedProperty)
                    <div class="col-md-2">
                        <label class="form-label">{{ __('Year') }}</label>
                        <select name="year" class="form-select" onchange="this.form.submit()">
                            @for($y = date('Y') - 1; $y <= date('Y') + 2; $y++)
                                <option value="{{ $y }}" {{ request('year', date('Y')) == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">{{ __('Month') }}</label>
                        <select name="month" class="form-select" onchange="this.form.submit()">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ request('month', date('n')) == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                @endif
            </form>

            @if($selectedProperty && !empty($monthlyData))
                <div class="row mb-4">
                    <div class="col-md-8">
                        <h4>{{ $selectedProperty->name }}</h4>
                        <p class="text-muted">
                            {{ \Carbon\Carbon::create(request('year', date('Y')), request('month', date('n')))->format('F Y') }}
                        </p>
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

                <!-- Summary Statistics -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="h3 text-success">{{ $monthlyData['summary']['available_days'] }}</div>
                                <div class="text-muted">{{ __('Available Days') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="h3 text-primary">{{ $monthlyData['summary']['booked_days'] }}</div>
                                <div class="text-muted">{{ __('Booked Days') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="h3 text-warning">{{ $monthlyData['summary']['blocked_days'] }}</div>
                                <div class="text-muted">{{ __('Blocked Days') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="h3 text-info">{{ number_format($monthlyData['summary']['occupancy_rate'], 1) }}%</div>
                                <div class="text-muted">{{ __('Occupancy Rate') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Revenue Summary -->
                @if($monthlyData['summary']['total_revenue'] > 0)
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body text-center">
                                    <div class="h3 text-success">${{ number_format($monthlyData['summary']['total_revenue'], 2) }}</div>
                                    <div class="text-muted">{{ __('Total Revenue') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body text-center">
                                    <div class="h3 text-info">${{ number_format($monthlyData['summary']['average_rate'], 2) }}</div>
                                    <div class="text-muted">{{ __('Average Nightly Rate') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Calendar Grid -->
                <div class="calendar-grid">
                    @php
                        $year = request('year', date('Y'));
                        $month = request('month', date('n'));
                        $firstDay = \Carbon\Carbon::create($year, $month, 1);
                        $lastDay = $firstDay->copy()->endOfMonth();
                        $startCalendar = $firstDay->copy()->startOfWeek();
                        $endCalendar = $lastDay->copy()->endOfWeek();
                        $availability = $monthlyData['availability'];
                    @endphp

                    <div class="table-responsive">
                        <table class="table table-bordered calendar-table">
                            <thead>
                                <tr>
                                    <th class="text-center">{{ __('Sun') }}</th>
                                    <th class="text-center">{{ __('Mon') }}</th>
                                    <th class="text-center">{{ __('Tue') }}</th>
                                    <th class="text-center">{{ __('Wed') }}</th>
                                    <th class="text-center">{{ __('Thu') }}</th>
                                    <th class="text-center">{{ __('Fri') }}</th>
                                    <th class="text-center">{{ __('Sat') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $currentDate = $startCalendar->copy();
                                @endphp
                                @while($currentDate <= $endCalendar)
                                    <tr>
                                        @for($i = 0; $i < 7; $i++)
                                            @php
                                                $dateKey = $currentDate->format('Y-m-d');
                                                $dayInfo = $availability[$dateKey] ?? null;
                                                $isCurrentMonth = $currentDate->month == $month;
                                                $isToday = $currentDate->isToday();
                                            @endphp
                                            <td class="calendar-day {{ !$isCurrentMonth ? 'text-muted' : '' }} {{ $isToday ? 'today' : '' }}" 
                                                style="height: 100px; vertical-align: top; position: relative;">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <span class="day-number {{ !$isCurrentMonth ? 'text-muted' : '' }}">
                                                        {{ $currentDate->day }}
                                                    </span>
                                                    @if($dayInfo && $isCurrentMonth)
                                                        <span class="badge badge-sm bg-{{ $dayInfo['status'] === 'available' ? 'success' : ($dayInfo['status'] === 'booked' ? 'primary' : ($dayInfo['status'] === 'blocked' ? 'warning' : 'danger')) }}">
                                                            {{ substr(ucfirst($dayInfo['status']), 0, 1) }}
                                                        </span>
                                                    @endif
                                                </div>
                                                @if($dayInfo && $isCurrentMonth && $dayInfo['price'])
                                                    <div class="small text-muted mt-1">
                                                        ${{ number_format($dayInfo['price'], 0) }}
                                                    </div>
                                                @endif
                                            </td>
                                            @php
                                                $currentDate->addDay();
                                            @endphp
                                        @endfor
                                    </tr>
                                @endwhile
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Legend -->
                <div class="mt-3">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="d-flex flex-wrap gap-3">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-success text-success-fg me-2">A</span>
                                    <span>{{ __('Available') }}</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-primary me-2">B</span>
                                    <span>{{ __('Booked') }}</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-warning me-2">X</span>
                                    <span>{{ __('Blocked') }}</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-danger me-2">M</span>
                                    <span>{{ __('Maintenance') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Events List -->
                @if(!empty($monthlyData['events']))
                    <div class="mt-4">
                        <h5>{{ __('Events This Month') }}</h5>
                        <div class="list-group">
                            @foreach($monthlyData['events'] as $event)
                                <div class="list-group-item">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <div class="avatar avatar-sm" style="background-color: {{ $event['color'] }}">
                                                <x-core::icon name="ti ti-calendar-event" class="text-white" />
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">{{ $event['title'] }}</div>
                                            <div class="text-muted">
                                                {{ \Carbon\Carbon::parse($event['start_date'])->format('M j') }} - 
                                                {{ \Carbon\Carbon::parse($event['end_date'])->format('M j, Y') }}
                                            </div>
                                            @if($event['description'])
                                                <div class="text-muted small">{{ $event['description'] }}</div>
                                            @endif
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
                @endif
            @elseif($selectedProperty)
                <div class="empty">
                    <div class="empty-icon">
                        <x-core::icon name="ti ti-calendar-x" />
                    </div>
                    <p class="empty-title">{{ __('No data available') }}</p>
                    <p class="empty-subtitle text-muted">
                        {{ __('No availability data found for the selected month.') }}
                    </p>
                </div>
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

    <style>
        .calendar-table {
            table-layout: fixed;
        }
        
        .calendar-day {
            width: 14.28%;
            min-height: 100px;
        }
        
        .calendar-day.today {
            background-color: rgba(13, 110, 253, 0.1);
        }
        
        .day-number {
            font-weight: 500;
        }
        
        .badge-sm {
            font-size: 0.7em;
            padding: 0.2em 0.4em;
        }
    </style>
@endsection
