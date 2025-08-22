<div class="homeya-box list-style-1" @if ($project->latitude && $project->longitude) data-lat="{{ $project->latitude }}" data-lng="{{ $project->longitude }}" @endif>
    <a href="{{ $project->url }}" class="images-group">
        <div class="images-style">
            {{ RvMedia::image($project->image, $project->name, 'medium-square') }}
        </div>
        <div class="top">
            <ul class="d-flex gap-4 flex-column">
            </ul>
            @if (RealEstateHelper::isEnabledWishlist())
                <button type="button" class="box-icon w-32"
                        data-type="project"
                        data-bb-toggle="add-to-wishlist"
                        data-id="{{ $project->getKey() }}"
                        data-add-message="{{ __('Added ":name" to wishlist successfully!', ['name' => $project->name]) }}"
                        data-remove-message="{{ __('Removed ":name" from wishlist successfully!', ['name' => $project->name]) }}"
                >
                    <x-core::icon name="ti ti-heart" />
                </button>
            @endif
        </div>
        @if($project->category)
            <div class="bottom">
                <span class="flag-tag style-2">{{ $project->category->name }}</span>
            </div>
        @endif
    </a>
    <div class="content">
        <div class="archive-top border-bottom-0">
            <div class="h7 text-capitalize">
                <a href="{{ $project->url }}" class="link line-clamp-1" title="{{ $project->name }}">{!! BaseHelper::clean($project->name) !!}</a>
            </div>
            @if($project->short_address)
                <div class="desc">

                    <p class="line-clamp-1">{{ $project->short_address }}</p>
                </div>
            @endif
        </div>
        <div class="d-flex justify-content-between align-items-center archive-bottom">
            <div class="d-flex align-items-center">
                <div class="h7 fw-5">{{ $project->formatted_price }}</div>
            </div>
        </div>
    </div>
</div>
