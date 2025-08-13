@if (RealEstateHelper::isEnabledProjects() && $property->project_id && ($project = $property->project))
    <div @class(['single-property-project', $class ?? null])>
        <div class="box-project mt-3 justify-content-between gap-4">
            
            <div class="project-info mb-4 mb-lg-0">
                <div class="h7 title fw-6 mb-4">{{ __("This building is part of the :project project", ['project' => $project->name]) }}</div>
                <a class="tf-btn secondary sm" href="{{ $project->url }}">
                    {{  __("View the project") }}
                </a>
            </div>
            <div class="project-thumb">
                <a href="{{ $project->url }}">
                    {{ RvMedia::image($project->image, $project->name) }}
                </a>
            </div>
        </div>
    </div>
@endif