{{ header }}
<div class="bb-main-content">
    <table class="bb-box" cellpadding="0" cellspacing="0">
        <tbody>
        <tr>
            <td class="bb-content bb-pb-0" align="center">
                <table class="bb-icon bb-icon-lg bb-bg-green" cellspacing="0" cellpadding="0">
                    <tbody>
                    <tr>
                        <td valign="middle" align="center">
                            <img src="{{ 'check-circle' | icon_url }}" class="bb-va-middle" width="40" height="40" alt="Icon">
                        </td>
                    </tr>
                    </tbody>
                </table>
                <h1 class="bb-text-center bb-m-0 bb-mt-md">New Booking Confirmed!</h1>
            </td>
        </tr>
        <tr>
            <td>
                <table cellpadding="0" cellspacing="0">
                    <tbody>
                    <tr>
                        <td class="bb-content">
                            <p>Dear Property Owner,</p>

                            <h4>Great news! You have a new confirmed booking for your vacation rental property from {{ site_title }}:</h4>

                            <table class="bb-table" cellspacing="0" cellpadding="0">
                                <thead>
                                <tr>
                                    <th width="150px"></th>
                                </tr>
                                </thead>
                                <tbody>
                                {% if booking_number %}
                                <tr>
                                    <td>Booking Number:</td>
                                    <td class="bb-font-strong bb-text-left" style="color: #28a745;">#{{ booking_number }}</td>
                                </tr>
                                {% endif %}
                                {% if property_name %}
                                <tr>
                                    <td>Property:</td>
                                    <td class="bb-font-strong bb-text-left">{{ property_name }}</td>
                                </tr>
                                {% endif %}
                                {% if guest_name %}
                                <tr>
                                    <td>Guest Name:</td>
                                    <td class="bb-font-strong bb-text-left">{{ guest_name }}</td>
                                </tr>
                                {% endif %}
                                {% if guest_email %}
                                <tr>
                                    <td>Guest Email:</td>
                                    <td class="bb-font-strong bb-text-left">{{ guest_email }}</td>
                                </tr>
                                {% endif %}
                                {% if guest_phone %}
                                <tr>
                                    <td>Guest Phone:</td>
                                    <td class="bb-font-strong bb-text-left">{{ guest_phone }}</td>
                                </tr>
                                {% endif %}
                                {% if check_in_date %}
                                <tr>
                                    <td>Check-in Date:</td>
                                    <td class="bb-font-strong bb-text-left">{{ check_in_date }}</td>
                                </tr>
                                {% endif %}
                                {% if check_out_date %}
                                <tr>
                                    <td>Check-out Date:</td>
                                    <td class="bb-font-strong bb-text-left">{{ check_out_date }}</td>
                                </tr>
                                {% endif %}
                                {% if guests_count %}
                                <tr>
                                    <td>Number of Guests:</td>
                                    <td class="bb-font-strong bb-text-left">{{ guests_count }}</td>
                                </tr>
                                {% endif %}
                                {% if total_amount %}
                                <tr>
                                    <td>Total Amount:</td>
                                    <td class="bb-font-strong bb-text-left" style="color: #28a745; font-size: 16px;">{{ total_amount }}</td>
                                </tr>
                                {% endif %}
                                {% if payment_status %}
                                <tr>
                                    <td>Payment Status:</td>
                                    <td class="bb-font-strong bb-text-left" style="color: #28a745;">{{ payment_status }}</td>
                                </tr>
                                {% endif %}
                                </tbody>
                            </table>

                            {% if special_requests and special_requests != 'None' %}
                            <h4>Special Requests from Guest:</h4>
                            <div class="bb-quote">
                                {{ special_requests | nl2br }}
                            </div>
                            {% endif %}

                            <div class="bb-text-center bb-mt-lg">
                                {% if booking_link %}
                                <a href="{{ booking_link }}" class="bb-btn bb-btn-primary">View Booking Details</a>
                                {% endif %}
                                {% if property_link %}
                                <a href="{{ property_link }}" class="bb-btn bb-btn-secondary bb-ml-sm">View Property</a>
                                {% endif %}
                            </div>

                            <div class="bb-alert bb-alert-warning bb-mt-lg">
                                <h4>⏰ Preparation Checklist:</h4>
                                <ul style="margin: 0; padding-left: 20px;">
                                    <li>Confirm property is clean and ready for guests</li>
                                    <li>Check all amenities are working properly</li>
                                    <li>Prepare welcome information and check-in instructions</li>
                                    <li>Review any special requests from the guest</li>
                                    <li>Block calendar dates if not already done</li>
                                    <li>Consider reaching out to welcome the guest</li>
                                </ul>
                            </div>

                            <p class="bb-mt-md">
                                <strong>Important:</strong> This is a confirmed, paid booking. Please ensure your property is ready for the guest's arrival and consider sending a welcome message.
                            </p>

                            <p>Congratulations on your new booking! This confirmed reservation will contribute to your property's success and guest satisfaction.</p>

                            <p>Best regards,<br>
                            <strong>{{ site_title }} Team</strong></p>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>
{{ footer }}
