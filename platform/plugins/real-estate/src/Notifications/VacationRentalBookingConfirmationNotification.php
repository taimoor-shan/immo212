<?php

namespace Botble\RealEstate\Notifications;

use Botble\Base\Facades\EmailHandler;
use Botble\RealEstate\Models\VacationRentalBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class VacationRentalBookingConfirmationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public VacationRentalBooking $booking)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $property = $this->booking->property;
        
        $emailHandler = EmailHandler::setModule(REAL_ESTATE_MODULE_SCREEN_NAME)
            ->setType('plugins')
            ->setTemplate('vacation-rental-booking-confirmation')
            ->addTemplateSettings(REAL_ESTATE_MODULE_SCREEN_NAME, config('plugins.real-estate.email', []))
            ->setVariableValue('customer_name', $this->booking->guest_name)
            ->setVariableValue('customer_email', $this->booking->guest_email)
            ->setVariableValue('booking_number', $this->booking->booking_number)
            ->setVariableValue('property_name', $property->name)
            ->setVariableValue('property_address', $property->location)
            ->setVariableValue('check_in_date', $this->booking->check_in_date->format('M d, Y'))
            ->setVariableValue('check_out_date', $this->booking->check_out_date->format('M d, Y'))
            ->setVariableValue('guests_count', $this->booking->guests_count)
            ->setVariableValue('total_amount', format_price($this->booking->total_amount))
            ->setVariableValue('booking_details_link', route('public.vacation-rental.booking.details', $this->booking->booking_number))
            ->setVariableValue('property_link', $property->url)
            ->setVariableValue('booking_status', ucfirst($this->booking->status))
            ->setVariableValue('special_requests', $this->booking->special_requests ?: 'None');

        return (new MailMessage())
            ->view(['html' => new HtmlString($emailHandler->getContent())])
            ->subject($emailHandler->getSubject());
    }
}
