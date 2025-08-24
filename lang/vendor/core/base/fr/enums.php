<?php

return [
    'statuses' => [
        'draft' => 'Brouillon',
        'pending' => 'En attente',
        'published' => 'Publié',
    ],
    'system_updater_steps' => [
        'download' => 'Télécharger des fichiers de mise à jour',
        'update_files' => 'Mettre à jour les fichiers système',
        'update_database' => 'Mettre à jour les bases de données',
        'publish_core_assets' => 'Publier les actifs de base',
        'publish_packages_assets' => 'Publier des actifs de packages',
        'clean_up' => 'Nettoyer les fichiers de mise à jour du système',
        'done' => 'Système mis à jour avec succès',
        'messages' => [
            'download' => 'Télécharger des fichiers de mise à jour ...',
            'update_files' => 'Mise à jour des fichiers système ...',
            'update_database' => 'Mise à jour des bases de données ...',
            'publish_core_assets' => 'Publication des actifs de base ...',
            'publish_packages_assets' => 'Publication des actifs de packages ...',
            'clean_up' => 'Nettoyage des fichiers de mise à jour du système ...',
            'done' => 'Fait! Votre navigateur sera rafraîchi en 30 secondes.',
        ],
        'failed_messages' => [
            'download' => 'Impossible de télécharger des fichiers de mise à jour',
            'update_files' => 'Impossible de mettre à jour les fichiers système',
            'update_database' => 'Impossible de mettre à jour les bases de données',
            'publish_core_assets' => 'Impossible de publier des actifs de base',
            'publish_packages_assets' => 'Impossible de publier des actifs de packages',
            'clean_up' => 'Impossible de nettoyer les fichiers de mise à jour du système',
        ],
        'success_messages' => [
            'download' => 'Téléchargé les fichiers de mise à jour avec succès.',
            'update_files' => 'Mis à jour les fichiers système avec succès.',
            'update_database' => 'Les bases de données mises à jour avec succès.',
            'publish_core_assets' => 'Publié les principaux actifs avec succès.',
            'publish_packages_assets' => 'Publié les actifs de packages avec succès.',
            'clean_up' => 'Nettoyé les fichiers de mise à jour du système avec succès.',
        ],
    ],
];
