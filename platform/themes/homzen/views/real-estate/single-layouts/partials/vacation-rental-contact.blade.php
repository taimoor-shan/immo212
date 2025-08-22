<div @class(['widget-box single-vacation-rental-contact', $class ?? null])>
    {{-- <div class="h7 title fw-6">{{ __('Contact Host') }}</div> --}}
    @if (!RealEstateHelper::hideAgentInfoInPropertyDetailPage() && ($account = $vacationRental->author))
        <div class="box-avatar d-flex flex-column align-items-center gap-3">
            {{-- Avatar and Host Info --}}
            <div class="avatar avt-100 round">
                <a href="{{ $account->url }}" class="d-block">
                    {{ RvMedia::image($account->avatar?->url ?: $account->avatar_url, $account->name) }}
                </a>
            </div>
            <div class="host-details">
                <h5 class="mb-4 name text-center">
                    {{-- Host Name --}}
                    <a href="{{ $account->url }}">{{ $account->name }}</a>
                </h5>
                <div class="contact align-items-center justify-content-center d-flex flex-wrap gap-2">
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
                                data-bs-target="#vacationRentalContactModal">
                                {{ __('Contact by email') }}
                            </a>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    @endif

    {!! apply_filters('vacation_rental_right_details_info', null, $vacationRental) !!}
    {!! apply_filters('before_vacation_rental_consult_form', null, $vacationRental) !!}

    <!-- Vacation Rental Contact Modal -->
    <div class="modal fade" id="vacationRentalContactModal" tabindex="-1"
        aria-labelledby="vacationRentalContactModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content px-4 px-lg-5 py-4">
                <div class="modal-header">
                    <h5 class="modal-title" id="vacationRentalContactModalLabel">{{ __('Contact Host') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="{{ __('Close') }}"></button>
                </div>
                <div class="modal-body">
                    {{-- Use standard contact-form class for existing JS handling --}}
                    {{-- <form class="contact-form" action="{{ route('public.send.consult') }}" method="POST">
                        @csrf


                        <input type="hidden" name="type" value="vacation_rental">
                        <input type="hidden" name="data_id" value="{{ $vacationRental->getKey() }}">
                        <input type="hidden" class="form-control" value="{{ $vacationRental->name }}">


                        <div class="row">
                            <div class="col-md-6 ip-group">
                                <label for="vr_name" class="form-label">{{ __('Name') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="vr_name" name="name" required
                                    placeholder="{{ __('Johny Dane') }}">
                            </div>
                            <div class="col-md-6 ip-group">
                                <label for="vr_email" class="form-label">{{ __('Email') }} <span
                                        class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="vr_email" name="email" required
                                    placeholder="{{ __('email@example.com') }}">
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-6 ip-group">
                                <label for="vr_phone" class="form-label">{{ __('Phone') }}</label>
                                <input type="tel" class="form-control" id="vr_phone" name="phone"
                                    placeholder="{{ __('Ex 0123456789') }}">
                            </div>
                            <div class="col-md-6 ip-group">
                                <label for="vr_contact_method" class="form-label">{{ __('Preferred Contact') }}</label>
                                <select class="fselect_js nice-select" id="vr_contact_method"
                                    name="consult_custom_fields[preferred_contact_method]">

                                    <option value="">{{ __('Select method') }}</option>
                                    <option value="email">{{ __('Email') }}</option>
                                    <option value="phone">{{ __('Phone') }}</option>
                                    <option value="whatsapp">{{ __('WhatsApp') }}</option>
                                    <option value="both">{{ __('Both') }}</option>
                                </select>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-4 ip-group">
                                <label for="vr_checkin" class="form-label">{{ __('Check-in') }}</label>
                                <input type="date" class="form-control" id="vr_checkin"
                                    name="consult_custom_fields[checkin_date]">
                            </div>
                            <div class="col-md-4 ip-group">
                                <label for="vr_checkout" class="form-label">{{ __('Check-out') }}</label>
                                <input type="date" class="form-control" id="vr_checkout"
                                    name="consult_custom_fields[checkout_date]">
                            </div>
                            <div class="col-md-4 ip-group">
                                <label for="vr_guests_count" class="form-label">{{ __('Guests') }}</label>
                                <select class="select_js nice-select" id="vr_guests_count"
                                    name="consult_custom_fields[number_of_guests]">
                                    <option value="">{{ __('How many?') }}</option>
                                    @for ($i = 1; $i <= ($vacationRental->maximum_guests ?? 10); $i++)
                                        <option value="{{ $i }}">{{ $i }}
                                            {{ $i == 1 ? __('Guest') : __('Guests') }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-6 ip-group">
                                <label for="vr_purpose" class="form-label">{{ __('Purpose of Stay') }}</label>
                                <select class="select_js nice-select" id="vr_purpose"
                                    name="consult_custom_fields[stay_purpose]">
                                    <option value="">{{ __('Select purpose') }}</option>
                                    <option value="vacation">{{ __('Vacation') }}</option>
                                    <option value="business">{{ __('Business') }}</option>
                                    <option value="family">{{ __('Family Visit') }}</option>
                                    <option value="event">{{ __('Special Event') }}</option>
                                    <option value="relocation">{{ __('Extended Stay') }}</option>
                                    <option value="other">{{ __('Other') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6 ip-group">
                                <label for="vr_group" class="form-label">{{ __('Group Type') }}</label>
                                <select class="select_js nice-select" id="vr_group" name="consult_custom_fields[group_type]">
                                    <option value="">{{ __('Select type') }}</option>
                                    <option value="family">{{ __('Family') }}</option>
                                    <option value="couple">{{ __('Couple') }}</option>
                                    <option value="friends">{{ __('Friends') }}</option>
                                    <option value="business">{{ __('Business') }}</option>
                                    <option value="solo">{{ __('Solo') }}</option>
                                    <option value="other">{{ __('Other') }}</option>
                                </select>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-12 ip-group">
                                <label for="vr_content" class="form-label">{{ __('Message') }} <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" id="vr_content" name="content" rows="5" required
                                    placeholder="{{ __('Enter your message...') }}"></textarea>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-12 ip-group">
                                <button type="submit" class="tf-btn primary w-100">{{ __('Send Message') }}</button>
                            </div>
                        </div>
                    </form> --}}
                     {!! \Botble\RealEstate\Forms\Fronts\ConsultForm::create()->formClass('contact-form')->setFormInputWrapperClass('ip-group')->modify('content', 'textarea', ['attr' => ['class' => 'form-control']])->modify('submit', 'submit', ['attr' => ['class' => 'tf-btn primary w-100']])->add('type', 'hidden', ['attr' => ['value' => 'property']])->add('data_id', 'hidden', ['attr' => ['value' => $vacationRental->getKey()]])->addBefore('content', 'data_name', 'text', [
                             'label' => false,
                             'attr' => ['value' => $vacationRental->name, 'disabled' => true],
                         ])->renderForm() !!}
                </div>
            </div>
        </div>
    </div>

    {!! apply_filters('after_vacation_rental_consult_form', null, $vacationRental) !!}
</div>

<style>
    .stat-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: #6c757d;
    }

    .stat-item x-core\\:icon {
        font-size: 14px;
        color: #007bff;
    }






    /* Use existing theme form styling with minimal overrides */
    .contact-form .ip-group {
        margin-bottom: 1rem;
    }

    .contact-form .form-control[readonly] {
        background-color: #f8f9fa;
        color: #6c757d;
    }

    .property-summary {
        border: 1px solid #dee2e6;
        background: #f8f9fa;
    }

    .property-summary h6 {
        color: #495057;
        margin-bottom: 1rem;
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .single-vacation-rental-contact {
            padding: 16px;
        }

        .modal-dialog {
            margin: 1rem 0.5rem;
            max-width: calc(100% - 1rem);
        }
    }

    input#vr_checkout,nput#vr_checkin {
    padding: 9px 8px 9px 12px;
}
</style>

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
