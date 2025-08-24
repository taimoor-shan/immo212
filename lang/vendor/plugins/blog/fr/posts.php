<?php

return [
    'create' => 'Créer un nouveau message',
    'form' => [
        'name' => 'Nom',
        'name_placeholder' => 'Post\'s name (Maximum :c characters)',
        'description' => 'Description',
        'description_placeholder' => 'Short description for post (Maximum :c characters)',
        'categories' => 'Catégories',
        'tags' => 'Balises',
        'tags_placeholder' => 'Balises',
        'content' => 'Contenu',
        'is_featured' => 'Présente ce post',
        'note' => 'Noter le contenu',
        'format_type' => 'Format',
    ],
    'cannot_delete' => 'Le poste n\'a pas pu être supprimé',
    'post_deleted' => 'Post supprimé',
    'posts' => 'Publications',
    'post' => 'Poste',
    'edit_this_post' => 'Modifier ce post',
    'no_new_post_now' => 'Il n\'y a pas de nouveau message maintenant!',
    'menu_name' => 'Publications',
    'widget_posts_recent' => 'Publications récentes',
    'categories' => 'Catégories',
    'category' => 'Catégorie',
    'author' => 'Auteur',
    'export' => [
        'description' => 'Exporter des publications vers le fichier CSV / Excel.',
        'total' => 'Postes totaux',
    ],
    'import' => [
        'description' => 'Importez des publications à partir d\'un fichier CSV / Excel.',
        'done_message' => ':created posts created and :updated posts updated.',
        'rules' => [
            'nullable_string_max' => 'The :attribute field accepts a string value of up to :max characters or may be left blank.',
            'sometimes_array' => 'The :attribute field accepts an array value or may be left blank.',
            'in' => ':attribute must be one of the following values: :values.',
            'nullable_string' => 'The :attribute field accepts a string value or may be left blank.',
            'nullable_string_max_in' => 'The :attribute field can be left blank, or must be a string with a maximum length of :max characters if provided and must be one of the following values: :values.',
        ],
    ],
];
