<?php

return [
    'webhook_secret' => 'Secret Webhook',
    'webhook_setup_guide' => [
        'title' => 'Guide de configuration de Stripe Webhook',
        'description' => 'Suivez ces étapes pour configurer un webhook Stripe',
        'step_1_label' => 'Connectez-vous au tableau de bord Stripe',
        'step_1_description' => 'Access the :link and click on the "Add Endpoint" button in the "Webhooks" section of the "Developers" tab.',
        'step_2_label' => 'Sélectionnez l\'événement et configurez le point de terminaison',
        'step_2_description' => 'Select the "payment_intent.succeeded" event and enter the following URL in the "Endpoint URL" field: :url',
        'step_3_label' => 'Ajouter un point final',
        'step_3_description' => 'Cliquez sur le bouton "Ajouter un point de terminaison" pour enregistrer le webhook.',
        'step_4_label' => 'Copier le secret de la signature',
        'step_4_description' => 'Copiez la valeur "Signer Secret" de la section "Détails Webhook" et collez-le dans le champ "Stripe Webhook Secret" dans la section "Stripe" de l\'onglet "Paiement" dans la page "Paramètres".',
    ],
];
