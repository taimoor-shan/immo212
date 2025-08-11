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
    <div class="widget-boxy">
        
        <div class="agencies-sidebar-list">
            @foreach($agencies as $agency)
                <div class="agency-card-sidebar">
                            
                            <div class="agency-info leftSec">
                                <div class="agency-name">
                                    @if (\Botble\RealEstate\Facades\RealEstateHelper::isDisabledPublicProfile())
                                        <h6 class="mb-1 textMuted">{{ $agency->name }}</h6>
                                    @else
                                        <a href="{{ $agency->url }}">
                                   <h6 class="link mb-1 textMuted">{{ $agency->name }}</h6>
                                        </a>
                                    @endif
                                </div>
                                
                                @if($agency->company)
                                    <p class="agency-company text-variant-1 mb-1">{{ $agency->company }}</p>
                                @endif
                                
                                <div class="something">
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
                            <div class="agency-rightSec">
                                <button class="sponsored-button" style="--agency-name: '{{ addslashes($agency->name) }}';">
                                <svg width="18" height="18" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" fill="currentColor"><path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 5v2h2V7h-2zm2 9c0 .55-.45 1-1 1s-1-.45-1-1v-4c0-.55.45-1 1-1s1 .45 1 1v4zm-9-4c0 4.41 3.59 8 8 8s8-3.59 8-8-3.59-8-8-8-8 3.59-8 8z"></path></svg>
                                 <span>Sponsored</span>
                                </button>
                                <div class="agency-avatar">
                                @if (\Botble\RealEstate\Facades\RealEstateHelper::isDisabledPublicProfile())
                                    {{ RvMedia::image($agency->avatar_url, $agency->name, 'thumb') }}
                                @else
                                    <a href="{{ $agency->url }}">
                                        {{ RvMedia::image($agency->avatar_url, $agency->name, 'thumb') }}
                                    </a>
                                @endif
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
