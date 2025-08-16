@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ trans('plugins/real-estate::vacation-rental.name') }}</h5>
                </div>
                <div class="card-body">
                    {!! $dataTable->renderTable() !!}
                </div>
            </div>
        </div>
    </div>
@stop
