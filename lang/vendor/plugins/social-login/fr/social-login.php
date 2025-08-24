<?php

return [
    'settings' => [
        'title' => 'Paramètres de connexion sociale',
        'description' => 'Configurer les options de connexion sociale',
        'facebook' => [
            'enable' => 'Activer la connexion Facebook',
            'app_id' => 'Identifiant d\'application',
            'app_secret' => 'Secret d\'applications',
            'helper' => 'Please go to https://developers.facebook.com to create new app update App ID, App Secret. Callback URL is :callback',
            'data_deletion_request_callback_url' => 'Set this URL :url as the Data Deletion Request URL in your Facebook app settings to allow users to request deletion of their data.',
        ],
        'google' => [
            'enable' => 'Activer Google Login',
            'app_id' => 'Identifiant d\'application',
            'app_secret' => 'Secret d\'applications',
            'helper' => 'Please go to https://console.developers.google.com/apis/dashboard to create new app update App ID, App Secret. Callback URL is :callback',
            'use_google_button' => 'Utiliser le bouton Google',
            'use_google_button_helper' => 'Si vous activez cette option, le bouton Google sera utilisé à la place du bouton par défaut.',
        ],
        'github' => [
            'enable' => 'Activer la connexion GitHub',
            'app_id' => 'Identifiant d\'application',
            'app_secret' => 'Secret d\'applications',
            'helper' => 'Please go to https://github.com/settings/developers to create new app update App ID, App Secret. Callback URL is :callback',
        ],
        'linkedin' => [
            'enable' => 'Activer la connexion LinkedIn',
            'app_id' => 'Identifiant d\'application',
            'app_secret' => 'Secret d\'applications',
            'helper' => 'Please go to https://www.linkedin.com/developers/apps/new to create new app update App ID, App Secret. Callback URL is :callback',
        ],
        'linkedin-openid' => [
            'enable' => 'Activer LinkedIn à l\'aide de la connexion OpenID Connect',
            'app_id' => 'Identifiant d\'application',
            'app_secret' => 'Secret d\'applications',
            'helper' => 'Please go to https://www.linkedin.com/developers/apps/new to create new app update App ID, App Secret. Callback URL is :callback',
        ],
        'enable' => 'Activer la connexion sociale?',
        'style' => 'Style',
        'minimal' => 'Minimal',
        'default' => 'Défaut',
        'basic' => 'Basique',
    ],
    'socials' => [
        'facebook' => 'Facebook',
        'google' => 'Google',
        'github' => 'Github',
        'linkedin' => 'Liendin',
        'linkedin-openid' => 'LinkedIn OpenID Connect',
    ],
    'menu' => 'Connexion sociale',
    'description' => 'Afficher et mettre à jour vos paramètres de connexion sociale',
    'sign_in_with' => 'Sign in with :provider',
];
