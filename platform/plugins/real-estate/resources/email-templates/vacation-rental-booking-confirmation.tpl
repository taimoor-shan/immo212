{{ header }}

<div style="margin-bottom: 30px;">
    <h2 style="color: #2c3e50; margin-bottom: 20px;">🎉 Booking Confirmed!</h2>
    
    <p><strong>Dear {{ customer_name }},</strong></p>
    
    <p>Great news! Your vacation rental booking has been confirmed. We're excited to host you!</p>
</div>

<div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
    <h3 style="color: #2c3e50; margin-top: 0;">📋 Booking Details</h3>
    
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 8px 0; font-weight: bold; width: 40%;">Booking Number:</td>
            <td style="padding: 8px 0;">#{{ booking_number }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-weight: bold;">Property:</td>
            <td style="padding: 8px 0;">{{ property_name }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-weight: bold;">Address:</td>
            <td style="padding: 8px 0;">{{ property_address }}</td>
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
            <td style="padding: 8px 0; font-size: 18px; font-weight: bold; color: #27ae60;">{{ total_amount }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-weight: bold;">Status:</td>
            <td style="padding: 8px 0; color: #27ae60;">{{ booking_status }}</td>
        </tr>
    </table>
</div>

{% if special_requests != 'None' %}
<div style="background: #fff3cd; padding: 15px; border-radius: 8px; margin-bottom: 30px; border-left: 4px solid #ffc107;">
    <h4 style="color: #856404; margin-top: 0;">📝 Special Requests</h4>
    <p style="color: #856404; margin-bottom: 0;">{{ special_requests }}</p>
</div>
{% endif %}

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ booking_details_link }}" style="background: #007bff; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
        📄 View Booking Details
    </a>
    
    <a href="{{ property_link }}" style="background: #28a745; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold; margin-left: 10px;">
        🏠 View Property
    </a>
</div>

<div style="background: #e9ecef; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
    <h4 style="color: #495057; margin-top: 0;">📞 Need Help?</h4>
    <p style="margin-bottom: 0;">If you have any questions about your booking, please don't hesitate to contact us. We're here to help make your stay perfect!</p>
</div>

<p>Thank you for choosing us for your vacation rental needs. We look forward to hosting you!</p>

<p>Best regards,<br>
<strong>{{ site_title }} Team</strong></p>

<hr style="margin: 30px 0; border: none; border-top: 1px solid #dee2e6;">

<p style="font-size: 12px; color: #6c757d;">
    <strong>Important:</strong> Please save this email for your records. You can also access your booking details anytime by clicking the "View Booking Details" button above.
</p>

{{ footer }}
