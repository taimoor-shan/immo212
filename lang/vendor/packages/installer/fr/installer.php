<?php

return [
    'title' => 'Installation',
    'next' => 'Prochaine étape',
    'back' => 'Précédent',
    'finish' => 'Installer',
    'installation' => 'Installation',
    'forms' => [
        'errorTitle' => 'Les erreurs suivantes se sont produites:',
    ],
    'welcome' => [
        'title' => 'Accueillir',
        'message' => 'Avant de commencer, nous avons besoin de quelques informations sur la base de données. Vous devrez connaître les éléments suivants avant de continuer.',
        'language' => 'Langue',
        'next' => 'Allons-y',
    ],
    'requirements' => [
        'title' => 'Exigences du serveur',
        'next' => 'Vérifier les autorisations',
    ],
    'permissions' => [
        'next' => 'Configurer l\'environnement',
    ],
    'environment' => [
        'wizard' => [
            'title' => 'Paramètres environnementaux',
            'form' => [
                'name_required' => 'Un nom d\'environnement est requis.',
                'app_name_label' => 'Titre du site',
                'app_name_placeholder' => 'Titre du site',
                'app_url_label' => 'URL',
                'app_url_placeholder' => 'URL',
                'db_connection_label' => 'Connexion de base de données',
                'db_connection_label_mysql' => 'Mysql',
                'db_connection_label_sqlite' => 'Sqlite',
                'db_connection_label_pgsql' => 'Postgresql',
                'db_host_label' => 'Hôte de base de données',
                'db_host_placeholder' => 'Hôte de base de données',
                'db_port_label' => 'Port de base de données',
                'db_port_placeholder' => 'Port de base de données',
                'db_name_label' => 'Nom de base de données',
                'db_name_placeholder' => 'Nom de base de données',
                'db_username_label' => 'Nom d\'utilisateur de base de données',
                'db_username_placeholder' => 'Nom d\'utilisateur de base de données',
                'db_password_label' => 'Mot de passe de base de données',
                'db_password_placeholder' => 'Mot de passe de base de données',
                'buttons' => [
                    'install' => 'Installer',
                ],
                'db_host_helper' => 'Si vous utilisez Laravel Sail, changez simplement DB_HOST en db_host = mysql. Sur un hébergement db_host peut être localhost au lieu de 127.0.0.1',
                'db_connections' => [
                    'mysql' => 'Mysql',
                    'sqlite' => 'Sqlite',
                    'pgsql' => 'Postgresql',
                ],
            ],
        ],
        'success' => 'Vos paramètres de fichier .env ont été enregistrés.',
        'errors' => 'Impossible d\'enregistrer le fichier .env, veuillez le créer manuellement.',
    ],
    'theme' => [
        'title' => 'Choisir le thème',
        'message' => 'Choisissez un thème pour personnaliser l\'apparence de votre site Web. Cette sélection importera également des exemples de données adaptées au thème choisi.',
    ],
    'theme_preset' => [
        'title' => 'Choisissez le thème préréglé',
        'message' => 'Choisissez un préréglage de thème pour personnaliser l\'apparence de votre site Web. Cette sélection importera également des exemples de données adaptées au thème choisi.',
    ],
    'createAccount' => [
        'title' => 'Créer un compte',
        'form' => [
            'first_name' => 'Prénom',
            'last_name' => 'Nom de famille',
            'username' => 'Nom d\'utilisateur',
            'email' => 'E-mail',
            'password' => 'Mot de passe',
            'password_confirmation' => 'Confirmation de mot de passe',
            'create' => 'Créer',
        ],
    ],
    'license' => [
        'title' => 'Activer la licence',
        'skip' => 'Sauter pour l\'instant',
    ],
    'install' => 'Installer',
    'final' => [
        'pageTitle' => 'Installation terminée',
        'title' => 'Fait',
        'message' => 'L\'application a été installée avec succès.',
        'exit' => 'Aller au tableau de bord administratif',
    ],
    'install_success' => 'Installé avec succès!',
    'install_step_title' => 'Installation - Step :step: :title',
];
