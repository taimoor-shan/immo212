@php
    use Botble\RealEstate\Models\Account;
    
    // Get all available agencies/agents with public profiles
    $agencies = Account::query()
        ->where('is_public_profile', true)
        ->orderByDesc('is_featured')
        ->oldest('first_name')
        ->withCount([
            'properties' => function ($query) {
                return \Botble\Base\Supports\RepositoryHelper::applyBeforeExecuteQuery($query, $query->getModel());
            },
        ])
        ->with(['avatar'])
        ->limit(10) // Limit to 10 agencies to avoid overwhelming the sidebar
        ->get();
@endphp

@if($agencies->isNotEmpty())
    <div class="widget-box bg-surface">
        <div class="h7 title fw-6 text-black mb-3">{{ __('Our Agencies') }}</div>
        
        <div class="agencies-sidebar-list">
            @foreach($agencies as $agency)
                <div class="agency-card-sidebar mb-3">
                    <div class="box-agent style-sidebar hover-img">
                        <div class="agency-card-content">
                            <div class="agency-avatar-small">
                                @if (\Botble\RealEstate\Facades\RealEstateHelper::isDisabledPublicProfile())
                                    {{ RvMedia::image($agency->avatar_url, $agency->name, 'thumb') }}
                                @else
                                    <a href="{{ $agency->url }}">
                                        {{ RvMedia::image($agency->avatar_url, $agency->name, 'thumb') }}
                                    </a>
                                @endif
                            </div>
                            
                            <div class="agency-info-small">
                                <div class="agency-name">
                                    @if (\Botble\RealEstate\Facades\RealEstateHelper::isDisabledPublicProfile())
                                        <h6 class="mb-1">{{ $agency->name }}</h6>
                                    @else
                                        <a href="{{ $agency->url }}">
                                            <h6 class="link mb-1">{{ $agency->name }}</h6>
                                        </a>
                                    @endif
                                </div>
                                
                                @if($agency->company)
                                    <p class="agency-company text-variant-1 mb-1">{{ $agency->company }}</p>
                                @endif
                                
                                <div class="agency-meta-small">
                                    @if ($agency->properties_count)
                                        <span class="properties-count text-variant-1">
                                            <x-core::icon name="ti ti-home" class="icon-small" />
                                            @if ($agency->properties_count === 1)
                                                {{ __('1 Property') }}
                                            @else
                                                {{ __(':count Properties', ['count' => $agency->properties_count]) }}
                                            @endif
                                        </span>
                                    @endif
                                </div>
                                
                                @if ($agency->phone && ! setting('real_estate_hide_agency_phone', 0))
                                    <div class="agency-contact-small mt-1">
                                        <a href="tel:{{ $agency->phone }}" class="contact-link">
                                            <x-core::icon name="ti ti-phone" class="icon-small" />
                                            {{ $agency->phone }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        @if($agencies->count() >= 10)
            <div class="text-center mt-3">
                <a href="{{ route('public.agents') }}" class="tf-btn primary size-1">
                    {{ __('View All Agencies') }}
                </a>
            </div>
        @endif
    </div>
@endif
