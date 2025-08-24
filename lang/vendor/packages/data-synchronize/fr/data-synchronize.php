<?php

return [
    'tools' => [
        'export_import_data' => 'Données d\'exportation / d\'importation',
    ],
    'import' => [
        'name' => 'Importer',
        'heading' => 'Import :label',
        'failed_to_read_file' => 'Le fichier n\'est pas valide ou corrompu ou trop grand pour lire.',
        'form' => [
            'quick_export_message' => 'If you want to export :label data, you can do it quickly by clicking on :export_csv_link or :export_excel_link.',
            'quick_export_button' => 'Export to :format',
            'dropzone_message' => 'Faites glisser et déposez le fichier ici ou cliquez pour télécharger',
            'allowed_extensions' => 'Choose a file with following extensions: :extensions.',
            'import_button' => 'Importer',
            'chunk_size' => 'Taille',
            'chunk_size_helper' => 'Le nombre de lignes à importer à la fois est défini par la taille du morceau. Augmentez cette valeur si vous avez un grand fichier et que les données sont importées très rapidement. Diminuez cette valeur si vous rencontrez des limites de mémoire ou des problèmes de délai de passerelle lors de l\'importation de données.',
        ],
        'failures' => [
            'title' => 'Échecs',
            'attribute' => 'Attribut',
            'errors' => 'Erreurs',
        ],
        'example' => [
            'title' => 'Exemple',
            'download' => 'Download example :type file',
        ],
        'rules' => [
            'title' => 'Règles',
            'column' => 'Colonne',
        ],
        'uploading_message' => 'Commencer à télécharger un fichier ...',
        'uploaded_message' => 'File :file has been uploaded successfully. Start validating data...',
        'validating_message' => 'Validating from :from to :to...',
        'importing_message' => 'Importing from :from to :to...',
        'done_message' => 'Imported :count :label successfully.',
        'validating_failed_message' => 'La validation a échoué. Veuillez vérifier les erreurs ci-dessous.',
        'no_data_message' => 'Vos données sont déjà à jour ou pas de données à importer.',
    ],
    'export' => [
        'name' => 'Exporter',
        'heading' => 'Export :label',
        'form' => [
            'all_columns_disabled' => 'Following columns will be exported: :columns.',
            'columns' => 'Colonnes',
            'format' => 'Format',
            'export_button' => 'Exporter',
        ],
        'success_message' => 'Exporté avec succès.',
        'error_message' => 'L\'exportation a échoué.',
        'empty_state' => [
            'title' => 'Aucune donnée à exporter',
            'description' => 'On dirait qu\'il n\'y a pas de données à exporter.',
            'back' => 'Back to :page',
        ],
    ],
    'check_all' => 'Vérifiez tout',
];
