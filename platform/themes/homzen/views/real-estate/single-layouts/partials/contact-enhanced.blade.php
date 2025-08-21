 <div @class(['widget-box single-property-contact', $class ?? null])>
     {{-- <div class="h7 title fw-6">{{ __('Contact Agency') }}</div> --}}
     @if (!RealEstateHelper::hideAgentInfoInPropertyDetailPage() && ($account = $property->author))
         <div class="box-avatar">
             <div class="avatar avt-100 round">
                 <a href="{{ $account->url }}" class="d-block">
                     {{ RvMedia::image($account->avatar?->url ?: $account->avatar_url, $account->name) }}
                 </a>
             </div>
             <div class="info line-clamp-1">
                 <div class="text-1 name">
                     <a href="{{ $account->url }}">{{ $account->name }}</a>
                 </div>
                 @if ($account->phone && !setting('real_estate_hide_agency_phone', false))
                     <a href="tel:{{ $account->phone }}" class="info-item phone-link" data-phone="{{ $account->phone }}">
                         {{ __('Contact by phone') }}

                     </a>
                 @elseif($hotline = theme_option('hotline'))
                     <a href="tel:{{ $hotline }}" class="info-item phone-link" data-phone="{{ $hotline }}">
                         {{ __('Contact by phone') }}
                     </a>
                 @endif

                 @if ($account->email && !setting('real_estate_hide_agency_email', false))
                     <a href="#" class="contact-btn message-btn" data-bs-toggle="modal" data-bs-target="#contactHostModal">  {{ __('Contact by email') }}
</a>
                 @endif
             </div>
         </div>
     @endif

     {!! apply_filters('property_right_details_info', null, $property) !!}

     {!! apply_filters('before_consult_form', null, $property) !!}


     <div class="modal fade" id="contactHostModal" tabindex="-1" aria-labelledby="contactHostModalLabel"
         aria-hidden="true">
         <div class="modal-dialog modal-lg">
             <div class="modal-content">
                 <div class="modal-header">
                     <h5 class="modal-title" id="contactHostModalLabel">{{ __('Contact About This Property') }}</h5>
                     <button type="button" class="btn-close" data-bs-dismiss="modal"
                         aria-label="{{ __('Close') }}"></button>
                 </div>
                 <div class="modal-body">
                     {{-- Enhanced Consult Form --}}
                     {!! 
                         \Botble\RealEstate\Forms\Fronts\ConsultForm::create()
                             ->formClass('contact-form enhanced-consult-form')
                             ->setFormInputWrapperClass('form-group mb-3')
                             
                             {{-- Add property specific fields --}}
                             ->add('type', 'hidden', ['attr' => ['value' => 'property']])
                             ->add('data_id', 'hidden', ['attr' => ['value' => $property->getKey()]])
                             
                             {{-- Add property name as readonly field --}}
                             ->addBefore('content', 'property_name', 'text', [
                                 'label' => __('Property of Interest'),
                                 'attr' => [
                                     'value' => $property->name, 
                                     'readonly' => true,
                                     'class' => 'form-control'
                                 ],
                             ])
                             
                             {{-- Modify name field --}}
                             ->modify('name', 'text', [
                                 'label' => __('Full Name'),
                                 'attr' => [
                                     'class' => 'form-control',
                                     'placeholder' => __('Enter your full name')
                                 ],
                                 'required' => true
                             ])
                             
                             {{-- Modify phone field --}}
                             ->modify('phone', 'text', [
                                 'label' => __('Phone Number'),
                                 'attr' => [
                                     'class' => 'form-control',
                                     'placeholder' => __('Your phone number')
                                 ]
                             ])
                             
                             {{-- Modify email field --}}
                             ->modify('email', 'email', [
                                 'label' => __('Email Address'),
                                 'attr' => [
                                     'class' => 'form-control',
                                     'placeholder' => __('your.email@example.com')
                                 ]
                             ])
                             
                             {{-- Add preferred contact method --}}
                             ->addAfter('email', 'preferred_contact_method', 'select', [
                                 'label' => __('Preferred Contact Method'),
                                 'attr' => ['class' => 'form-control'],
                                 'choices' => [
                                     '' => __('How would you like to be contacted?'),
                                     'email' => __('Email'),
                                     'phone' => __('Phone Call'),
                                     'whatsapp' => __('WhatsApp'),
                                     'both' => __('Both Email & Phone'),
                                 ],
                                 'required' => false
                             ])
                             
                             {{-- Add budget range --}}
                             ->addAfter('preferred_contact_method', 'budget_range', 'select', [
                                 'label' => __('Budget Range'),
                                 'attr' => ['class' => 'form-control'],
                                 'choices' => [
                                     '' => __('Select your budget range (optional)'),
                                     '0-100000' => __('Under $100,000'),
                                     '100000-250000' => __('$100,000 - $250,000'),
                                     '250000-500000' => __('$250,000 - $500,000'),
                                     '500000-750000' => __('$500,000 - $750,000'),
                                     '750000-1000000' => __('$750,000 - $1,000,000'),
                                     '1000000+' => __('Over $1,000,000'),
                                 ],
                                 'required' => false
                             ])
                             
                             {{-- Add viewing timeframe --}}
                             ->addAfter('budget_range', 'viewing_timeframe', 'select', [
                                 'label' => __('When would you like to view?'),
                                 'attr' => ['class' => 'form-control'],
                                 'choices' => [
                                     '' => __('Select viewing timeframe'),
                                     'asap' => __('As soon as possible'),
                                     'this_week' => __('This week'),
                                     'next_week' => __('Next week'),
                                     'within_month' => __('Within a month'),
                                     'flexible' => __('I am flexible'),
                                 ],
                                 'required' => false
                             ])
                             
                             {{-- Add financing status --}}
                             ->addAfter('viewing_timeframe', 'financing_status', 'select', [
                                 'label' => __('Financing Status'),
                                 'attr' => ['class' => 'form-control'],
                                 'choices' => [
                                     '' => __('Select your financing status'),
                                     'cash_buyer' => __('Cash Buyer'),
                                     'pre_approved' => __('Pre-approved for mortgage'),
                                     'need_financing' => __('Need financing assistance'),
                                     'first_time_buyer' => __('First-time buyer'),
                                     'other' => __('Other'),
                                 ],
                                 'required' => false
                             ])
                             
                             {{-- Modify content/message field --}}
                             ->modify('content', 'textarea', [
                                 'label' => __('Message'),
                                 'attr' => [
                                     'class' => 'form-control',
                                     'rows' => '5',
                                     'placeholder' => __('I am interested in this property. Please provide more information about pricing, availability, viewing appointments, and any additional details...')
                                 ],
                                 'required' => true
                             ])
                             
                             {{-- Add additional questions --}}
                             ->addAfter('content', 'additional_questions', 'textarea', [
                                 'label' => __('Additional Questions or Requirements'),
                                 'attr' => [
                                     'class' => 'form-control',
                                     'rows' => '3',
                                     'placeholder' => __('Any specific questions about the neighborhood, amenities, or property features?')
                                 ],
                                 'required' => false
                             ])
                             
                             {{-- Modify submit button --}}
                             ->modify('submit', 'submit', [
                                 'attr' => ['class' => 'tf-btn primary w-100 mt-3'],
                                 'label' => __('Send Inquiry')
                             ])
                             
                             ->renderForm() 
                     !!}
                     
                     {{-- Property Summary Card --}}
                     <div class="property-summary-card mt-4 p-3 bg-light rounded">
                         <h6>{{ __('Property Summary') }}</h6>
                         <div class="row">
                             <div class="col-6">
                                 <small class="text-muted">{{ __('Price') }}</small><br>
                                 <strong>{{ $property->price_html }}</strong>
                             </div>
                             <div class="col-6">
                                 <small class="text-muted">{{ __('Type') }}</small><br>
                                 <strong>{{ $property->category->name ?? __('Property') }}</strong>
                             </div>
                             @if($property->number_bedroom || $property->number_bathroom || $property->square)
                                 <div class="col-12 mt-2">
                                     <small class="text-muted">{{ __('Details') }}</small><br>
                                     @if($property->number_bedroom)
                                         <span class="badge bg-secondary me-1">{{ $property->number_bedroom }} {{ __('beds') }}</span>
                                     @endif
                                     @if($property->number_bathroom)
                                         <span class="badge bg-secondary me-1">{{ $property->number_bathroom }} {{ __('baths') }}</span>
                                     @endif
                                     @if($property->square)
                                         <span class="badge bg-secondary">{{ $property->square_text }}</span>
                                     @endif
                                 </div>
                             @endif
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </div>

     {!! apply_filters('after_consult_form', null, $property) !!}
 </div>

{{-- Enhanced Styling --}}
<style>
.enhanced-consult-form {
    max-width: 100%;
}

.enhanced-consult-form .form-group {
    margin-bottom: 1.5rem;
}

.enhanced-consult-form label {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.5rem;
    display: block;
    font-size: 14px;
}

.enhanced-consult-form .form-control {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    font-size: 14px;
    transition: all 0.3s ease;
    background-color: #fff;
}

.enhanced-consult-form .form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
    outline: 0;
}

.enhanced-consult-form .form-control[readonly] {
    background-color: #f8f9fa;
    border-color: #dee2e6;
    color: #6c757d;
}

.enhanced-consult-form select.form-control {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m1 6 7 7 7-7'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 16px 12px;
    appearance: none;
}

.enhanced-consult-form textarea.form-control {
    resize: vertical;
    min-height: 100px;
}

.enhanced-consult-form .tf-btn {
    font-weight: 600;
    padding: 1rem 2rem;
    border-radius: 8px;
    transition: all 0.3s ease;
    font-size: 16px;
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border: none;
}

.enhanced-consult-form .tf-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 15px rgba(0, 123, 255, 0.3);
}

.property-summary-card {
    border: 1px solid #dee2e6;
    background: linear-gradient(145deg, #f8f9fa 0%, #e9ecef 100%);
}

.property-summary-card h6 {
    color: #495057;
    margin-bottom: 1rem;
    font-weight: 600;
}

.property-summary-card .badge {
    font-size: 12px;
    padding: 0.35em 0.65em;
}

@media (max-width: 768px) {
    .modal-dialog {
        margin: 1rem 0.5rem;
        max-width: calc(100% - 1rem);
    }
    
    .enhanced-consult-form .form-control {
        font-size: 16px; /* Prevents zoom on iOS */
    }
    
    .enhanced-consult-form .tf-btn {
        font-size: 18px;
        padding: 1.2rem 2rem;
    }
    
    .property-summary-card .col-6 {
        margin-bottom: 1rem;
    }
}

/* Loading state */
.enhanced-consult-form .btn-loading {
    position: relative;
    color: transparent;
}

.enhanced-consult-form .btn-loading::after {
    content: '';
    position: absolute;
    width: 20px;
    height: 20px;
    top: 50%;
    left: 50%;
    margin-left: -10px;
    margin-top: -10px;
    border: 2px solid #ffffff;
    border-radius: 50%;
    border-top-color: transparent;
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
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
         
         // Enhanced form validation
         const form = document.querySelector('.enhanced-consult-form');
         if (form) {
             form.addEventListener('submit', function(e) {
                 const submitBtn = form.querySelector('button[type="submit"]');
                 if (submitBtn) {
                     submitBtn.classList.add('btn-loading');
                     submitBtn.disabled = true;
                 }
             });
         }
     });
 </script>
