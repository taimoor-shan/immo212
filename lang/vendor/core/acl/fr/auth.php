<?php

return [
    'login' => [
        'username' => 'Courriel / nom d\'utilisateur',
        'email' => 'E-mail',
        'password' => 'Mot de passe',
        'title' => 'Connexion de l\'utilisateur',
        'remember' => 'Souviens-toi de moi?',
        'login' => 'Se connecter',
        'placeholder' => [
            'username' => 'Entrez votre nom d\'utilisateur ou votre adresse e-mail',
            'email' => 'Entrez votre adresse e-mail',
            'password' => 'Entrez votre mot de passe',
        ],
        'success' => 'Connectez-vous avec succès!',
        'fail' => 'Mauvais nom d\'utilisateur ou mot de passe.',
        'not_active' => 'Votre compte n\'a pas encore été activé!',
        'banned' => 'Ce compte est interdit.',
        'logout_success' => 'Déconnectez-vous avec succès!',
        'dont_have_account' => 'Vous n\'avez pas de compte sur ce système, veuillez contacter l\'administrateur pour plus d\'informations!',
    ],
    'forgot_password' => [
        'title' => 'Mot de passe oublié',
        'message' => '<p> Avez-vous oublié votre mot de passe? </p> <p> Veuillez saisir votre compte de messagerie. Le système enverra un e-mail avec un lien actif pour réinitialiser votre mot de passe. </p>',
        'submit' => 'Soumettre',
    ],
    'reset' => [
        'new_password' => 'Nouveau mot de passe',
        'password_confirmation' => 'Confirmer un nouveau mot de passe',
        'email' => 'E-mail',
        'title' => 'Réinitialisez votre mot de passe',
        'update' => 'Mise à jour',
        'wrong_token' => 'Ce lien n\'est pas valide ou expiré. Veuillez essayer de réutiliser le formulaire de réinitialisation.',
        'user_not_found' => 'Ce nom d\'utilisateur n\'existe pas.',
        'success' => 'Réinitialisez le mot de passe avec succès!',
        'fail' => 'Le jeton n\'est pas valide, le lien de mot de passe de réinitialisation a été expiré!',
        'reset' => [
            'title' => 'Mot de passe de réinitialisation par e-mail',
        ],
        'send' => [
            'success' => 'Un e-mail a été envoyé à votre compte de messagerie. Veuillez vérifier et terminer cette action.',
            'fail' => 'Impossible d\'envoyer un e-mail à cette époque. Veuillez réessayer plus tard.',
        ],
        'new-password' => 'Nouveau mot de passe',
        'placeholder' => [
            'new_password' => 'Entrez votre nouveau mot de passe',
            'new_password_confirmation' => 'Confirmez votre nouveau mot de passe',
        ],
    ],
    'email' => [
        'reminder' => [
            'title' => 'Mot de passe de réinitialisation par e-mail',
        ],
    ],
    'password_confirmation' => 'Mot de passe Confirmer',
    'failed' => 'Échoué',
    'throttle' => 'Étrangler',
    'not_member' => 'Pas encore membre?',
    'register_now' => 'Inscrivez-vous maintenant',
    'lost_your_password' => 'Vous avez perdu votre mot de passe?',
    'login_title' => 'Administrer',
    'login_via_social' => 'Connectez-vous avec les réseaux sociaux',
    'back_to_login' => 'Retour à la page de connexion',
    'sign_in_below' => 'Connectez-vous ci-dessous',
    'languages' => 'Langues',
    'reset_password' => 'Réinitialiser le mot de passe',
    'settings' => [
        'email' => [
            'title' => 'ACL',
            'description' => 'Configuration de l\'e-mail ACL',
            'templates' => [
                'password_reminder' => [
                    'title' => 'Réinitialiser le mot de passe',
                    'description' => 'Envoyer un e-mail à l\'utilisateur lors de la demande de mot de passe de réinitialisation',
                    'subject' => 'Réinitialiser le mot de passe',
                    'reset_link' => 'Réinitialiser le lien de mot de passe',
                ],
            ],
        ],
    ],
];
