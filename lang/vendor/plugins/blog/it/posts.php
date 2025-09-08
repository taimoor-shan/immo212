<?php

return [
    'create' => 'Crea un nuovo post',
    'form' => [
        'name' => 'Nome',
        'name_placeholder' => 'Nome del post (Massimo :c caratteri)',
        'description' => 'Descrizione',
        'description_placeholder' => 'Breve descrizione per il post (Massimo :c caratteri)',
        'categories' => 'Categorie',
        'tags' => 'Tag',
        'tags_placeholder' => 'Tag',
        'content' => 'Contenuto',
        'is_featured' => 'Metti in evidenza questo post',
        'note' => 'Contenuto della nota',
        'format_type' => 'Formato',
    ],
    'cannot_delete' => 'Impossibile eliminare il post',
    'post_deleted' => 'Post eliminato',
    'posts' => 'Post',
    'post' => 'Pubblica',
    'edit_this_post' => 'Modifica questo post',
    'no_new_post_now' => 'Non ci sono nuovi post al momento!',
    'menu_name' => 'Post',
    'widget_posts_recent' => 'Post recenti',
    'categories' => 'Categorie',
    'category' => 'Categoria',
    'author' => 'Autore',
    'export' => [
        'description' => 'Esporta i post in un file CSV/Excel.',
        'total' => 'Post totali',
    ],
    'import' => [
        'description' => 'Importa post da un file CSV/Excel.',
        'done_message' => ':created post creati e :updated post aggiornati.',
        'rules' => [
            'nullable_string_max' => 'Il campo :attribute accetta un valore di tipo stringa fino a :max caratteri oppure può essere lasciato vuoto.',
            'sometimes_array' => 'Il campo :attribute accetta un valore di tipo array o può essere lasciato vuoto.',
            'in' => ':attribute deve essere uno dei seguenti valori: :values.',
            'nullable_string' => 'Il campo :attribute accetta un valore di tipo stringa oppure può essere lasciato vuoto.',
            'nullable_string_max_in' => 'Il campo :attribute può essere lasciato vuoto, oppure deve essere una stringa con una lunghezza massima di :max caratteri se fornito e deve essere uno dei seguenti valori: :values.',
        ],
    ],
];
