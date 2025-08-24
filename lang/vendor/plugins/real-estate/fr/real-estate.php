<?php

return [
    'name' => 'Immobilier',
    'settings' => 'Paramètres',
    'login_form' => 'Formulaire de connexion',
    'register_form' => 'Formulaire de registre',
    'forgot_password_form' => 'Formulaire de mot de passe oublié',
    'reset_password_form' => 'Réinitialiser le formulaire de mot de passe',
    'consult_form' => 'Consulter le formulaire',
    'review_form' => 'Formulaire de revue',
    'theme_options' => [
        'slug_name' => 'URL de l\'immobilier',
        'slug_description' => 'Personnalisez les limaces utilisées pour les pages immobilières. Soyez prudent lors de la modification car il peut affecter le référencement et l\'expérience utilisateur. Si quelque chose ne va pas, vous pouvez réinitialiser par défaut en tapant la valeur par défaut ou en le laissant vide.',
        'page_slug_name' => ':page page slug',
        'page_slug_description' => 'It will look like :slug when you access the page. Default value is :default.',
        'page_slug_already_exists' => 'The :slug page slug is already in use. Please choose another one.',
        'page_slugs' => [
            'projects_city' => 'Projets par ville',
            'projects_state' => 'Projets par état',
            'properties_city' => 'Propriétés par ville',
            'properties_state' => 'Propriétés par état',
        ],
    ],
];
