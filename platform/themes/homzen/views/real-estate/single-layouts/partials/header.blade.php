<div class="header-property-detail pt-4">
    <div class="content-top row justify-content-between align-items-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="box-name">
                <!-- {!! BaseHelper::clean($property->status_html) !!} -->
                <h2 class="section-title">
                    {!! BaseHelper::clean($property->name) !!}
                </h2>
                @if ($property->short_address)
                    <p class="meta-item">
                        {{ $property->short_address }}
                    </p>
                @endif
                <ul class="d-flex align-items-center gap-2 text-dBlue">
                    @if ($property->number_bedroom)
                        <li class="meta-item">
                            {{ $property->number_bedroom }} Bedrooms
                        </li>
                    @endif
                    @if ($property->number_bathroom)
                        <li class="meta-item">
                            |
                        </li>
                    @endif
                    @if ($property->square)
                        <li class="meta-item">
                            {{ $property->square_text }}
                        </li>
                    @endif
                </ul>

            </div>
        </div>

        <div class="col-12 col-md-4 col-lg-6">
            <div class="box-price d-flex justify-content-between justify-content-md-end align-items-end mt-3 mt-md-0">
                <div class="lSec text-end">
                    <h4>{{ $property->price_html }}</h4>
                    <ul class="iconText d-flex justify-content-between justify-content-md-end gap-3">
                        @if (RealEstateHelper::isEnabledWishlist())
                            <li>
                                <button type="button" class="roundBtn" data-type="property"
                                    data-bb-toggle="add-to-wishlist" data-id="{{ $property->getKey() }}"
                                    data-add-message="{{ __('Added ":name" to wishlist successfully!', ['name' => $property->name]) }}"
                                    data-remove-message="{{ __('Removed ":name" from wishlist successfully!', ['name' => $property->name]) }}">
                                    <i class="fa-regular fa-heart"></i>

                                </button>
                            </li>
                        @endif
                        <li>
                            @include(Theme::getThemeNamespace('views.real-estate.partials.social-sharing'),
                                [
                                    'model' => $property,
                                ]
                            )
                        </li>
                    </ul>
                </div>
                 @if (!RealEstateHelper::hideAgentInfoInPropertyDetailPage() && ($account = $property->author))
                <div class="rSec d-flex flex-wrap justify-content-center align-items-center gap-2 d-block d-md-none">
                     @php $showPhone = $account->phone && !setting('real_estate_hide_agency_phone', false); @endphp
                    @if ($showPhone)
                        <div class="contact-item">
                            <a href="tel:{{ $account->phone }}" class="contact-btn phone-linko"
                                data-phone="{{ $account->phone }}">
                                {{ __('Contact Us') }}
                            </a>
                        </div>
                        @endif
                        @php $showEmail = $account->email && !setting('real_estate_hide_agency_email', false); @endphp
                    @if ($showEmail)
                        <div class="contact-item">
                            <a href="#" class="contact-btn outline message-btn" data-bs-toggle="modal"
                                data-bs-target="#contactAgentModal">
                                {{ __('Get in touch') }}
                            </a>
                        </div>
                    @endif

                </div>
                 @endif
            </div>
        </div>
    </div>
</div>


