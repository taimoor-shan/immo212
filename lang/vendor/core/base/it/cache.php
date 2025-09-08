<?php

return [
    'cache_management' => 'Gestione della cache',
    'cache_management_description' => 'Svuota la cache per aggiornare il tuo sito.',
    'cache_commands' => 'Comandi per svuotare la cache',
    'commands' => [
        'clear_cms_cache' => [
            'title' => 'Cancella tutta la cache del CMS',
            'description' => 'Svuota la cache del CMS: cache del database, blocchi statici... Esegui questo comando quando non visualizzi le modifiche dopo aver aggiornato i dati.',
            'success_msg' => 'Cache svuotata',
        ],
        'refresh_compiled_views' => [
            'title' => 'Aggiorna le viste compilate',
            'description' => 'Cancella le viste compilate per aggiornare le viste.',
            'success_msg' => 'Vista cache aggiornata',
        ],
        'clear_config_cache' => [
            'title' => 'Cancella la cache di configurazione',
            'description' => 'Potrebbe essere necessario aggiornare la cache della configurazione quando si apportano modifiche nell’ambiente di produzione.',
            'success_msg' => 'Cache di configurazione pulita',
        ],
        'clear_route_cache' => [
            'title' => 'Cancella la cache delle rotte',
            'description' => 'Cancella la cache del routing.',
            'success_msg' => 'La cache delle rotte è stata pulita',
        ],
        'clear_log' => [
            'title' => 'Cancella registro',
            'description' => 'Cancella i file di log di sistema',
            'success_msg' => 'Il registro di sistema è stato pulito',
        ],
    ],
];
