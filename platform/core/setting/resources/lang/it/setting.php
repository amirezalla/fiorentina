<?php

return [
    'main_posts_limit' => 'Limite dei post principali',
    'min_main_posts_limit' => 'Limite dei post principali prima di altre notizie',
    'title' => 'Impostazioni',
    'general_setting' => 'Impostazioni generali',
    'menu' => 'Tutte le impostazioni',
    'email_setting_title' => 'Impostazioni email',
    'email_setting_description' => 'Configura le impostazioni email',
    'general' => [
        'theme' => 'Tema',
        'title' => 'Informazioni generali',
        'description' => 'Visualizza e aggiorna le informazioni del sito',
        'rich_editor' => 'Editor avanzato',
        'site_title' => 'Titolo del sito',
        'admin_email' => 'Email dell\'amministratore',
        'seo_block' => 'Configurazione SEO',
        'seo_title' => 'Titolo SEO',
        'seo_description' => 'Descrizione SEO',
        'webmaster_tools_block' => 'Strumenti Google Webmaster',
        'placeholder' => [
            'site_title' => 'Titolo del sito (massimo 120 caratteri)',
            'admin_email' => 'Email dell\'amministratore',
            'seo_title' => 'Titolo SEO (massimo 120 caratteri)',
            'seo_description' => 'Descrizione SEO (massimo 120 caratteri)',
            'google_analytics' => 'Google Analytics',
        ],
        'enable_send_error_reporting_via_email' => 'Abilitare l\'invio di report sugli errori via email?',
        'time_zone' => 'Fuso orario',
        'enable' => 'Abilita',
        'disable' => 'Disabilita',
        'enable_cache' => 'Abilitare la cache?',
        'disable_cache_in_the_admin_panel' => 'Disabilitare la cache nel pannello di amministrazione?',
        'disabled_helper' => 'Non è possibile disabilitare questo modello di email!',
        'cache_time' => 'Tempo di cache (minuti)',
        'enable_cache_site_map' => 'Abilitare la cache della mappa del sito?',
        'cache_time_site_map' => 'Tempo di cache della mappa del sito (minuti)',
        'admin_logo' => 'Logo dell\'amministrazione',
        'admin_favicon' => 'Favicon dell\'amministrazione',
        'admin_title' => 'Titolo dell\'amministrazione',
        'admin_title_placeholder' => 'Titolo mostrato nella scheda del browser',
        'admin_appearance_title' => 'Aspetto dell\'amministrazione',
        'admin_appearance_description' => 'Impostazioni dell\'aspetto dell\'amministrazione come editor, lingua...',
        'seo_block_description' => 'Imposta il titolo del sito, la meta descrizione del sito, le parole chiave per ottimizzare la SEO',
        'webmaster_tools_description' => 'Gli strumenti Google Webmaster (GWT) sono software gratuiti che aiutano a gestire il lato tecnico del tuo sito web',
        'yes' => 'Sì',
        'no' => 'No',
        'show_on_front' => 'La tua homepage mostra',
        'select' => '— Seleziona —',
        'show_site_name' => 'Mostrare il nome del sito dopo il titolo della pagina, separato con "-"?',
        'locale' => 'Lingua del sito',
        'locale_direction' => 'Direzione della lingua del sito',
        'minutes' => 'minuti',
        'redirect_404_to_homepage' => 'Reindirizzare tutte le richieste non trovate alla homepage?',
    ],
    'admin_appearance' => [
        'title' => 'Aspetto dell\'amministrazione',
        'description' => 'Visualizza e aggiorna logo, favicon, layout,...',
        'layout' => 'Layout',
        'horizontal' => 'Orizzontale',
        'vertical' => 'Verticale',
        'show_menu_item_icon' => 'Mostrare l\'icona degli elementi del menu?',
        'language' => 'Lingua nel pannello di amministrazione',
        'theme_mode' => 'Modalità tema',
        'dark' => 'Scuro',
        'light' => 'Chiaro',
        'container_width' => [
            'title' => 'Larghezza del contenitore',
            'default' => 'Predefinito',
            'large' => 'Grande',
            'full' => 'Completo',
        ],
        'form' => [
            'admin_logo' => 'Logo dell\'amministrazione',
            'admin_favicon' => 'Favicon dell\'amministrazione',
            'admin_title' => 'Titolo dell\'amministrazione',
            'admin_title_placeholder' => 'Titolo mostrato nella scheda del browser',
            'admin_login_screen_backgrounds' => 'Sfondo della schermata di accesso (~1366x768)',
            'admin_locale_direction' => 'Direzione della lingua dell\'amministrazione',
            'rich_editor' => 'Editor avanzato',
            'show_admin_bar' => 'Mostrare la barra di amministrazione (Quando l\'amministratore è connesso, mostra comunque la barra di amministrazione nel sito web)?',
            'show_guidelines' => 'Mostrare le linee guida?',
            'primary_font' => 'Carattere primario',
            'primary_color' => 'Colore primario',
            'secondary_color' => 'Colore secondario',
            'heading_color' => 'Colore delle intestazioni',
            'text_color' => 'Colore del testo',
            'link_color' => 'Colore dei link',
            'link_hover_color' => 'Colore dei link al passaggio del mouse',
            'show_menu_item_icon' => 'Mostrare l\'icona degli elementi del menu?',
            'custom_css' => 'CSS personalizzato',
            'custom_js' => 'JS personalizzato',
            'custom_header_js' => 'JS nell\'header',
            'custom_header_js_placeholder' => 'JS nell\'header della pagina, racchiudi nel tag &#x3C;script&#x3E;&#x3C;/script&#x3E;',
            'custom_body_js' => 'JS nel corpo',
            'custom_body_js_placeholder' => 'JS nel corpo della pagina, racchiudi nel tag &#x3C;script&#x3E;&#x3C;/script&#x3E;',
            'custom_footer_js' => 'JS nel footer',
            'custom_footer_js_placeholder' => 'JS nel footer della pagina, racchiudi nel tag &#x3C;script&#x3E;&#x3C;/script&#x3E;',
        ],
    ],
    'datatable' => [
        'title' => 'Datatables',
        'description' => 'Impostazioni per le datatables',
        'form' => [
            'show_column_visibility' => 'Mostrare di default la visibilità delle colonne?',
            'show_export_button' => 'Mostrare di default il pulsante di esportazione?',
            'pagination_type' => 'Tipo di paginazione',
            'default' => 'Predefinito',
            'dropdown' => 'Dropdown',
        ],
    ],
    'email' => [
        'subject' => 'Oggetto',
        'content' => 'Contenuto',
        'title' => 'Impostazioni per il modello email',
        'description' => 'Modello email utilizzando HTML e variabili di sistema.',
        'reset_to_default' => 'Ripristina impostazioni predefinite',
        'back' => 'Torna alle impostazioni',
        'reset_success' => 'Ripristinato con successo alle impostazioni predefinite',
        'confirm_reset' => 'Confermare il ripristino del modello email?',
        'confirm_message' => 'Vuoi davvero ripristinare questo modello email ai valori predefiniti?',
        'continue' => 'Continua',
        'sender_name' => 'Nome del mittente',
        'sender_name_placeholder' => 'Nome',
        'sender_email' => 'Email del mittente',
        'mailer' => 'Mailer',
        'port' => 'Porta',
        'port_placeholder' => 'Es: 587',
        'host' => 'Host',
        'host_placeholder' => 'Es: smtp.gmail.com',
        'username' => 'Nome utente',
        'username_placeholder' => 'Nome utente per accedere al server di posta',
        'password' => 'Password',
        'password_placeholder' => 'Password per accedere al server di posta',
        'encryption' => 'Crittografia',
        'mail_gun_domain' => 'Dominio',
        'mail_gun_domain_placeholder' => 'Dominio',
        'mail_gun_secret' => 'Segreto',
        'mail_gun_secret_placeholder' => 'Segreto',
        'mail_gun_endpoint' => 'Endpoint',
        'mail_gun_endpoint_placeholder' => 'Endpoint',
        'log_channel' => 'Canale di log',
        'sendmail_path' => 'Percorso Sendmail',
        'encryption_placeholder' => 'Crittografia: ssl o tls',

        'ses_key' => 'Chiave',
        'ses_key_placeholder' => 'Chiave',
        'ses_secret' => 'Segreto',
        'ses_secret_placeholder' => 'Segreto',
        'ses_region' => 'Regione',
        'ses_region_placeholder' => 'Regione',

        'postmark_token' => 'Token',
        'postmark_token_placeholder' => 'Token',

        'email_templates' => 'Modelli email',
        'email_templates_description' => 'Modelli email utilizzando HTML e variabili di sistema.',
        'email_rules' => 'Regole email',
        'email_rules_description' => 'Configura le regole di validazione delle email',
        'base_template' => 'Modello base',
        'base_template_description' => 'Modello base per tutte le email',
        'template_header' => 'Header del modello email',
        'template_header_description' => 'Modello per l\'header delle email',
        'template_footer' => 'Footer del modello email',
        'template_footer_description' => 'Modello per il footer delle email',
        'default' => 'Predefinito',
        'template_off_status_helper' => 'Questo modello email è disattivato.',
        'blacklist_email_domains' => 'Domini email nella blacklist',
        'blacklist_email_domains_helper' => 'Inserisci un elenco di domini email da inserire nella blacklist. Es: gmail.com, yahoo.com.',
        'blacklist_specified_emails' => 'Indirizzi email nella blacklist',
        'blacklist_specified_emails_helper' => 'Inserisci un elenco di indirizzi email specifici da inserire nella blacklist. Es: mail@example.com.',
        'exception_emails' => 'Email in eccezione',
        'exception_emails_helper' => 'Queste email saranno escluse dalle regole di validazione.',
        'email_rules_strict' => 'Validazione email rigorosa',
        'email_rules_strict_helper' => 'Esegui una validazione email simile a quella RFC con regole rigorose.',
        'email_rules_dns' => 'Validazione DNS',
        'email_rules_dns_helper' => 'Verifica se ci sono record DNS che indicano che il server accetta email.',
        'email_rules_spoof' => 'Rilevamento spoofing',
        'email_rules_spoof_helper' => 'Rileva tentativi di spoofing email.',
        'template_turn_off' => 'Clicca per disattivare questo modello email',
        'template_turn_on' => 'Clicca per attivare questo modello email',
        'turn_on_success_message' => 'Modello email attivato con successo!',
        'turn_off_success_message' => 'Modello email disattivato con successo!',
        'email_template_status' => 'Stato del modello email',
        'email_template_status_description' => 'Attiva/Disattiva modello email',
    ],
    'media' => [
        'title' => 'Media',
        'driver' => 'Driver',
        'description' => 'Impostazioni per i media',
        'aws_access_key_id' => 'AWS Access Key ID',
        'aws_secret_key' => 'AWS Secret Key',
        'aws_default_region' => 'AWS Default Region',
        'aws_bucket' => 'AWS Bucket',
        'aws_url' => 'AWS URL',
        'aws_endpoint' => 'AWS Endpoint (Opzionale)',
        'r2_access_key_id' => 'R2 Access Key ID',
        'r2_secret_key' => 'R2 Secret Key',
        'r2_bucket' => 'R2 Bucket',
        'r2_url' => 'R2 URL',
        'r2_endpoint' => 'R2 Endpoint',
        'do_spaces_access_key_id' => 'DO Spaces Access Key ID',
        'do_spaces_secret_key' => 'DO Spaces Secret Key',
        'do_spaces_default_region' => 'DO Spaces Default Region',
        'do_spaces_bucket' => 'DO Spaces Bucket',
        'do_spaces_endpoint' => 'DO Spaces Endpoint',
        'do_spaces_cdn_enabled' => 'DO Spaces CDN è abilitato?',
        'media_do_spaces_cdn_custom_domain' => 'Dominio personalizzato DO Spaces CDN',
        'media_do_spaces_cdn_custom_domain_placeholder' => 'https://tuo-dominio-personalizzato.com',
        'wasabi_access_key_id' => 'Wasabi Access Key ID',
        'wasabi_secret_key' => 'Wasabi Secret Key',
        'wasabi_default_region' => 'Wasabi Default Region',
        'wasabi_bucket' => 'Wasabi Bucket',
        'wasabi_root' => 'Wasabi Root',
        'default_placeholder_image' => 'Immagine segnaposto predefinita',
        'enable_chunk' => 'Abilitare il caricamento per dimensioni chunk?',
        'chunk_size' => 'Dimensione chunk (Bytes)',
        'chunk_size_placeholder' => 'Predefinito: 1048576 ~ 1MB',
        'max_file_size' => 'Dimensione massima del file (MB)',
        'max_file_size_placeholder' => 'Predefinito: 1048576 ~ 1GB',
        'enable_watermark' => 'Abilitare filigrana?',
        'watermark_source' => 'Immagine della filigrana',
        'watermark_size' => 'Dimensione della filigrana (%)',
        'watermark_size_placeholder' => 'Predefinito: 10 (%)',
        'watermark_opacity' => 'Opacità della filigrana (%)',
        'watermark_opacity_placeholder' => 'Predefinito: 70 (%)',
        'watermark_position' => 'Posizione della filigrana',
        'watermark_position_x' => 'Posizione X della filigrana',
        'watermark_position_y' => 'Posizione Y della filigrana',
        'watermark_position_top_left' => 'In alto a sinistra',
        'watermark_position_top_right' => 'In alto a destra',
        'watermark_position_bottom_left' => 'In basso a sinistra',
        'watermark_position_bottom_right' => 'In basso a destra',
        'watermark_position_center' => 'Centro',
        'turn_off_automatic_url_translation_into_latin' => 'Disabilita la traduzione automatica degli URL in latino',
        'bunnycdn_hostname' => 'Hostname',
        'bunnycdn_zone' => 'Nome della zona (Il nome della tua zona di archiviazione)',
        'bunnycdn_key' => 'Password di accesso FTP e API (La password di accesso all\'API della zona di archiviazione)',
        'bunnycdn_region' => 'Regione (La regione della zona di archiviazione)',
        'optional' => 'Opzionale',
        'sizes' => 'Dimensioni delle miniature dei media',
        'media_sizes_helper' => 'Imposta larghezza o altezza a 0 se vuoi ritagliare solo per larghezza o altezza. Devi cliccare su "Genera miniature" per applicare le modifiche.',
        'width' => 'Larghezza',
        'height' => 'Altezza',
        'default_size_value' => 'Predefinito: :size',
        'all' => 'Tutti',
        'all_helper_text' => 'Se deselezioni tutte le cartelle, verrà applicato a tutte le cartelle.',
        'media_folders_can_add_watermark' => 'Aggiungi filigrana per le immagini nelle cartelle:',
        'max_upload_filesize' => 'Dimensione massima del file di caricamento (MB)',
        'max_upload_filesize_placeholder' => 'Predefinito: :size, deve essere inferiore a :size.',
        'max_upload_filesize_helper' => 'Il tuo server permette di caricare file fino a un massimo di :size, puoi modificare questo valore per limitare la dimensione dei file di caricamento.',
        'image_processing_library' => 'Libreria di elaborazione immagini',
        'use_original_name_for_file_path' => 'Usa il nome originale per il percorso del file',
    ],
    'license' => [
        'purchase_code' => 'Codice di acquisto',
        'buyer' => 'Acquirente',
    ],
    'field_type_not_exists' => 'Questo tipo di campo non esiste',
    'save_settings' => 'Salva impostazioni',
    'template' => 'Modello',
    'description' => 'Descrizione',
    'enable' => 'Abilita',
    'send' => 'Invia',
    'test_email_description' => 'Per inviare un\'email di prova, assicurati di aver aggiornato la configurazione per inviare email!',
    'test_email_input_placeholder' => 'Inserisci l\'email a cui vuoi inviare l\'email di prova.',
    'test_email_modal_title' => 'Invia un\'email di prova',
    'test_send_mail' => 'Invia email di prova',
    'test_email_send_success' => 'Email inviata con successo!',
    'locale_direction_ltr' => 'Da sinistra a destra',
    'locale_direction_rtl' => 'Da destra a sinistra',
    'emails_warning' => 'Puoi aggiungere un massimo di :count email',
    'email_add_more' => 'Aggiungi altro',
    'generate' => 'Genera',
    'generate_thumbnails' => 'Genera miniature',
    'generate_thumbnails_success' => 'Miniature generate con successo. :count file sono stati generati!',
    'generate_thumbnails_error' => 'Impossibile rigenerare le miniature per questi file :count file!',
    'generate_thumbnails_description' => 'Sei sicuro di voler rigenerare le miniature per tutte le immagini? Ci vorrà del tempo, quindi NON lasciare questa pagina, attendi fino al completamento.',
    'enable_chunk_description' => 'Il caricamento delle dimensioni dei chunk viene utilizzato per caricare file di grandi dimensioni.',
    'watermark_description' => 'ATTENZIONE: La filigrana viene aggiunta solo alle immagini caricate di recente, non verrà aggiunta alle immagini esistenti. Disabilitare la filigrana non rimuoverà la filigrana dalle immagini esistenti.',
    'submit' => 'Invia',
    'back' => 'Indietro',
    'enter_sample_value' => 'Inserisci valori di esempio per il test',
    'preview' => 'Anteprima',
    'media_size_width' => 'La larghezza della dimensione :size deve essere maggiore di 0',
    'media_size_height' => 'L\'altezza della dimensione :size deve essere maggiore di 0',
    'cronjob' => [
        'name' => 'Cronjob',
        'description' => 'Il Cronjob ti consente di automatizzare determinati comandi o script sul tuo sito.',
        'is_not_ready' => 'Per eseguire il cronjob, segui le istruzioni di seguito.',
        'is_working' => 'Congratulazioni! Il tuo cronjob è in esecuzione.',
        'is_not_working' => "Il tuo cronjob non è in esecuzione. Controlla il cronjob del tuo server.",
        'last_checked' => 'Ultima verifica a :time.',
        'copy_button' => 'Copia',
        'setup' => [
            'name' => 'Impostazione del Cronjob',
            'connect_to_server' => 'Collegati al tuo server tramite SSH o qualsiasi altro metodo preferito.',
            'open_crontab' => 'Apri il file crontab utilizzando un editor di testo (ad esempio, `crontab -e`).',
            'add_cronjob' => 'Aggiungi il comando sopra nel file crontab e salvalo.',
            'done' => 'Il cronjob ora verrà eseguito ogni minuto ed eseguirà il comando specificato.',
            'learn_more' => 'Puoi saperne di più sul cronjob dalla documentazione di Laravel :documentation',
            'documentation' => 'documentazione',
            'copied' => 'Copiato',
        ],
    ],
    'cache' => [
        'title' => 'Cache',
        'description' => 'Configura la cache del sistema per ottimizzare la velocità',
        'form' => [
            'enable_cache' => 'Abilitare la cache?',
            'cache_time' => 'Tempo di cache (minuti)',
            'disable_cache_in_the_admin_panel' => 'Disabilitare la cache nel pannello di amministrazione?',
            'cache_admin_menu' => 'Cache menu amministratore?',
            'enable_cache_site_map' => 'Abilitare la cache della mappa del sito?',
            'cache_time_site_map' => 'Tempo di cache della mappa del sito (minuti)',
        ],
    ],
    'appearance' => [
        'title' => 'Aspetto',
    ],
    'panel' => [
        'common' => 'Comune',
        'general' => 'Generale',
        'general_description' => 'Visualizza e aggiorna le tue impostazioni generali e attiva la licenza',
        'email' => 'Email',
        'email_description' => 'Visualizza e aggiorna le tue impostazioni email e i modelli email',
        'media' => 'Media',
        'media_description' => 'Visualizza e aggiorna le tue impostazioni media',
        'system' => 'Sistema',
        'system_updater' => 'Aggiornamento del sistema',
        'system_updater_description' => 'Aggiorna il tuo sistema all\'ultima versione',
        'others' => 'Altri',
    ],
    'saving' => 'Salvataggio in corso...',
    'generating_media_thumbnails' => 'Generazione miniature dei media in corso...',
    'website_tracking' => [
        'title' => 'Monitoraggio del sito web',
        'description' => 'Configura il monitoraggio del sito web',
        'google_tag_manager_id' => 'ID Google Tag Manager',
        'google_tag_manager_id_placeholder' => 'Esempio: G-123ABC4567',
        'google_tag_manager_code' => 'Codice Google Tag Manager',
        'google_tag_manager_code_helper' => 'Vai su https://tagmanager.google.com per configurare i tuoi tag.',
        'google_tag_manager_info' => 'Imposta solo uno tra ID Google Tag Manager o Codice Google Tag Manager',
    ],
];
