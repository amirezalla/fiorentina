<?php



return [
    'common' => [
        'name' => 'Nome',
        'email' => 'Email',
        'website' => 'Sito web',
        'comment' => 'Commento',
    ],
    'sort_options'=>[
        [
            'key'=>"latest",
            'title'=>"Ultime"
        ],
        [
            'key'=>"oldest",
            'title'=>"il più vecchio"
        ],
    ],
    'title' => 'Commenti',
    'author' => 'Autore',
    'responsed_to' => 'Risposta a',
    'permalink' => 'Permalink',
    'url' => 'URL',
    'submitted_on' => 'Inviato il',
    'edit_comment' => 'Modifica Commento',
    'reply' => 'Rispondi',
    'in_reply_to' => 'In risposta a :name',

    'reply_modal' => [
        'title' => 'Rispondi a :comment',
        'cancel' => 'Annulla',
    ],

    'allow_comments' => 'Permetti i commenti',

    'front' => [
        'admin_badge' => 'Admin',

        'list' => [
            'title' => ':count commento|:count commenti',
            'reply' => 'Rispondi',
            'reply_to' => 'Rispondi a :name',
            'cancel_reply' => 'Annulla risposta',
            'waiting_for_approval_message' => 'Il tuo commento è in attesa di moderazione. Questo è un anteprima, il tuo commento sarà visibile dopo l’approvazione.',
        ],

        'form' => [
            'title' => 'Lascia un commento',
            'description' => '',
            'cookie_consent' => 'Salva il mio nome, email e sito web in questo browser per la prossima volta che commenterò.',
            'submit' => 'Pubblica Commento',
        ],

        'comment_success_message' => 'Il tuo commento è stato inviato con successo.',
    ],

    'enums' => [
        'statuses' => [
            'pending' => 'In attesa',
            'approved' => 'Approvato',
            'spam' => 'Spam',
            'trash' => 'Cestino',
        ],
    ],

    'settings' => [
        'title' => 'FOB Comment',
        'description' => 'Configura le impostazioni per FOB Comment',

        'form' => [
            'enable_recaptcha' => 'Abilita reCAPTCHA',
            'enable_recaptcha_help' => 'Devi abilitare reCAPTCHA in :url per usare questa funzione.',
            'captcha_setting_label' => 'Impostazioni Captcha',
            'comment_moderation' => 'I commenti devono essere approvati manualmente',
            'comment_moderation_help' => 'Tutti i commenti devono essere approvati manualmente da un admin prima di essere visualizzati sul frontend.',
            'show_comment_cookie_consent' => 'Mostra la casella di consenso dei cookie per i commenti, permettendo ai visitatori di salvare le loro informazioni nel browser',
            'auto_fill_comment_form' => 'Riempi automaticamente il modulo dei commenti per gli utenti registrati',
            'auto_fill_comment_form_help' => 'Il modulo dei commenti sarà riempito automaticamente con i dati dell’utente, come nome completo, email, ecc., se è loggato.',
            'comment_order' => 'Ordina i commenti per',
            'comment_order_help' => 'Scegli l’ordine preferito per visualizzare i commenti nella lista.',
            'comment_order_choices' => [
                'asc' => 'Più vecchi',
                'desc' => 'Più recenti',
            ],
            'display_admin_badge' => 'Mostra il badge admin per i commenti dell’admin',
        ],
    ],
];
