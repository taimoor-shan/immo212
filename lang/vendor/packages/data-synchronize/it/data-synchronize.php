<?php

return [
    'tools' => [
        'export_import_data' => 'Esporta/Importa dati',
    ],
    'import' => [
        'name' => 'Importa',
        'heading' => 'Importa :label',
        'failed_to_read_file' => 'Il file non è valido, è danneggiato o troppo grande per essere letto.',
        'form' => [
            'quick_export_message' => 'Se desidera esportare i dati :label, può farlo rapidamente cliccando su :export_csv_link o :export_excel_link.',
            'quick_export_button' => 'Esporta in :format',
            'allowed_extensions' => 'Scegli un file con le seguenti estensioni: :extensions.',
            'import_button' => 'Importa',
            'chunk_size' => 'Dimensione del blocco',
            'chunk_size_helper' => 'Il numero di righe da importare alla volta è definito dalla dimensione del blocco (chunk size). Aumenta questo valore se hai un file di grandi dimensioni e i dati vengono importati molto rapidamente. Riduci questo valore se riscontri limiti di memoria o problemi di timeout del gateway durante l’importazione dei dati.',
        ],
        'failures' => [
            'title' => 'Errori',
            'attribute' => 'Attributo',
            'errors' => 'Errori',
        ],
        'example' => [
            'title' => 'Esempio',
            'download' => 'Scarica il file di esempio :type',
        ],
        'rules' => [
            'title' => 'Regole',
            'column' => 'Colonna',
        ],
        'uploading_message' => 'Inizio caricamento file...',
        'uploaded_message' => 'File :file è stato caricato con successo. Avvio della convalida dei dati...',
        'validating_message' => 'Validazione da :from a :to...',
        'importing_message' => 'Importazione da :from a :to...',
        'done_message' => 'Importati :count :label con successo.',
        'validating_failed_message' => 'Convalida non riuscita. Si prega di controllare gli errori qui sotto.',
        'no_data_message' => 'I tuoi dati sono già aggiornati oppure non ci sono dati da importare.',
    ],
    'export' => [
        'name' => 'Esporta',
        'heading' => 'Esporta :label',
        'form' => [
            'all_columns_disabled' => 'Le seguenti colonne verranno esportate: :columns.',
            'columns' => 'Colonne',
            'format' => 'Formato',
            'export_button' => 'Esporta',
        ],
        'success_message' => 'Esportazione riuscita.',
        'error_message' => 'Esportazione non riuscita.',
        'empty_state' => [
            'title' => 'Nessun dato da esportare',
            'description' => 'Sembra che non ci siano dati da esportare.',
            'back' => 'Torna a :page',
        ],
    ],
    'check_all' => 'Seleziona tutto',
];
