 <div @class(['widget-box single-property-contact', $class ?? null])>
     {{-- <div class="h7 title fw-6">{{ __('Contact Agency') }}</div> --}}
     @if (!RealEstateHelper::hideAgentInfoInPropertyDetailPage() && ($account = $property->author))
          <div class="box-avatar d-flex flex-column align-items-center gap-3">
            {{-- Avatar and Host Info --}}
            <div class="avatar avt-100 round">
                <a href="{{ $account->url }}" class="d-block">
                    {{ RvMedia::image($account->avatar?->url ?: $account->avatar_url, $account->name) }}
                </a>
            </div>
            <div class="host-details">
                <h5 class=" mb-4 name text-center">
                    {{-- Host Name --}}
                    <a href="{{ $account->url }}">{{ $account->name }}</a>
                </h5>
                <div class="contact d-flex flex-wrap gap-2 align-items-center justify-content-center">
                    {{-- Phone Contact --}}
                    @php $showPhone = $account->phone && !setting('real_estate_hide_agency_phone', false); @endphp
                    @if ($showPhone)
                        <div class="contact-item">
                            <a href="tel:{{ $account->phone }}" class="contact-btn phone-link"
                                data-phone="{{ $account->phone }}">
                                {{ __('Contact by phone') }}
                            </a>
                        </div>
                    @elseif($hotline = theme_option('hotline'))
                        <div class="contact-item">
                            <a href="tel:{{ $hotline }}" class="contact-btn phone-link"
                                data-phone="{{ $hotline }}">
                                {{ __('Contact by phone') }}
                            </a>
                        </div>
                    @endif

                    {{-- Email Contact --}}
                    @php $showEmail = $account->email && !setting('real_estate_hide_agency_email', false); @endphp
                    @if ($showEmail)
                        <div class="contact-item">
                            <a href="#" class="contact-btn outline message-btn" data-bs-toggle="modal"
                                data-bs-target="#contactAgentModal">
                                {{ __('Contact by email') }}
                            </a>
                        </div>
                    @endif
                </div>

            </div>
        </div>
     @endif

     {!! apply_filters('property_right_details_info', null, $property) !!}

     {!! apply_filters('before_consult_form', null, $property) !!}


     <div class="modal fade" id="contactAgentModal" tabindex="-1" aria-labelledby="contactAgentModal"
         aria-hidden="true">
         <div class="modal-dialog">
             <div class="modal-content px-4 px-lg-5 py-4">
                 <div class="modal-header">
                     <h5 class="modal-title" id="contactAgentModalLabel">{{ __('Contact Agent') }}</h5>
                     <button type="button" class="btn-close" data-bs-dismiss="modal"
                         aria-label="{{ __('Close') }}"></button>
                 </div>
                 <div class="modal-body">
                     {!! \Botble\RealEstate\Forms\Fronts\ConsultForm::create()->formClass('contact-form')->setFormInputWrapperClass('ip-group')->modify('content', 'textarea', ['attr' => ['class' => 'form-control']])->modify('submit', 'submit', ['attr' => ['class' => 'tf-btn primary w-100']])->add('type', 'hidden', ['attr' => ['value' => 'property']])->add('data_id', 'hidden', ['attr' => ['value' => $property->getKey()]])->addBefore('content', 'data_name', 'text', [
                             'label' => false,
                             'attr' => ['value' => $property->name, 'disabled' => true],
                         ])->renderForm() !!}
                 </div>
             </div>
         </div>
     </div>




     {!! apply_filters('after_consult_form', null, $property) !!}
 </div>

 <script>
     document.addEventListener("DOMContentLoaded", function() {
         var links = document.querySelectorAll(".phone-link");

         links.forEach(function(link) {
             var phone = link.getAttribute("data-phone");

             link.addEventListener("click", function(e) {
                 e.preventDefault();
                 link.textContent = phone;
                 link.setAttribute("href", "tel:" + phone);
             });
         });
     });
 </script>
