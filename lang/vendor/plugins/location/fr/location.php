<?php

return [
    'name' => 'Lieux',
    'all_states' => 'Tous les États',
    'abbreviation' => 'Abréviation',
    'abbreviation_placeholder' => 'Par exemple: ca',
    'enums' => [
        'import_type' => [
            'country' => 'Pays',
            'state' => 'État',
            'city' => 'Ville',
        ],
    ],
    'export' => [
        'total' => 'Total des emplacements',
        'total_countries' => 'Total des pays',
        'total_states' => 'Total des États',
        'total_cities' => 'Total des villes',
        'description' => 'Exportez vos données de localisation comme les pays, les États et les villes.',
    ],
    'import' => [
        'description' => 'Importez facilement les données de localisation à partir des données disponibles ou en téléchargeant un fichier CSV / Excel.',
        'rules' => [
            'name' => 'Le nom de l\'emplacement est obligatoire et ne doit pas dépasser 120 caractères.',
            'slug' => 'La limace de l\'emplacement, si elle est fournie, ne doit pas dépasser 120 caractères.',
            'import_type' => 'Le type d\'importation est obligatoire et devrait être l\'une des valeurs prédéfinies.',
            'order' => 'L\'ordre de l\'emplacement, s\'il est fourni, doit être un entier positif entre 0 et 127.',
            'abbreviation' => 'L\'abréviation de l\'emplacement, si elle est fournie, ne doit pas dépasser 10 caractères.',
            'status' => 'L\'état de l\'emplacement est obligatoire et devrait être l\'une des valeurs prédéfinies.',
            'country' => 'Le champ du pays est obligatoire si le type d\'importation est de l\'État ou de la ville.',
            'state' => 'Le champ d\'État est obligatoire si le type d\'importation est de la ville.',
            'nationality' => 'La nationalité de l\'emplacement, si elle est fournie, ne doit pas dépasser 120 caractères.',
        ],
    ],
];
