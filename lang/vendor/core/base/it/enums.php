<?php

return [
    'statuses' => [
        'draft' => 'Bozza',
        'pending' => 'In sospeso',
        'published' => 'Pubblicato',
    ],
    'system_updater_steps' => [
        'download' => 'Scarica i file di aggiornamento',
        'update_files' => 'Aggiorna i file di sistema',
        'update_database' => 'Aggiorna database',
        'publish_core_assets' => 'Pubblica risorse principali',
        'publish_packages_assets' => 'Pubblica le risorse dei pacchetti',
        'clean_up' => 'Pulisci i file di aggiornamento di sistema',
        'done' => 'Sistema aggiornato con successo',
        'messages' => [
            'download' => 'Download dei file di aggiornamento in corso...',
            'update_files' => 'Aggiornamento dei file di sistema...',
            'update_database' => 'Aggiornamento dei database...',
            'publish_core_assets' => 'Pubblicazione degli asset principali...',
            'publish_packages_assets' => 'Pubblicazione degli asset dei pacchetti...',
            'clean_up' => 'Pulizia dei file di aggiornamento del sistema in corso...',
            'done' => 'Fatto! Il tuo browser verrà aggiornato tra 30 secondi.',
        ],
        'failed_messages' => [
            'download' => 'Impossibile scaricare i file di aggiornamento',
            'update_files' => 'Impossibile aggiornare i file di sistema',
            'update_database' => 'Impossibile aggiornare i database',
            'publish_core_assets' => 'Impossibile pubblicare le risorse principali',
            'publish_packages_assets' => 'Impossibile pubblicare le risorse dei pacchetti',
            'clean_up' => 'Impossibile pulire i file di aggiornamento del sistema',
        ],
        'success_messages' => [
            'download' => 'File di aggiornamento scaricati con successo.',
            'update_files' => 'File di sistema aggiornati con successo.',
            'update_database' => 'Database aggiornati con successo.',
            'publish_core_assets' => 'Asset principali pubblicati con successo.',
            'publish_packages_assets' => 'Pacchetti pubblicati con successo.',
        ],
    ],
];
