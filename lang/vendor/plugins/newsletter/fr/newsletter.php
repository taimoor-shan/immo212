<?php

return [
    'name' => 'Newsletters',
    'newsletter_form' => 'Formulaire de newsletters',
    'description' => 'Afficher et supprimer les abonnés de newsletter',
    'settings' => [
        'email' => [
            'templates' => [
                'title' => 'Bulletin',
                'description' => 'Modèles d\'e-mail de newsletter config',
                'to_admin' => [
                    'title' => 'Email Envoi à l\'administrateur',
                    'description' => 'Modèle pour envoyer un e-mail à l\'administrateur',
                    'subject' => 'Le nouvel utilisateur a abonné votre newsletter',
                    'newsletter_email' => 'E-mail de l\'utilisateur qui souscrit la newsletter',
                ],
                'to_user' => [
                    'title' => 'Email Envoi à l\'utilisateur',
                    'description' => 'Modèle pour envoyer un e-mail à l\'abonné',
                    'subject' => '{{site_title}}: abonnement confirmé!',
                    'newsletter_name' => 'Nom complet de l\'utilisateur qui souscrit la newsletter',
                    'newsletter_email' => 'E-mail de l\'utilisateur qui souscrit la newsletter',
                    'newsletter_unsubscribe_link' => 'Lien pour la newsletter de désabonnement',
                    'newsletter_unsubscribe_url' => 'URL pour la newsletter de désabonnement',
                ],
            ],
        ],
        'title' => 'Bulletin',
        'panel_description' => 'Afficher et mettre à jour les paramètres de newsletter',
        'description' => 'Paramètres pour la newsletter (Auto Envoyer un e-mail de newsletter à SendGrid, MailChimp ... quand quelqu\'un enregistre la newsletter sur le site Web).',
        'mailchimp_api_key' => 'Clé API MailChimp',
        'mailchimp_list_id' => 'ID de liste MailChimp',
        'mailchimp_list' => 'Liste de MailChimp',
        'sendgrid_api_key' => 'Clé API SendGrid',
        'sendgrid_list_id' => 'ID de liste SendGrid',
        'sendgrid_list' => 'Liste SendGrid',
        'enable_newsletter_contacts_list_api' => 'Activer l\'API de la liste des contacts de newsletter?',
    ],
    'statuses' => [
        'subscribed' => 'Souscrit',
        'unsubscribed' => 'Non abonné',
    ],
];
