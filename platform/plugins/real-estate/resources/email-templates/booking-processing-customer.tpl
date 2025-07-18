{{ header }}

<div style="margin-bottom: 30px;">
    <h2 style="color: #2196f3; margin-bottom: 20px;">⏳ Processing Your Booking Request</h2>
    
    <p><strong>Dear {{ customer_name }},</strong></p>
    
    <p>Thank you for choosing us for your vacation rental needs! We have received your booking request and are currently processing it.</p>
</div>

<div style="background: #e3f2fd; padding: 20px; border-radius: 8px; margin-bottom: 30px; border-left: 4px solid #2196f3;">
    <h3 style="color: #1976d2; margin-top: 0;">📋 Your Booking Request</h3>
    
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 8px 0; font-weight: bold; width: 40%;">Reference:</td>
            <td style="padding: 8px 0;">{{ booking_reference }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-weight: bold;">Property:</td>
            <td style="padding: 8px 0;">{{ property_name }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-weight: bold;">Check-in:</td>
            <td style="padding: 8px 0;">{{ check_in_date }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-weight: bold;">Check-out:</td>
            <td style="padding: 8px 0;">{{ check_out_date }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-weight: bold;">Guests:</td>
            <td style="padding: 8px 0;">{{ guests_count }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-weight: bold;">Total Amount:</td>
            <td style="padding: 8px 0; font-size: 18px; font-weight: bold; color: #2196f3;">{{ total_amount }}</td>
        </tr>
    </table>
</div>

<div style="background: #fff3cd; padding: 20px; border-radius: 8px; margin-bottom: 30px; border-left: 4px solid #ffc107;">
    <h4 style="color: #856404; margin-top: 0;">⏰ What Happens Next?</h4>
    <div style="color: #856404;">
        <p><strong>1. Payment Processing</strong><br>
        We're currently processing your payment and verifying availability.</p>
        
        <p><strong>2. Confirmation Email</strong><br>
        You'll receive a confirmation email with your booking number within {{ estimated_confirmation_time }}.</p>
        
        <p><strong>3. Property Access</strong><br>
        Once confirmed, you'll get access to booking details and check-in information.</p>
    </div>
</div>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ property_link }}" style="background: #28a745; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
        🏠 View Property Details
    </a>
</div>

<div style="background: #d1ecf1; padding: 20px; border-radius: 8px; margin-bottom: 30px; border-left: 4px solid #17a2b8;">
    <h4 style="color: #0c5460; margin-top: 0;">📞 Need Help?</h4>
    <p style="color: #0c5460; margin-bottom: 0;">
        If you have any questions about your booking or need to make changes, please don't hesitate to contact us. Our team is here to help!
    </p>
</div>

<div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
    <h4 style="color: #495057; margin-top: 0;">🔒 Secure Processing</h4>
    <p style="color: #495057; margin-bottom: 0;">
        Your payment information is processed securely using industry-standard encryption. We never store your complete payment details on our servers.
    </p>
</div>

<p>We're excited to host you and will send your confirmation details shortly!</p>

<p>Best regards,<br>
<strong>{{ site_title }} Team</strong></p>

<hr style="margin: 30px 0; border: none; border-top: 1px solid #dee2e6;">

<p style="font-size: 12px; color: #6c757d;">
    <strong>Please Note:</strong> This is a processing notification. Your booking is not yet confirmed. You will receive a separate confirmation email once payment is verified and the booking is finalized.
</p>

{{ footer }}
