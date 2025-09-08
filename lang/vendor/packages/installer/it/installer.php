<?php

return [
    'title' => 'Installazione',
    'next' => 'Prossimo passo',
    'back' => 'Precedente',
    'finish' => 'Installa',
    'installation' => 'Installazione',
    'forms' => [
        'errorTitle' => 'Si sono verificati i seguenti errori:',
    ],
    'welcome' => [
        'title' => 'Benvenuto',
        'message' => 'Prima di iniziare, abbiamo bisogno di alcune informazioni sul database. Dovrà conoscere i seguenti elementi prima di procedere.',
        'language' => 'Lingua',
        'next' => 'Andiamo',
    ],
    'requirements' => [
        'title' => 'Requisiti del server',
        'next' => 'Controlla autorizzazioni',
    ],
    'permissions' => [
        'next' => 'Configura ambiente',
    ],
    'environment' => [
        'wizard' => [
            'title' => 'Impostazioni ambiente',
            'form' => [
                'name_required' => 'È necessario specificare un nome per l\'ambiente.',
                'app_name_label' => 'Titolo del sito',
                'app_name_placeholder' => 'Titolo del sito',
                'db_connection_label' => 'Connessione al database',
                'db_host_label' => 'Host del database',
                'db_host_placeholder' => 'Host del database',
                'db_port_label' => 'Porta del database',
                'db_port_placeholder' => 'Porta del database',
                'db_name_label' => 'Nome del database',
                'db_name_placeholder' => 'Nome del database',
                'db_username_label' => 'Nome utente del database',
                'db_username_placeholder' => 'Nome utente del database',
                'db_password_label' => 'Password del database',
                'db_password_placeholder' => 'Password del database',
                'buttons' => [
                    'install' => 'Installa',
                ],
                'db_host_helper' => 'Se utilizzi Laravel Sail, basta cambiare DB_HOST in DB_HOST=mysql. Su alcuni hosting, DB_HOST può essere localhost invece di 127.0.0.1',
            ],
        ],
        'success' => 'Le impostazioni del file .env sono state salvate.',
        'errors' => 'Impossibile salvare il file .env. Si prega di crearlo manualmente.',
    ],
    'theme' => [
        'title' => 'Scegli tema',
        'message' => 'Scegli un tema per personalizzare l’aspetto del tuo sito web. Questa selezione importerà anche dati di esempio adattati al tema scelto.',
    ],
    'theme_preset' => [
        'title' => 'Scegli il tema predefinito',
        'message' => 'Scegli un tema predefinito per personalizzare l’aspetto del tuo sito web. Questa selezione importerà anche dati di esempio adattati al tema scelto.',
    ],
    'createAccount' => [
        'title' => 'Crea account',
        'form' => [
            'first_name' => 'Nome',
            'last_name' => 'Cognome',
            'username' => 'Nome utente',
            'password_confirmation' => 'Conferma password',
            'create' => 'Crea',
        ],
    ],
    'license' => [
        'title' => 'Attiva licenza',
        'skip' => 'Salta per ora',
    ],
    'install' => 'Installa',
    'final' => [
        'pageTitle' => 'Installazione completata',
        'title' => 'Fatto',
        'message' => 'L\'applicazione è stata installata con successo.',
        'exit' => 'Vai al pannello di amministrazione',
    ],
    'install_success' => 'Installato con successo!',
    'install_step_title' => 'Installazione - Passaggio :step: :title',
];
