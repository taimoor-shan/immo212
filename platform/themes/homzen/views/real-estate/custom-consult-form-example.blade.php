{{-- Example of how to create a custom consult form with additional fields --}}

<div class="modal fade" id="contactHostModal" tabindex="-1" aria-labelledby="contactHostModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="contactHostModalLabel">{{ __('Contact About Property') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
            </div>
            <div class="modal-body">
                {{-- Enhanced form with additional fields --}}
                {!! 
                    \Botble\RealEstate\Forms\Fronts\ConsultForm::create()
                        ->formClass('contact-form enhanced-consult-form')
                        ->setFormInputWrapperClass('ip-group mb-3')
                        
                        {{-- Modify existing fields --}}
                        ->modify('content', 'textarea', [
                            'attr' => [
                                'class' => 'form-control',
                                'rows' => '4',
                                'placeholder' => 'I am interested in this property. Please provide more details about...'
                            ]
                        ])
                        ->modify('submit', 'submit', [
                            'attr' => ['class' => 'tf-btn primary w-100']
                        ])
                        
                        {{-- Add property type --}}
                        ->add('type', 'hidden', ['attr' => ['value' => 'property']])
                        ->add('data_id', 'hidden', ['attr' => ['value' => $property->getKey()]])
                        
                        {{-- Add property name as read-only --}}
                        ->addBefore('content', 'data_name', 'text', [
                            'label' => __('Property'),
                            'attr' => ['value' => $property->name, 'readonly' => true, 'class' => 'form-control'],
                        ])
                        
                        {{-- Add preferred contact method --}}
                        ->addAfter('email', 'preferred_contact', 'select', [
                            'label' => __('Preferred Contact Method'),
                            'attr' => ['class' => 'form-control'],
                            'choices' => [
                                '' => __('Select preferred method'),
                                'email' => __('Email'),
                                'phone' => __('Phone Call'),
                                'whatsapp' => __('WhatsApp'),
                                'both' => __('Both Email & Phone'),
                            ]
                        ])
                        
                        {{-- Add budget range --}}
                        ->addAfter('preferred_contact', 'budget_range', 'select', [
                            'label' => __('Budget Range'),
                            'attr' => ['class' => 'form-control'],
                            'choices' => [
                                '' => __('Select your budget range'),
                                '0-100000' => __('Under $100,000'),
                                '100000-250000' => __('$100,000 - $250,000'),
                                '250000-500000' => __('$250,000 - $500,000'),
                                '500000-750000' => __('$500,000 - $750,000'),
                                '750000-1000000' => __('$750,000 - $1,000,000'),
                                '1000000+' => __('Over $1,000,000'),
                            ]
                        ])
                        
                        {{-- Add viewing preference --}}
                        ->addAfter('budget_range', 'viewing_preference', 'select', [
                            'label' => __('When would you like to view?'),
                            'attr' => ['class' => 'form-control'],
                            'choices' => [
                                '' => __('Select viewing preference'),
                                'asap' => __('As soon as possible'),
                                'this_week' => __('This week'),
                                'next_week' => __('Next week'),
                                'flexible' => __('I am flexible'),
                            ]
                        ])
                        
                        {{-- Add financing status --}}
                        ->addAfter('viewing_preference', 'financing_status', 'select', [
                            'label' => __('Financing Status'),
                            'attr' => ['class' => 'form-control'],
                            'choices' => [
                                '' => __('Select financing status'),
                                'cash_buyer' => __('Cash Buyer'),
                                'pre_approved' => __('Pre-approved for mortgage'),
                                'need_financing' => __('Need financing assistance'),
                                'other' => __('Other'),
                            ]
                        ])
                        
                        {{-- Add additional requirements --}}
                        ->addAfter('financing_status', 'additional_requirements', 'textarea', [
                            'label' => __('Additional Requirements/Questions'),
                            'attr' => [
                                'class' => 'form-control',
                                'rows' => '3',
                                'placeholder' => 'Any specific requirements or questions about the property?'
                            ],
                            'required' => false
                        ])
                        
                        {{-- Add privacy consent --}}
                        ->addAfter('additional_requirements', 'privacy_consent', 'onOffCheckbox', [
                            'label' => __('I consent to the processing of my personal data for this inquiry'),
                            'required' => true,
                            'attr' => ['class' => 'form-check-input']
                        ])
                        
                        ->renderForm() 
                !!}
            </div>
        </div>
    </div>
</div>

<style>
.enhanced-consult-form {
    max-width: 100%;
}

.enhanced-consult-form .ip-group {
    margin-bottom: 1rem;
}

.enhanced-consult-form label {
    font-weight: 600;
    color: #333;
    margin-bottom: 0.5rem;
    display: block;
}

.enhanced-consult-form .form-control {
    border: 1px solid #ddd;
    border-radius: 6px;
    padding: 0.75rem;
    font-size: 14px;
    transition: border-color 0.3s ease;
}

.enhanced-consult-form .form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    outline: 0;
}

.enhanced-consult-form .form-check-input {
    margin-right: 0.5rem;
}

.enhanced-consult-form .tf-btn {
    font-weight: 600;
    padding: 0.75rem 1.5rem;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.enhanced-consult-form .tf-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

@media (max-width: 768px) {
    .modal-dialog {
        margin: 1rem 0.5rem;
        max-width: calc(100% - 1rem);
    }
    
    .enhanced-consult-form .form-control {
        font-size: 16px; /* Prevents zoom on mobile */
    }
}
</style>
