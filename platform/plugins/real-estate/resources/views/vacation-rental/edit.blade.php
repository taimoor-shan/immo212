@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            {!! $form->renderForm() !!}
        </div>
    </div>
@stop
