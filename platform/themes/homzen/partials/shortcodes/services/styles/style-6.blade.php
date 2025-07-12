<section class="flat-section-v3 flat-service-v6" @style(["background-color: $shortcode->background_color" => $shortcode->background_color])>
    <div class="container">
        {!! Theme::partial('shortcode-heading', compact('shortcode')) !!}

        @if(count($services) >= 3)
            <div class="services-bento-grid wow fadeInUpSmall" data-wow-delay=".4s" data-wow-duration="2000ms">
                @php
                    $servicesArray = collect($services)->take(3)->values();
                @endphp

                {{-- Service 1: Content in cta1 area, image in image1 area --}}
                @if(isset($servicesArray[0]))
                    @php $service = $servicesArray[0]; @endphp
                    <div class="bento-service-item content-area cta1">
                        <div class="content">
                            <h6 class="title">{!! BaseHelper::clean($service['title']) !!}</h6>
                            <!-- @if($service['description'])
                                <p class="description">{!! BaseHelper::clean(nl2br($service['description'])) !!}</p>
                            @endif -->
                            @if ($service['button_url'] && $service['button_label'])
                                <a href="{{ $service['button_url'] }}" class="tf-btn primary size-3">
                                    <span class="text">{{ $service['button_label'] }}</span>
                                    
                                </a>
                            @endif
                        </div>
                    </div>
                    @if($service['icon_image'])
                        <div class="bento-service-item image-area image1" style="background: url('{{ RvMedia::getImageUrl($service['icon_image']) }}') center/cover no-repeat">
                            
                        </div>
                    @endif
                @endif

                {{-- Background Image in image2 area (tall area) --}}
                @if($shortcode->background_image)
                    <div class="bento-service-item background-area image2" style="background: url('{{ RvMedia::getImageUrl($shortcode->background_image) }}') center/cover no-repeat"
                         >
                        
                    </div>
                @endif

                {{-- Service 2: Image in image3 area, content in cta3 area --}}
                @if(isset($servicesArray[1]))
                    @php $service = $servicesArray[1]; @endphp
                    @if($service['icon_image'])
                        <div class="bento-service-item image-area image3" style="background: url('{{ RvMedia::getImageUrl($service['icon_image']) }}') center/cover no-repeat">
                            
                        </div>
                    @endif
                    <div class="bento-service-item content-area cta3">
                        <div class="content">
                            <h6 class="title">{!! BaseHelper::clean($service['title']) !!}</h6>
                            <!-- @if($service['description'])
                                <p class="description">{!! BaseHelper::clean(nl2br($service['description'])) !!}</p>
                            @endif -->
                            @if ($service['button_url'] && $service['button_label'])
                                <a href="{{ $service['button_url'] }}" class="tf-btn primary size-3">
                                    <span class="text">{{ $service['button_label'] }}</span>
                                    
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Service 3: Content in cta2 area, image in image4 area --}}
                @if(isset($servicesArray[2]))
                    @php $service = $servicesArray[2]; @endphp
                    <div class="bento-service-item content-area cta2">
                       
                        <div class="content">
                            <h6 class="title">{!! BaseHelper::clean($service['title']) !!}</h6>
                            <!-- @if($service['description'])
                                <p class="description">{!! BaseHelper::clean(nl2br($service['description'])) !!}</p>
                            @endif -->
                            @if ($service['button_url'] && $service['button_label'])
                                <a href="{{ $service['button_url'] }}" class="tf-btn primary size-3">
                                    <span class="text">{{ $service['button_label'] }}</span>
                                    
                                </a>
                            @endif
                        </div>
                    </div>
                    @if($service['icon_image'])
                        <div class="bento-service-item image-area image4" style="background: url('{{ RvMedia::getImageUrl($service['icon_image']) }}') center/cover no-repeat">
                            
                        </div>
                    @endif
                @endif
            </div>
        @else
            <div class="alert alert-warning text-center">
                <p>{{ __('Please ensure you have at least 3 services with images to display the bento grid layout. Also add a background image for the best visual effect.') }}</p>
            </div>
        @endif
    </div>
</section>
