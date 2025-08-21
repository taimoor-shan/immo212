@if ($vacationRental->images)
    <section class="flat-gallery-single">
        @foreach((array) $vacationRental->images as $image)
            @if($loop->first)
                <div class="item1 box-img">
                    {{ RvMedia::image($image, $vacationRental->name) }}
                    <div class="box-btn">
                        <a href="{{ RvMedia::getImageUrl($image) }}" data-fancybox="gallery" class="photoBtn"> {!! BaseHelper::renderIcon('ti ti-library-photo') !!}{{ __('Photos') }}</a>
                    </div>
                </div>
            @else
                <a href="{{ RvMedia::getImageUrl($image) }}" class="item-{{ $loop->iteration }} box-img" data-fancybox="gallery" @style(['display: none' => $loop->iteration > 4])>
                    {{ RvMedia::image($image, $vacationRental->name, lazy: false) }}
                </a>
            @endif
        @endforeach
    </section>
@endif
