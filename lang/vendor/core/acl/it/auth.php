<?php

return [
    'login' => [
        'username' => 'Email/Nome utente',
        'title' => 'Accesso utente',
        'remember' => 'Ricordami?',
        'login' => 'Accedi',
        'placeholder' => [
            'username' => 'Inserisci il tuo nome utente o indirizzo email',
            'email' => 'Inserisci il tuo indirizzo email',
            'password' => 'Inserisci la tua password',
        ],
        'success' => 'Accesso effettuato con successo!',
        'fail' => 'Nome utente o password errati.',
        'not_active' => 'Il tuo account non è ancora stato attivato!',
        'banned' => 'Questo account è stato bannato.',
        'logout_success' => 'Disconnessione effettuata con successo!',
        'dont_have_account' => 'Non hai un account su questo sistema, contatta l\'amministratore per maggiori informazioni!',
    ],
    'forgot_password' => [
        'title' => 'Password dimenticata',
        'message' => '<p>Hai dimenticato la tua password?</p><p>Inserisci il tuo indirizzo email. Il sistema invierà un\'email con un link attivo per reimpostare la tua password.</p>',
        'submit' => 'Invia',
    ],
    'reset' => [
        'new_password' => 'Nuova password',
        'password_confirmation' => 'Conferma la nuova password',
        'title' => 'Reimposta la tua password',
        'update' => 'Aggiorna',
        'wrong_token' => 'Questo link non è valido o è scaduto. Provi a utilizzare nuovamente il modulo di reimpostazione.',
        'user_not_found' => 'Questo nome utente non esiste.',
        'success' => 'Reimpostazione della password riuscita!',
        'fail' => 'Il token non è valido, il link per reimpostare la password è scaduto!',
        'reset' => [
            'title' => 'Email reimposta password',
        ],
        'send' => [
            'success' => 'È stata inviata un\'email al suo account di posta elettronica. La preghiamo di controllare e completare questa operazione.',
            'fail' => 'Impossibile inviare l\'email in questo momento. Si prega di riprovare più tardi.',
        ],
        'new-password' => 'Nuova password',
        'placeholder' => [
            'new_password' => 'Inserisci la tua nuova password',
            'new_password_confirmation' => 'Conferma la tua nuova password',
        ],
    ],
    'email' => [
        'reminder' => [
            'title' => 'Email reimposta password',
        ],
    ],
    'password_confirmation' => 'Conferma password',
    'failed' => 'Non riuscito',
    'throttle' => 'Acceleratore',
    'not_member' => 'Non sei ancora membro?',
    'register_now' => 'Registrati ora',
    'lost_your_password' => 'Hai perso la password?',
    'login_title' => 'Amministratore',
    'login_via_social' => 'Accedi con i social network',
    'back_to_login' => 'Torna alla pagina di accesso',
    'sign_in_below' => 'Accedi qui sotto',
    'languages' => 'Lingue',
    'reset_password' => 'Reimposta password',
    'settings' => [
        'email' => [
            'description' => 'Configurazione email ACL',
            'templates' => [
                'password_reminder' => [
                    'title' => 'Reimposta password',
                    'description' => 'Invia un\'email all\'utente quando viene richiesta la reimpostazione della password',
                    'subject' => 'Reimposta password',
                    'reset_link' => 'Link per reimpostare la password',
                ],
            ],
        ],
    ],
];
