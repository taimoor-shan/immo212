<?php

return [
    'settings' => [
        'title' => 'Optimiser',
        'description' => 'MINIFICE HTML Sortie, en ligne CSS, supprimez les commentaires ...',
        'enable' => 'Activer Optimiser la vitesse de la page?',
    ],
    'collapse_white_space' => 'Effondrer l\'espace blanc',
    'collapse_white_space_description' => 'Ce filtre réduit les octets transmis dans un fichier HTML en supprimant les espaces blancs inutiles.',
    'elide_attributes' => 'Attributs Elide',
    'elide_attributes_description' => 'Ce filtre réduit la taille de transfert des fichiers HTML en supprimant les attributs des balises lorsque la valeur spécifiée est égale à la valeur par défaut de cet attribut. Cela peut économiser un nombre modeste d\'octets et peut rendre le document plus compressible en canonicalisant les balises affectées.',
    'inline_css' => 'CSS en ligne',
    'inline_css_description' => 'Ce filtre transforme l\'attribut "style" en ligne des balises en classes en déplaçant le CSS vers l\'en-tête.',
    'insert_dns_prefetch' => 'Insérer la préfescée DNS',
    'insert_dns_prefetch_description' => 'Ce filtre injecte des balises dans la tête pour permettre au navigateur de faire la pré-feuille DNS.',
    'remove_comments' => 'Supprimer des commentaires',
    'remove_comments_description' => 'Ce filtre élimine les commentaires HTML, JS et CSS. Le filtre réduit la taille de transfert des fichiers HTML en supprimant les commentaires. Selon le fichier HTML, ce filtre peut réduire considérablement le nombre d\'octets transmis sur le réseau.',
    'remove_quotes' => 'Supprimer les citations',
    'remove_quotes_description' => 'Ce filtre élimine les guillemets inutiles des attributs HTML. Bien que requis par les différentes spécifications HTML, les navigateurs permettent leur omission lorsque la valeur d\'un attribut est composée d\'un certain sous-ensemble de caractères (alphanumérique et certains caractères de ponctuation).',
    'defer_javascript' => 'Différer JavaScript',
    'defer_javascript_description' => 'Découvre l\'exécution de JavaScript dans le HTML. Si nécessaire d\'annuler le déférences dans un script, utilisez des données de données de données de données comme attribut de script pour annuler le report.',
];
