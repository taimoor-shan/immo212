<?php

return [
    'cache_management' => 'Gestion du cache',
    'cache_management_description' => 'Effacer le cache pour rendre votre site à jour.',
    'cache_commands' => 'Commandes de cache effacer',
    'commands' => [
        'clear_cms_cache' => [
            'title' => 'Effacer tout le cache CMS',
            'description' => 'Effacer la mise en cache CMS: mise en cache de base de données, blocs statiques ... exécutez cette commande lorsque vous ne voyez pas les modifications après la mise à jour des données.',
            'success_msg' => 'Cache nettoyée',
        ],
        'refresh_compiled_views' => [
            'title' => 'Refreindre les vues compilées',
            'description' => 'Effacer les vues compilées pour rendre les vues à jour.',
            'success_msg' => 'Vue de cache rafraîchie',
        ],
        'clear_config_cache' => [
            'title' => 'Effacer le cache de configuration',
            'description' => 'Vous devrez peut-être actualiser la mise en cache de configuration lorsque vous changez quelque chose sur l\'environnement de production.',
            'success_msg' => 'Config Cache nettoyé',
        ],
        'clear_route_cache' => [
            'title' => 'Cache d\'itinéraire effacer',
            'description' => 'Effacer le routage du cache.',
            'success_msg' => 'Le cache d\'itinéraire a été nettoyé',
        ],
        'clear_log' => [
            'title' => 'Effacer le journal',
            'description' => 'Effacer les fichiers journaux système',
            'success_msg' => 'Le journal système a été nettoyé',
        ],
    ],
];
