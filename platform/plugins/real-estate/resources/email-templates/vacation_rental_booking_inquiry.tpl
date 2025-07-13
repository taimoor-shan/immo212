{{ header }}
<div class="bb-main-content">
    <table class="bb-box" cellpadding="0" cellspacing="0">
        <tbody>
        <tr>
            <td class="bb-content bb-pb-0" align="center">
                <table class="bb-icon bb-icon-lg bb-bg-blue" cellspacing="0" cellpadding="0">
                    <tbody>
                    <tr>
                        <td valign="middle" align="center">
                            <img src="{{ 'calendar' | icon_url }}" class="bb-va-middle" width="40" height="40" alt="Icon">
                        </td>
                    </tr>
                    </tbody>
                </table>
                <h1 class="bb-text-center bb-m-0 bb-mt-md">New Vacation Rental Booking Inquiry</h1>
            </td>
        </tr>
        <tr>
            <td>
                <table cellpadding="0" cellspacing="0">
                    <tbody>
                    <tr>
                        <td class="bb-content">
                            <p>Dear Property Owner,</p>

                            <h4>You have received a new booking inquiry for your vacation rental property from {{ site_title }}:</h4>

                            <table class="bb-table" cellspacing="0" cellpadding="0">
                                <thead>
                                <tr>
                                    <th width="150px"></th>
                                </tr>
                                </thead>
                                <tbody>
                                {% if property_name %}
                                <tr>
                                    <td>Property:</td>
                                    <td class="bb-font-strong bb-text-left">{{ property_name }}</td>
                                </tr>
                                {% endif %}
                                {% if consult_name %}
                                <tr>
                                    <td>Guest Name:</td>
                                    <td class="bb-font-strong bb-text-left">{{ consult_name }} ({{ consult_ip_address }})</td>
                                </tr>
                                {% endif %}
                                {% if consult_email %}
                                <tr>
                                    <td>Email:</td>
                                    <td class="bb-font-strong bb-text-left">{{ consult_email }}</td>
                                </tr>
                                {% endif %}
                                {% if consult_phone %}
                                <tr>
                                    <td>Phone:</td>
                                    <td class="bb-font-strong bb-text-left">{{ consult_phone }}</td>
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
                                </tbody>
                            </table>

                            {% if consult_content %}
                            <h4>Message from Guest:</h4>
                            <div class="bb-quote">
                                {{ consult_content | nl2br }}
                            </div>
                            {% endif %}

                            {% if consult_custom_fields %}
                            <h4>Additional Information:</h4>
                            <table class="bb-table" cellspacing="0" cellpadding="0">
                                <tbody>
                                {% for field_name, field_value in consult_custom_fields %}
                                <tr>
                                    <td width="150px">{{ field_name }}:</td>
                                    <td class="bb-font-strong bb-text-left">{{ field_value }}</td>
                                </tr>
                                {% endfor %}
                                </tbody>
                            </table>
                            {% endif %}

                            <div class="bb-text-center bb-mt-lg">
                                <a href="{{ consult_link }}" class="bb-btn bb-btn-primary">View Inquiry Details</a>
                            </div>

                            <p class="bb-mt-md">
                                <strong>Next Steps:</strong><br>
                                • Review the booking request details<br>
                                • Check your property availability<br>
                                • Contact the guest directly to confirm or discuss the booking<br>
                                • Update your calendar if the booking is confirmed
                            </p>

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
