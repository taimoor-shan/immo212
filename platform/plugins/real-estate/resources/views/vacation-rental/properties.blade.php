@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="vacation-rental-properties">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">{{ __('Vacation Rental Properties') }}</h4>
                <a href="{{ route('property.create') }}?type=vacation_rental" class="btn btn-primary">
                    <x-core::icon name="ti ti-plus" class="me-2" />
                    {{ __('Add New Property') }}
                </a>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <x-core::icon name="ti ti-info-circle" class="me-2" />
                    {{ __('To manage availability and calendar for a vacation rental property, click "Edit" on any property below. The availability calendar will be available within the property edit form.') }}
                </div>
                <div class="table-responsive">
                    {!! $dataTable->table(['class' => 'table table-striped table-hover'], false) !!}
                </div>
            </div>
        </div>
    </div>
@endsection



@push('footer')
    {!! $dataTable->scripts() !!}
@endpush
