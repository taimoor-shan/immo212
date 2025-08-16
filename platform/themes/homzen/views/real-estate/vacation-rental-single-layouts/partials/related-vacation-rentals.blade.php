@php
    $relatedVacationRentals = app(\Botble\RealEstate\Repositories\Interfaces\VacationRentalInterface::class)
        ->getRelatedVacationRentals(
            $vacationRental->id,
            theme_option('number_of_related_properties', 8)
        );
@endphp

@if ($relatedVacationRentals->isNotEmpty())
    <section class="flat-section pt-0 flat-latest-property">
        <div class="container">
            <div class="box-title">
                <h2 class="section-title mt-4">{{ __('Similar Vacation Rentals') }}</h2>
            </div>
            <div class="swiper tf-latest-property" data-preview-lg="4" data-preview-md="3" data-preview-sm="2" data-space="30" data-loop="true">
                <div class="swiper-wrapper">
                    @foreach($relatedVacationRentals as $rental)
                        <div class="swiper-slide">
                            @include(Theme::getThemeNamespace('views.real-estate.properties.item-vacation-rental'), ['vacationRental' => $rental, 'class' => 'style-2'])
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endif
