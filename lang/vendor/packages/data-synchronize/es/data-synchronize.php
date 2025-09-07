<?php

return [
    'tools' => [
        'export_import_data' => 'Exportar/Importar datos',
    ],
    'import' => [
        'name' => 'Importar',
        'heading' => 'Importar :label',
        'failed_to_read_file' => 'El archivo no es válido, está dañado o es demasiado grande para leer.',
        'form' => [
            'quick_export_message' => 'Si desea exportar los datos de :label, puede hacerlo rápidamente haciendo clic en :export_csv_link o :export_excel_link.',
            'quick_export_button' => 'Exportar a :format',
            'dropzone_message' => 'Arrastre y suelte el archivo aquí o haga clic para cargar',
            'allowed_extensions' => 'Elija un archivo con las siguientes extensiones: :extensions.',
            'import_button' => 'Importar',
            'chunk_size' => 'Tamaño de fragmento',
            'chunk_size_helper' => 'El número de filas que se importan a la vez está definido por el tamaño del bloque. Aumente este valor si tiene un archivo grande y los datos se importan muy rápido. Disminuya este valor si encuentra límites de memoria o problemas de tiempo de espera del gateway al importar datos.',
        ],
        'failures' => [
            'title' => 'Fallos',
            'attribute' => 'Atributo',
            'errors' => 'Errores',
        ],
        'example' => [
            'title' => 'Ejemplo',
            'download' => 'Descargar archivo de ejemplo :type',
        ],
        'rules' => [
            'title' => 'Reglas',
            'column' => 'Columna',
        ],
        'uploading_message' => 'Comenzando a subir el archivo...',
        'uploaded_message' => 'El archivo :file se ha subido correctamente. Comenzando la validación de datos...',
        'validating_message' => 'Validando desde :from hasta :to...',
        'importing_message' => 'Importando de :from a :to...',
        'done_message' => 'Se importaron correctamente :count :label.',
        'validating_failed_message' => 'La validación ha fallado. Por favor, revise los errores a continuación.',
        'no_data_message' => 'Sus datos ya están actualizados o no hay datos para importar.',
    ],
    'export' => [
        'name' => 'Exportar',
        'heading' => 'Exportar :label',
        'form' => [
            'all_columns_disabled' => 'Se exportarán las siguientes columnas: :columns.',
            'columns' => 'Columnas',
            'format' => 'Formato',
            'export_button' => 'Exportar',
        ],
        'success_message' => 'Exportado correctamente.',
        'error_message' => 'La exportación ha fallado.',
        'empty_state' => [
            'title' => 'No hay datos para exportar',
            'description' => 'Parece que no hay datos para exportar.',
            'back' => 'Volver a :page',
        ],
    ],
    'check_all' => 'Seleccionar todo',
];
