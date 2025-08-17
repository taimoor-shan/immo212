@if ($vacationRental->author)
    <div @class(['widget-box single-vacation-rental-contact', $class ?? null])>
        <div class="h7 title fw-6">{{ __('Contact Host') }}</div>
        
        <div class="host-info">
            <div class="host-avatar">
                @if ($vacationRental->author->avatar)
                    {{ RvMedia::image($vacationRental->author->avatar, $vacationRental->author->name, 'thumb') }}
                @else
                    <div class="avatar-placeholder">
                        <x-core::icon name="ti ti-user" />
                    </div>
                @endif
            </div>
            
            <div class="host-details">
                <div class="host-name">{{ $vacationRental->author->name }}</div>
                @if ($vacationRental->author->description)
                    <div class="host-description">{{ Str::limit($vacationRental->author->description, 100) }}</div>
                @endif
                
                <div class="host-stats">
                    @if ($vacationRental->author->vacationRentals)
                        <div class="stat-item">
                            <x-core::icon name="ti ti-building" />
                            <span>{{ $vacationRental->author->vacationRentals->count() }} {{ __('properties') }}</span>
                        </div>
                    @endif
                    @if ($vacationRental->author->created_at)
                        <div class="stat-item">
                            <x-core::icon name="ti ti-calendar" />
                            <span>{{ __('Joined') }} {{ $vacationRental->author->created_at->format('M Y') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="contact-actions">
            @if ($vacationRental->author->phone)
                <a href="tel:{{ $vacationRental->author->phone }}" class="contact-btn phone-btn">
                    <x-core::icon name="ti ti-phone" />
                    <span>{{ __('Call') }}</span>
                </a>
            @endif
            
            @if ($vacationRental->author->email)
                <a href="mailto:{{ $vacationRental->author->email }}" class="contact-btn email-btn">
                    <x-core::icon name="ti ti-mail" />
                    <span>{{ __('Email') }}</span>
                </a>
            @endif
            
            <button type="button" class="contact-btn message-btn" data-bs-toggle="modal" data-bs-target="#contactHostModal">
                <x-core::icon name="ti ti-message" />
                <span>{{ __('Message') }}</span>
            </button>
        </div>
        
        @if ($vacationRental->author->phone)
            <div class="phone-display">
                <x-core::icon name="ti ti-phone" />
                <span>{{ $vacationRental->author->phone }}</span>
            </div>
        @endif
    </div>
    
    <!-- Contact Host Modal -->
    <div class="modal fade" id="contactHostModal" tabindex="-1" aria-labelledby="contactHostModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="contactHostModalLabel">{{ __('Contact Host') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <div class="modal-body">
                    <form id="contactHostForm" action="{{ route('public.vacation-rental.inquiry') }}" method="POST">
                        @csrf
                        <input type="hidden" name="vacation_rental_id" value="{{ $vacationRental->id }}">
                        
                        <div class="mb-3">
                            <label for="contact_name" class="form-label">{{ __('Your Name') }}</label>
                            <input type="text" class="form-control" id="contact_name" name="name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="contact_email" class="form-label">{{ __('Your Email') }}</label>
                            <input type="email" class="form-control" id="contact_email" name="email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="contact_phone" class="form-label">{{ __('Your Phone') }}</label>
                            <input type="tel" class="form-control" id="contact_phone" name="phone">
                        </div>
                        
                        <div class="mb-3">
                            <label for="contact_message" class="form-label">{{ __('Message') }}</label>
                            <textarea class="form-control" id="contact_message" name="message" rows="4" required placeholder="{{ __('I am interested in this vacation rental...') }}"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" form="contactHostForm" class="btn btn-primary">{{ __('Send Message') }}</button>
                </div>
            </div>
        </div>
    </div>
@endif

<style>
.single-vacation-rental-contact {
    padding: 24px;
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    margin-bottom: 20px;
}

.host-info {
    display: flex;
    gap: 16px;
    margin: 20px 0;
}

.host-avatar {
    flex-shrink: 0;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    overflow: hidden;
}

.host-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-placeholder {
    width: 100%;
    height: 100%;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    font-size: 24px;
}

.host-details {
    flex: 1;
    min-width: 0;
}

.host-name {
    font-size: 18px;
    font-weight: 600;
    color: #212529;
    margin-bottom: 4px;
}

.host-description {
    font-size: 14px;
    color: #6c757d;
    margin-bottom: 8px;
    line-height: 1.4;
}

.host-stats {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

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

.contact-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
    gap: 8px;
    margin: 20px 0;
}

.contact-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    padding: 12px 8px;
    border: 1px solid #e9ecef;
    border-radius: 6px;
    background: white;
    color: #6c757d;
    text-decoration: none;
    font-size: 12px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.contact-btn:hover {
    background: #f8f9fa;
    border-color: #007bff;
    color: #007bff;
}

.contact-btn x-core\\:icon {
    font-size: 18px;
}

.phone-btn:hover {
    background: #e8f5e8;
    border-color: #28a745;
    color: #28a745;
}

.email-btn:hover {
    background: #fff3cd;
    border-color: #ffc107;
    color: #856404;
}

.message-btn:hover {
    background: #e7f3ff;
    border-color: #007bff;
    color: #007bff;
}

.phone-display {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px;
    background: #f8f9fa;
    border-radius: 6px;
    font-weight: 500;
    color: #212529;
}

.phone-display x-core\\:icon {
    color: #28a745;
    font-size: 16px;
}

@media (max-width: 768px) {
    .single-vacation-rental-contact {
        padding: 16px;
    }
    
    .host-info {
        gap: 12px;
    }
    
    .host-avatar {
        width: 50px;
        height: 50px;
    }
    
    .contact-actions {
        grid-template-columns: repeat(3, 1fr);
    }
}
</style>
