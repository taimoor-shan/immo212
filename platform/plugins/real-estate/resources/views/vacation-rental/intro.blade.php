@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="row justify-content-center">
        <div class="col-xxl-6 col-xl-8 col-lg-10 col-md-12">
            <div class="widget meta-boxes">
                <div class="widget-title">
                    <h4>{{ trans('plugins/real-estate::vacation-rental.intro.title') }}</h4>
                </div>
                <div class="widget-body">
                    <div class="text-center">
                        <img src="{{ asset('vendor/core/plugins/real-estate/images/vacation-rental-intro.svg') }}" alt="Vacation Rentals" class="mb-3" style="max-width: 300px;">
                        
                        <h3 class="mb-3">{{ trans('plugins/real-estate::vacation-rental.intro.welcome') }}</h3>
                        
                        <p class="text-muted mb-4">
                            {{ trans('plugins/real-estate::vacation-rental.intro.description') }}
                        </p>
                        
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card border-0 bg-light">
                                    <div class="card-body text-center">
                                        <i class="fas fa-home fa-2x text-primary mb-2"></i>
                                        <h6>{{ trans('plugins/real-estate::vacation-rental.intro.feature_1_title') }}</h6>
                                        <small class="text-muted">{{ trans('plugins/real-estate::vacation-rental.intro.feature_1_desc') }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 bg-light">
                                    <div class="card-body text-center">
                                        <i class="fas fa-calendar-alt fa-2x text-success mb-2"></i>
                                        <h6>{{ trans('plugins/real-estate::vacation-rental.intro.feature_2_title') }}</h6>
                                        <small class="text-muted">{{ trans('plugins/real-estate::vacation-rental.intro.feature_2_desc') }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 bg-light">
                                    <div class="card-body text-center">
                                        <i class="fas fa-chart-line fa-2x text-warning mb-2"></i>
                                        <h6>{{ trans('plugins/real-estate::vacation-rental.intro.feature_3_title') }}</h6>
                                        <small class="text-muted">{{ trans('plugins/real-estate::vacation-rental.intro.feature_3_desc') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-center gap-2">
                            <a href="{{ route('vacation-rental.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i>
                                {{ trans('plugins/real-estate::vacation-rental.intro.create_first') }}
                            </a>
                            
                            <a href="{{ route('vacation-rental.admin.overview') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-chart-bar"></i>
                                {{ trans('plugins/real-estate::vacation-rental.intro.view_overview') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
