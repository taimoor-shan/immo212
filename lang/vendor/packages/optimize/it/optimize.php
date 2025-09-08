<?php

return [
    'settings' => [
        'title' => 'Ottimizza',
        'description' => 'Minimizza l\'output HTML, incorpora i CSS, rimuovi i commenti...',
        'enable' => 'Abilitare l’ottimizzazione della velocità della pagina?',
    ],
    'collapse_white_space' => 'Comprimi gli spazi bianchi',
    'collapse_white_space_description' => 'Questo filtro riduce i byte trasmessi in un file HTML rimuovendo gli spazi bianchi non necessari.',
    'elide_attributes' => 'Elidi attributi',
    'elide_attributes_description' => 'Questo filtro riduce la dimensione di trasferimento dei file HTML rimuovendo gli attributi dai tag quando il valore specificato è uguale al valore predefinito per quell\'attributo. Questo può far risparmiare un modesto numero di byte e può rendere il documento più comprimibile, rendendo canonici i tag interessati.',
    'inline_css' => 'CSS inline',
    'inline_css_description' => 'Questo filtro trasforma l\'attributo "style" inline dei tag in classi spostando il CSS nell\'intestazione.',
    'insert_dns_prefetch' => 'Inserisci il prefetch DNS',
    'insert_dns_prefetch_description' => 'Questo filtro inserisce tag nell\'HEAD per consentire al browser di effettuare il prefetching DNS.',
    'remove_comments' => 'Rimuovi commenti',
    'remove_comments_description' => 'Questo filtro elimina i commenti HTML, JS e CSS. Il filtro riduce la dimensione dei file HTML trasferiti rimuovendo i commenti. A seconda del file HTML, questo filtro può ridurre significativamente il numero di byte trasmessi sulla rete.',
    'remove_quotes' => 'Rimuovi virgolette',
    'remove_quotes_description' => 'Questo filtro elimina le virgolette non necessarie dagli attributi HTML. Sebbene siano richieste dalle varie specifiche HTML, i browser ne consentono l’omissione quando il valore di un attributo è composto da un determinato sottoinsieme di caratteri (alfanumerici e alcuni caratteri di punteggiatura).',
    'defer_javascript' => 'Rinvia javascript',
    'defer_javascript_description' => 'Rinvia l\'esecuzione di JavaScript nell\'HTML. Se necessario annullare il rinvio in qualche script, utilizzare l\'attributo data-pagespeed-no-defer nello script per annullare il rinvio.',
];
