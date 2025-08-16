@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">{{ trans('plugins/real-estate::vacation-rental.details') }}</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <tr>
                                    <td><strong>{{ trans('plugins/real-estate::vacation-rental.name') }}</strong></td>
                                    <td>{{ $vacationRental->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>{{ trans('plugins/real-estate::vacation-rental.location') }}</strong></td>
                                    <td>{{ $vacationRental->location }}</td>
                                </tr>
                                <tr>
                                    <td><strong>{{ trans('plugins/real-estate::vacation-rental.price_per_night') }}</strong></td>
                                    <td>{{ format_price($vacationRental->price) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>{{ trans('plugins/real-estate::vacation-rental.maximum_guests') }}</strong></td>
                                    <td>{{ $vacationRental->maximum_guests }}</td>
                                </tr>
                                <tr>
                                    <td><strong>{{ trans('plugins/real-estate::vacation-rental.minimum_stay') }}</strong></td>
                                    <td>{{ $vacationRental->minimum_stay }} {{ __('nights') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>{{ trans('core/base::tables.status') }}</strong></td>
                                    <td>{!! BaseHelper::clean($vacationRental->status->toHtml()) !!}</td>
                                </tr>
                                <tr>
                                    <td><strong>{{ trans('core/base::tables.created_at') }}</strong></td>
                                    <td>{{ BaseHelper::formatDate($vacationRental->created_at) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($vacationRental->description)
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">{{ trans('plugins/real-estate::vacation-rental.description') }}</h5>
                            </div>
                            <div class="card-body">
                                <p>{{ $vacationRental->description }}</p>
                            </div>
                        </div>
                    @endif

                    @if($vacationRental->content)
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">{{ trans('plugins/real-estate::vacation-rental.content') }}</h5>
                            </div>
                            <div class="card-body">
                                {!! BaseHelper::clean($vacationRental->content) !!}
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-md-4">
                    @if($vacationRental->images && count($vacationRental->images))
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">{{ trans('plugins/real-estate::vacation-rental.images') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($vacationRental->images as $image)
                                        <div class="col-6 mb-2">
                                            <img src="{{ RvMedia::getImageUrl($image, 'thumb') }}" alt="{{ $vacationRental->name }}" class="img-fluid rounded">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">{{ trans('plugins/real-estate::vacation-rental.booking_info') }}</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>{{ trans('plugins/real-estate::vacation-rental.check_in_time') }}:</strong> {{ $vacationRental->check_in_time }}</p>
                            <p><strong>{{ trans('plugins/real-estate::vacation-rental.check_out_time') }}:</strong> {{ $vacationRental->check_out_time }}</p>
                            @if($vacationRental->cleaning_fee)
                                <p><strong>{{ trans('plugins/real-estate::vacation-rental.cleaning_fee') }}:</strong> {{ format_price($vacationRental->cleaning_fee) }}</p>
                            @endif
                            @if($vacationRental->security_deposit)
                                <p><strong>{{ trans('plugins/real-estate::vacation-rental.security_deposit') }}:</strong> {{ format_price($vacationRental->security_deposit) }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('Actions') }}</h5>
                        </div>
                        <div class="card-body">
                            <a href="{{ route('vacation-rental.edit', $vacationRental) }}" class="btn btn-primary btn-sm">
                                <i class="fa fa-edit"></i> {{ trans('core/base::forms.edit') }}
                            </a>
                            <a href="{{ route('vacation-rental.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fa fa-list"></i> {{ __('Back to List') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
