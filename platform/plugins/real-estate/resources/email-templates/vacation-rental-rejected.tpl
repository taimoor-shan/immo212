{{ header }}

<p>Hello <strong>{{ author_name }}</strong>,</p>

<p>We regret to inform you that your vacation rental <strong>{{ vacation_rental_name }}</strong> has been rejected on {{ site_title }}.</p>

<p>The reason for rejection is as follows: <strong>{{ reason }}</strong>. If you believe this decision was made in error, please contact our support team at <a href="mailto:{{ site_email }}">{{ site_email }}</a>.</p>

<p>To view or edit your vacation rental, please click on this link: <a href="{{ vacation_rental_link }}">View Vacation Rental</a></p>

<p>You can make the necessary changes and resubmit your vacation rental for review.</p>

<p>Regards,</p>

<p><strong>{{ site_title }} Team</strong></p>

{{ footer }}
