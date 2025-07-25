<div class="advanced-search">
    <a href="#" class="advanced-search-toggler">{{ __('Advanced') }} <i class="fas fa-caret-down"></i></a>
    <div class="advanced-search-content property-advanced-search">
        <div class="form-group">
            <div class="row">
                @if(RealEstateHelper::isEnabledProjects())
                    <div class="col-sm-3 px-md-1">
                        {!! Theme::partial('real-estate.filters.by-project') !!}
                    </div>
                @endif
                <div @class(['px-md-1', 'col-sm-6' => RealEstateHelper::isEnabledProjects(), 'col-sm-12' => ! RealEstateHelper::isEnabledProjects()])>
                    {!! Theme::partial('real-estate.filters.categories', compact('categories')) !!}
                </div>
                {{-- Price filter moved to main display --}}
            </div>

            <div class="row">
                {{-- Bedroom filter moved to main display, keeping bathroom and floor in advanced --}}
                <div class="col-md-6 col-sm-6 px-md-1">
                    {!! Theme::partial('real-estate.filters.bathroom') !!}
                </div>
                <div class="col-md-6 col-sm-6 px-md-1">
                    {!! Theme::partial('real-estate.filters.floor') !!}
                </div>
            </div>
        </div>
    </div>

    @if ($enableProjectsSearch)
        <div class="advanced-search-content project-advanced-search">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        {!! Theme::partial('real-estate.filters.categories', compact('categories')) !!}
                    </div>
                    {{-- Price filter moved to main display --}}
                </div>
            </div>
        </div>
    @endif
</div>
