<?php

/*
| Traduzioni dei singoli tool (chiave: tools.{nome_tool}.{chiave}).
| Popolato man mano che i tool della Fase 1 vengono implementati.
*/

return [
    'subnet_calculator' => [
        'title'       => 'Calcolatore Subnet (IPv4)',
        'description' => 'Calcola rete, broadcast, range host e maschere a partire da un indirizzo IP e dal prefisso CIDR.',
        'menu'        => 'Subnet Calculator',

        'input_ip'    => 'Indirizzo IP',
        'input_cidr'  => 'Prefisso CIDR',
        'calculate'   => 'Calcola',
        'placeholder_ip' => 'es. 192.168.1.10',

        'result_title'     => 'Risultato',
        'result_network'   => 'Indirizzo di rete',
        'result_broadcast' => 'Broadcast',
        'result_netmask'   => 'Maschera di rete',
        'result_wildcard'  => 'Wildcard',
        'result_host_min'  => 'Primo host',
        'result_host_max'  => 'Ultimo host',
        'result_usable'    => 'Host utilizzabili',
        'result_total'     => 'Indirizzi totali',
        'result_class'     => 'Classe',
        'result_type'      => 'Tipo',
        'type_private'     => 'Privato',
        'type_public'      => 'Pubblico',

        'empty'       => 'Inserisci un indirizzo IP e un prefisso CIDR per vedere il risultato.',

        'error_ip_required'   => "L'indirizzo IP è obbligatorio.",
        'error_ip_invalid'    => "L'indirizzo IP non è un IPv4 valido.",
        'error_cidr_required' => 'Il prefisso CIDR è obbligatorio.',
        'error_cidr_invalid'  => 'Il prefisso CIDR deve essere un numero intero.',
        'error_cidr_range'    => 'Il prefisso CIDR deve essere compreso tra 0 e 32.',
    ],

    'ipv6_calculator' => [
        'title'       => 'Calcolatore Subnet IPv6',
        'description' => 'Calcola rete, primo e ultimo indirizzo, forma estesa/compressa e numero di indirizzi da un IPv6 e dal prefisso.',
        'menu'        => 'IPv6 Subnet Calculator',

        'input_address'   => 'Indirizzo IPv6',
        'input_prefix'    => 'Lunghezza prefisso',
        'calculate'       => 'Calcola',
        'placeholder_address' => 'es. 2001:db8::1',

        'result_title'        => 'Risultato',
        'result_compressed'   => 'Forma compressa',
        'result_expanded'     => 'Forma estesa',
        'result_network'      => 'Rete',
        'result_network_expanded' => 'Rete (estesa)',
        'result_first'        => 'Primo indirizzo',
        'result_last'         => 'Ultimo indirizzo',
        'result_total'        => 'Indirizzi totali',
        'result_type'         => 'Tipo',

        'type_global_unicast' => 'Global Unicast',
        'type_link_local'     => 'Link-Local',
        'type_unique_local'   => 'Unique Local',
        'type_multicast'      => 'Multicast',
        'type_loopback'       => 'Loopback',
        'type_unspecified'    => 'Non specificato',

        'empty'       => 'Inserisci un indirizzo IPv6 e un prefisso per vedere il risultato.',

        'error_address_required' => "L'indirizzo IPv6 è obbligatorio.",
        'error_address_invalid'  => "L'indirizzo IPv6 non è valido.",
        'error_prefix_required'  => 'La lunghezza del prefisso è obbligatoria.',
        'error_prefix_invalid'   => 'La lunghezza del prefisso deve essere un numero intero.',
        'error_prefix_range'     => 'La lunghezza del prefisso deve essere compresa tra 0 e 128.',
    ],

    'port_reference' => [
        'title'       => 'Riferimento Porte (TCP/UDP)',
        'description' => 'Cerca tra le porte note per numero, servizio o protocollo.',
        'menu'        => 'Port Reference',

        'search_placeholder' => 'Cerca per numero o servizio (es. 443, SSH, MySQL)…',
        'protocol_label'     => 'Protocollo',
        'protocol_all'       => 'Tutti',
        'protocol_tcp'       => 'TCP',
        'protocol_udp'       => 'UDP',

        'col_port'        => 'Porta',
        'col_protocol'    => 'Protocollo',
        'col_service'     => 'Servizio',
        'col_description' => 'Descrizione',

        'count'       => ':count porte',
        'no_results'  => 'Nessuna porta corrisponde ai criteri di ricerca.',
    ],

    'cable_schemas' => [
        'title'       => 'Schemi Cavi di Rete',
        'description' => 'Standard di cablaggio RJ45: T568A, T568B, cavo dritto, incrociato e rollover.',
        'menu'        => 'Schemi Cavi',

        'section_standards' => 'Standard T568A e T568B',
        'section_cables'    => 'Tipi di Cavo',

        'col_pin'    => 'Pin',
        'col_color'  => 'Colore',

        'end_a' => 'Estremo A',
        'end_b' => 'Estremo B',

        'cable_straight'  => 'Cavo Dritto (Straight-Through)',
        'cable_crossover' => 'Cavo Incrociato (Crossover)',
        'cable_rollover'  => 'Cavo Rollover (Console)',

        'cable_straight_use'  => 'PC ↔ Switch, PC ↔ Hub, Switch ↔ Router',
        'cable_crossover_use' => 'PC ↔ PC, Switch ↔ Switch, Router ↔ Router',
        'cable_rollover_use'  => 'PC ↔ porta Console router/switch (Cisco)',

        'note_mdix' => 'I dispositivi moderni con Auto-MDI/MDI-X rilevano automaticamente il tipo di cavo — il crossover è necessario solo con apparati più vecchi.',
    ],

    'cidr_cheatsheet' => [
        'title'       => 'CIDR Cheat Sheet',
        'description' => 'Tabella di riferimento rapido per tutti i prefissi IPv4 da /0 a /32 con netmask, wildcard e numero di host.',
        'menu'        => 'CIDR Cheat Sheet',

        'col_cidr'    => 'Prefisso',
        'col_netmask' => 'Netmask',
        'col_wildcard'=> 'Wildcard',
        'col_total'   => 'Indirizzi totali',
        'col_usable'  => 'Host utilizzabili',
        'col_note'    => 'Note',
    ],

    'vlan_calculator' => [
        'title'       => 'VLAN Calculator',
        'description' => 'Suddividi una rete base in subnet di pari dimensione e assegna VLAN ID progressivi.',
        'menu'        => 'VLAN Calculator',

        'input_network'     => 'Rete base',
        'input_base_cidr'   => 'Prefisso rete base',
        'input_subnet_cidr' => 'Prefisso subnet (VLAN)',
        'input_start_vlan'  => 'VLAN ID iniziale',
        'placeholder_network' => 'es. 10.0.0.0',
        'calculate'         => 'Calcola',

        'result_title'        => 'Allocazione VLAN',
        'result_base'         => 'Rete base',
        'result_total'        => 'Subnet totali',
        'result_shown'        => 'Mostrate',
        'result_truncated'    => 'Risultati limitati ai primi :max — la rete contiene :total subnet in totale.',

        'col_vlan'      => 'VLAN ID',
        'col_network'   => 'Subnet',
        'col_gateway'   => 'Gateway',
        'col_broadcast' => 'Broadcast',
        'col_netmask'   => 'Netmask',
        'col_usable'    => 'Host',

        'empty' => 'Inserisci i parametri e premi Calcola per vedere l\'allocazione.',

        'error_network_required'     => 'L\'indirizzo di rete è obbligatorio.',
        'error_network_invalid'      => 'L\'indirizzo di rete non è un IPv4 valido.',
        'error_base_cidr_required'   => 'Il prefisso della rete base è obbligatorio.',
        'error_base_cidr_range'      => 'Il prefisso della rete base deve essere tra 0 e 30.',
        'error_subnet_cidr_required' => 'Il prefisso subnet è obbligatorio.',
        'error_subnet_cidr_range'    => 'Il prefisso subnet deve essere tra 1 e 32.',
        'error_subnet_too_small'     => 'Il prefisso subnet deve essere maggiore del prefisso della rete base.',
        'error_start_vlan_required'  => 'Il VLAN ID iniziale è obbligatorio.',
        'error_start_vlan_range'     => 'Il VLAN ID iniziale deve essere tra 1 e 4094.',
    ],

    'dns_lookup' => [
        'title'       => 'DNS Lookup',
        'description' => 'Interroga i record DNS di un hostname o indirizzo IP (A, AAAA, MX, NS, TXT, CNAME, SOA, PTR).',
        'menu'        => 'DNS Lookup',

        'input_host'  => 'Hostname o indirizzo IP',
        'input_type'  => 'Tipo di record',
        'lookup'      => 'Risolvi',
        'placeholder_host' => 'es. example.com oppure 8.8.8.8',

        'result_title'   => 'Risultato',
        'result_host'    => 'Query',
        'result_type'    => 'Tipo',
        'result_count'   => ':count record trovati',
        'result_none'    => 'Nessun record trovato per questa query.',

        'col_type'    => 'Tipo',
        'col_ttl'     => 'TTL',
        'col_data'    => 'Dati',

        'empty' => 'Inserisci un hostname o IP e scegli il tipo di record.',

        'error_host_required' => 'L\'hostname è obbligatorio.',
        'error_host_invalid'  => 'Inserisci un hostname o indirizzo IP valido.',
        'error_host_too_long' => 'L\'hostname non può superare 253 caratteri.',
        'error_type_required' => 'Il tipo di record è obbligatorio.',
        'error_type_invalid'  => 'Il tipo di record selezionato non è valido.',
        'error_throttle'      => 'Troppe richieste: limite di 60 query al secondo raggiunto. Riprova tra un momento.',
    ],

    'ip_geolocation' => [
        'title'       => 'IP Geolocation',
        'description' => 'Ottieni informazioni geografiche e di rete per un indirizzo IPv4 o IPv6.',
        'menu'        => 'IP Geolocation',

        'input_ip'    => 'Indirizzo IP',
        'placeholder' => 'es. 8.8.8.8 oppure 2001:db8::1',
        'lookup'      => 'Cerca',

        'result_title'    => 'Risultato',
        'field_ip'        => 'Indirizzo IP',
        'field_country'   => 'Paese',
        'field_region'    => 'Regione',
        'field_city'      => 'Città',
        'field_zip'       => 'CAP / ZIP',
        'field_coords'    => 'Coordinate',
        'field_timezone'  => 'Fuso orario',
        'field_isp'       => 'ISP',
        'field_org'       => 'Organizzazione',
        'field_as'        => 'AS Number',

        'open_map'  => 'Apri mappa',
        'empty'     => 'Inserisci un indirizzo IP per vedere le informazioni geografiche.',
        'no_coords' => 'Coordinate non disponibili',

        'error_ip_required'      => "L'indirizzo IP è obbligatorio.",
        'error_ip_invalid'       => "Inserisci un indirizzo IPv4 o IPv6 valido.",
        'error_api_unavailable'  => "Il servizio di geolocalizzazione non è raggiungibile. Riprova più tardi.",
    ],

    'osi_model' => [
        'title'       => 'Modello OSI — Riferimento',
        'description' => 'I 7 livelli del modello OSI con protocolli, unità dati (PDU) e dispositivi associati.',
        'menu'        => 'Modello OSI',

        'col_layer'     => 'Livello',
        'col_name'      => 'Nome',
        'col_pdu'       => 'PDU',
        'col_protocols' => 'Protocolli / Standard',
        'col_devices'   => 'Dispositivi',
        'col_desc'      => 'Descrizione',

        'layer_7_application_name' => 'Applicazione',
        'layer_7_application_desc' => 'Interfaccia diretta con le applicazioni utente. Fornisce servizi di rete come trasferimento file, email e navigazione web.',

        'layer_6_presentation_name' => 'Presentazione',
        'layer_6_presentation_desc' => 'Traduce, cifra e comprime i dati. Garantisce che il formato dei dati sia comprensibile al livello Applicazione.',

        'layer_5_session_name' => 'Sessione',
        'layer_5_session_desc' => 'Gestisce l\'apertura, il mantenimento e la chiusura delle sessioni di comunicazione tra applicazioni.',

        'layer_4_transport_name' => 'Trasporto',
        'layer_4_transport_desc' => 'Garantisce la consegna affidabile end-to-end dei dati, gestendo il controllo del flusso, la correzione degli errori e il multiplexing delle porte.',

        'layer_3_network_name' => 'Rete',
        'layer_3_network_desc' => 'Gestisce l\'indirizzamento logico (IP) e il routing dei pacchetti tra reti diverse.',

        'layer_2_data_link_name' => 'Collegamento dati',
        'layer_2_data_link_desc' => 'Gestisce la trasmissione affidabile di frame tra nodi adiacenti sulla stessa rete, inclusi indirizzamento MAC e rilevamento degli errori.',

        'layer_1_physical_name' => 'Fisico',
        'layer_1_physical_desc' => 'Trasmette bit grezzi sul mezzo fisico. Definisce caratteristiche elettriche, meccaniche e funzionali del collegamento.',
    ],

    'ping_traceroute' => [
        'title'       => 'Ping / Traceroute',
        'description' => 'Verifica la raggiungibilità di un host e traccia il percorso dei pacchetti sulla rete.',
        'menu'        => 'Ping / Traceroute',

        'input_target'       => 'Host / Indirizzo IP',
        'placeholder_target' => 'es. 8.8.8.8 o example.com',
        'input_tool'         => 'Strumento',
        'tool_ping'          => 'Ping',
        'tool_traceroute'    => 'Traceroute',
        'input_count'        => 'Numero di ping',
        'input_hops'         => 'Max hop',
        'run'                => 'Esegui',
        'running'            => 'In esecuzione…',

        'result_title'    => 'Risultato',
        'result_exit_ok'  => 'Completato con successo',
        'result_exit_err' => 'Completato con errori (exit :code)',
        'empty'           => 'Inserisci un host e premi Esegui.',

        'note_traceroute' => 'Il traceroute può richiedere fino a 90 secondi.',

        'error_target_required' => "L'host di destinazione è obbligatorio.",
        'error_target_invalid'  => "Inserisci un indirizzo IP valido o un hostname valido (es. example.com).",
        'error_tool_required'   => 'Seleziona uno strumento (ping o traceroute).',
        'error_tool_invalid'    => 'Strumento non valido.',
        'error_binary_missing'  => 'Il comando richiesto non è disponibile nel sistema.',
    ],

    'linux_cheatsheet' => [
        'title'       => 'Linux Commands Cheatsheet',
        'description' => 'Riferimento rapido ai comandi Linux più usati, organizzati per categoria. Usa la ricerca per filtrare.',
        'menu'        => 'Linux Cheatsheet',

        'search_placeholder' => 'Filtra per comando o descrizione…',
        'no_results'         => 'Nessun comando corrisponde alla ricerca.',
        'col_command'        => 'Comando',
        'col_description'    => 'Descrizione',
        'col_example'        => 'Esempio',

        'cat_filesystem' => 'Filesystem',
        'cat_text'       => 'Testo e ricerca',
        'cat_processes'  => 'Processi e servizi',
        'cat_network'    => 'Rete',
        'cat_system'     => 'Sistema e hardware',
        'cat_users'      => 'Utenti e permessi',
        'cat_archives'   => 'Archivi e compressione',
        'cat_disk'       => 'Disco e partizioni',
    ],

    'cable_colors' => [
        'title'       => 'Colori Cavi T568A / T568B',
        'description' => 'Riferimento visivo per la codifica colori dei cavi di rete RJ45 secondo gli standard T568A e T568B.',
        'menu'        => 'Colori Cavi RJ45',

        'col_pin'       => 'Pin',
        'col_color'     => 'Colore',
        'col_pair'      => 'Coppia',
        'col_func_fast' => '10/100 Mb',
        'col_func_gig'  => '1 Gb (802.3ab)',

        'differs_badge'  => 'differisce',
        'stripe_label'   => 'striscia',

        'pair_blue'   => 'Coppia 1 — Blu',
        'pair_orange' => 'Coppia 2 — Arancione',
        'pair_green'  => 'Coppia 3 — Verde',
        'pair_brown'  => 'Coppia 4 — Marrone',

        'color_white_green'  => 'Bianco/Verde',
        'color_green'        => 'Verde',
        'color_white_orange' => 'Bianco/Arancione',
        'color_orange'       => 'Arancione',
        'color_white_blue'   => 'Bianco/Blu',
        'color_blue'         => 'Blu',
        'color_white_brown'  => 'Bianco/Marrone',
        'color_brown'        => 'Marrone',

        'note_diff'  => 'I pin evidenziati differiscono tra T568A e T568B (coppie 2 e 3 invertite).',
        'note_usage' => 'T568B è lo standard più diffuso in ambito commerciale. T568A è richiesto nelle installazioni governative USA (TIA-568). Entrambi vanno bene purché si usi lo stesso standard su entrambi i capi.',
        'legend'     => 'Legenda coppie',
    ],

    'rfc_browser' => [
        'title'       => 'RFC Browser',
        'description' => 'Sfoglia e cerca i principali RFC di Internet per numero, titolo, categoria o stato.',
        'menu'        => 'RFC Browser',

        'search_placeholder' => 'Cerca per numero, titolo o parola chiave…',
        'no_results'         => 'Nessun RFC corrisponde ai criteri di ricerca.',
        'filter_all'         => 'Tutti',
        'filter_category'    => 'Categoria',
        'filter_status'      => 'Stato',
        'count_unit'         => 'RFC',

        'col_number'   => 'RFC',
        'col_title'    => 'Titolo',
        'col_year'     => 'Anno',
        'col_status'   => 'Stato',
        'col_category' => 'Categoria',
        'col_link'     => 'Link',
        'open_rfc'     => 'Apri',

        'cat_networking'  => 'Networking',
        'cat_routing'     => 'Routing',
        'cat_dns'         => 'DNS',
        'cat_email'       => 'Email',
        'cat_web'         => 'Web',
        'cat_security'    => 'Sicurezza',
        'cat_management'  => 'Gestione',
        'cat_reference'   => 'Riferimenti',

        'status_std'  => 'Internet Standard',
        'status_ps'   => 'Proposed Standard',
        'status_bcp'  => 'Best Current Practice',
        'status_info' => 'Informational',
        'status_hist' => 'Historic',

        'note_source' => 'Fonte ufficiale: IETF RFC Editor — rfc-editor.org',
    ],

    'http_status_codes' => [
        'title'       => 'HTTP Status Codes',
        'description' => 'Riferimento completo dei codici di stato HTTP con descrizione e RFC di riferimento.',
        'menu'        => 'HTTP Status Codes',

        'search_placeholder' => 'Cerca per codice o nome…',
        'no_results'         => 'Nessun codice corrisponde alla ricerca.',
        'filter_all'         => 'Tutti',
        'count_unit'         => 'codici',

        'col_code'  => 'Codice',
        'col_name'  => 'Nome',
        'col_desc'  => 'Descrizione',
        'col_rfc'   => 'RFC',

        'cat_1xx' => '1xx — Informational',
        'cat_2xx' => '2xx — Success',
        'cat_3xx' => '3xx — Redirection',
        'cat_4xx' => '4xx — Client Error',
        'cat_5xx' => '5xx — Server Error',
    ],

    'mac_lookup' => [
        'title'       => 'MAC Address Lookup',
        'description' => 'Identifica il produttore di un dispositivo a partire dal suo indirizzo MAC (OUI).',
        'menu'        => 'MAC Address Lookup',

        'input_mac'       => 'Indirizzo MAC',
        'placeholder_mac' => 'es. 00:1A:2B o 00:1A:2B:3C:4D:5E',
        'lookup'          => 'Cerca',

        'result_title'  => 'Risultato',
        'field_vendor'  => 'Produttore',
        'field_oui'     => 'OUI (primi 3 ottetti)',
        'field_nic'     => 'NIC (ultimi 3 ottetti)',
        'field_type'    => 'Tipo',
        'field_formats' => 'Formati',

        'type_unicast'    => 'Unicast',
        'type_multicast'  => 'Multicast',
        'type_global'     => 'Globalmente amministrato (IEEE)',
        'type_local'      => 'Localmente amministrato',

        'oui_only_badge'  => 'Solo OUI',
        'vendor_unknown'  => 'Produttore non trovato',
        'vendor_hint'     => 'Il database OUI incorporato copre i vendor più comuni. Per una ricerca completa consulta IEEE RA.',

        'empty'           => 'Inserisci un indirizzo MAC per identificare il produttore.',
        'error_required'  => "L'indirizzo MAC è obbligatorio.",
        'error_invalid'   => "Indirizzo MAC non valido. Formati accettati: AA:BB:CC (solo OUI) oppure AA:BB:CC:DD:EE:FF (MAC completo), con separatori : - . o senza.",
    ],

    'whois' => [
        'title'       => 'Whois Lookup',
        'description' => 'Interroga il registro Whois per domini e indirizzi IP.',
        'menu'        => 'Whois Lookup',

        'input_target'       => 'Dominio o indirizzo IP',
        'placeholder_target' => 'es. example.com o 8.8.8.8',
        'lookup'             => 'Cerca',
        'running'            => 'In corso…',

        'result_title'    => 'Risultato Whois',
        'result_for'      => 'Query per:',
        'truncated_notice'=> 'Output troncato ai primi :n righe.',
        'empty'           => 'Inserisci un dominio o un indirizzo IP e premi Cerca.',

        'error_target_required' => 'Il dominio o indirizzo IP è obbligatorio.',
        'error_target_invalid'  => 'Inserisci un dominio valido (es. example.com) o un indirizzo IP.',
        'error_binary_missing'  => 'Il comando whois non è disponibile su questo sistema.',
        'error_lookup_failed'   => 'La query Whois non ha restituito risultati.',
    ],

    'regex_tester' => [
        'title'       => 'Regex Tester',
        'description' => 'Testa espressioni regolari con evidenziazione delle corrispondenze e cattura dei gruppi.',
        'menu'        => 'Regex Tester',

        'label_pattern'     => 'Pattern',
        'placeholder_pattern' => 'es. \b\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\b',
        'label_flags'       => 'Flags',
        'flag_i'            => 'i — case-insensitive',
        'flag_m'            => 'm — multiline',
        'flag_s'            => 's — dotall',
        'flag_u'            => 'u — unicode',
        'label_subject'     => 'Testo di test',
        'placeholder_subject' => 'Incolla qui il testo da analizzare…',
        'label_replacement' => 'Sostituzione (opzionale)',
        'placeholder_replacement' => 'es. [REDACTED] o $1-$2',
        'btn_test'          => 'Testa',

        'match_count_one'   => '1 corrispondenza trovata',
        'match_count_many'  => ':count corrispondenze trovate',
        'match_count_zero'  => 'Nessuna corrispondenza',
        'truncated_notice'  => 'Visualizzate le prime :n corrispondenze.',
        'section_highlight' => 'Testo con evidenziazioni',
        'section_matches'   => 'Corrispondenze',
        'section_replace'   => 'Risultato sostituzione',
        'col_n'             => '#',
        'col_match'         => 'Match',
        'col_start'         => 'Inizio',
        'col_end'           => 'Fine',
        'col_groups'        => 'Gruppi',
        'group_label'       => 'Gruppo :n',
        'no_groups'         => '—',
        'idle'              => 'Inserisci un pattern e un testo per iniziare.',
        'error_invalid'     => 'Pattern non valido:',
    ],

    'ssl_checker' => [
        'title'       => 'SSL/TLS Checker',
        'description' => 'Verifica il certificato SSL/TLS di un host: validità, scadenza, SANs e protocollo.',
        'menu'        => 'SSL/TLS Checker',

        'label_host'         => 'Hostname',
        'placeholder_host'   => 'es. example.com',
        'label_port'         => 'Porta',
        'placeholder_port'   => '443',
        'btn_check'          => 'Verifica',
        'running'            => 'Connessione…',

        'status_valid'       => 'Certificato valido',
        'status_expired'     => 'Certificato scaduto',
        'status_expiring'    => 'In scadenza',
        'status_error'       => 'Errore di connessione',
        'status_trusted'     => 'Catena attendibile',
        'status_untrusted'   => 'Catena non attendibile',
        'status_trust_unknown' => 'Trust non verificato',

        'days_left_many'     => ':n giorni rimanenti',
        'days_left_one'      => '1 giorno rimanente',
        'days_left_zero'     => 'Scaduto',

        'section_cert'       => 'Dettagli certificato',
        'section_tls'        => 'Protocollo TLS',
        'section_sans'       => 'Subject Alternative Names',

        'field_subject_cn'   => 'Common Name',
        'field_subject_o'    => 'Organizzazione',
        'field_issuer_cn'    => 'Emesso da',
        'field_issuer_o'     => 'Issuer Org',
        'field_valid_from'   => 'Valido dal',
        'field_valid_to'     => 'Valido fino al',
        'field_serial'       => 'Numero seriale',
        'field_fingerprint'  => 'Fingerprint SHA-256',
        'field_protocol'     => 'Protocollo',
        'field_cipher'       => 'Cipher suite',
        'field_bits'         => 'Bit',

        'empty'              => 'Inserisci un hostname e premi Verifica.',
        'error_host_required'=> "L'hostname è obbligatorio.",
        'error_host_invalid' => 'Inserisci un hostname valido (es. example.com).',
        'error_port_invalid' => 'La porta deve essere un numero tra 1 e 65535.',
    ],

    'base_converter' => [
        'title'       => 'Convertitore di Base',
        'description' => 'Converti numeri tra binario, ottale, decimale, esadecimale e qualsiasi base da 2 a 36.',
        'menu'        => 'Convertitore di Base',

        'label_number'      => 'Numero',
        'placeholder_number'=> 'Inserisci un numero…',
        'label_from_base'   => 'Base di partenza',
        'btn_convert'       => 'Converti',

        'base_bin'   => 'BIN',
        'base_oct'   => 'OCT',
        'base_dec'   => 'DEC',
        'base_hex'   => 'HEX',

        'section_results' => 'Risultati',
        'field_bit_length'=> 'Lunghezza in bit',
        'field_ascii'     => 'Carattere ASCII',

        'idle'              => 'Inserisci un numero e seleziona la base di partenza.',
        'error_invalid_chars' => 'Il numero contiene caratteri non validi per la base selezionata.',
        'error_overflow'    => 'Il numero è troppo grande (limite: intero con segno a 64 bit).',
        'error_base_required'=> 'La base di partenza è obbligatoria.',
        'error_base_invalid' => 'La base deve essere un numero intero tra 2 e 36.',
    ],

    'bandwidth_calculator' => [
        'title'       => 'Calcolatore di Banda',
        'description' => 'Calcola tempi di trasferimento, dimensione dei file o banda necessaria a partire da due valori noti.',
        'menu'        => 'Calcolatore di Banda',

        'mode_time'      => 'Tempo di trasferimento',
        'mode_size'      => 'Dimensione trasferibile',
        'mode_bandwidth' => 'Banda necessaria',

        'label_file_size'   => 'Dimensione file',
        'label_bandwidth'   => 'Banda disponibile',
        'label_time'        => 'Tempo',
        'label_overhead'    => 'Overhead protocollo (%)',
        'placeholder_overhead' => 'es. 4',
        'btn_calculate'     => 'Calcola',

        'result_time'       => 'Tempo di trasferimento',
        'result_size'       => 'Dati trasferibili',
        'result_bandwidth'  => 'Banda necessaria',
        'throughput'        => 'Throughput effettivo',
        'section_details'   => 'Dettaglio unità',
        'col_unit'          => 'Unità',
        'col_value'         => 'Valore',

        'error_positive'    => 'I valori devono essere maggiori di zero.',
        'error_validation'  => 'Dati non validi.',
    ],

    'formatter' => [
        'title'       => 'Formatter',
        'description' => 'Formatta e indenta JSON, XML e HTML con syntax highlighting.',
        'menu'        => 'Formatter',

        'label_input'       => 'Input',
        'placeholder_input' => 'Incolla qui il tuo JSON, XML o HTML…',
        'label_format'      => 'Formato',
        'label_indent'      => 'Indentazione',
        'btn_format'        => 'Formatta',

        'format_auto' => 'Auto',
        'format_json' => 'JSON',
        'format_xml'  => 'XML',
        'format_html' => 'HTML',
        'indent_2'    => '2 spazi',
        'indent_4'    => '4 spazi',

        'section_output'  => 'Output',
        'detected_format' => 'Formato',
        'lines_count'     => 'Righe',
        'size_in'         => 'Input',
        'size_out'        => 'Output',
        'btn_copy'        => 'Copia',
        'copied'          => 'Copiato!',

        'idle'                 => 'Incolla del testo e premi Formatta.',
        'error_invalid'        => 'Il contenuto non è un :format valido.',
        'error_too_large'      => "L'input supera il limite di 200 KB.",
        'error_unknown_format' => 'Formato non riconosciuto. Seleziona JSON, XML o HTML manualmente.',
    ],

    'port_checker' => [
        'title'       => 'Port Checker',
        'description' => 'Verifica se una porta TCP o UDP è aperta su un host remoto.',
        'menu'        => 'Port Checker',

        'label_host'        => 'Host / Indirizzo IP',
        'placeholder_host'  => 'es. example.com o 192.168.1.1',
        'label_port'        => 'Porta',
        'placeholder_port'  => 'es. 80',
        'label_protocol'    => 'Protocollo',
        'label_captcha'     => 'Copia il codice qui sopra',
        'placeholder_captcha' => 'Inserisci il codice',
        'btn_check'         => 'Verifica',

        'status_open'         => 'Aperta',
        'status_closed'       => 'Chiusa',
        'status_filtered'     => 'Filtrata',
        'status_open_filtered'=> 'Aperta / Filtrata',

        'result_host'      => 'Host',
        'result_port'      => 'Porta',
        'result_protocol'  => 'Protocollo',
        'result_latency'   => 'Latenza',
        'result_latency_ms'=> ':ms ms',

        'udp_note' => 'UDP: l\'assenza di risposta non garantisce che la porta sia aperta — i firewall possono bloccare i pacchetti ICMP senza rispondere.',

        'error_host_required'     => "L'host è obbligatorio.",
        'error_host_invalid'      => 'Inserisci un hostname o indirizzo IP valido.',
        'error_port_required'     => 'La porta è obbligatoria.',
        'error_port_invalid'      => 'La porta deve essere un numero tra 1 e 65535.',
        'error_protocol_invalid'  => 'Seleziona TCP o UDP.',
        'error_captcha_required'  => 'Inserisci il codice di verifica.',
        'error_captcha'           => 'Codice non corretto. Un nuovo codice è stato generato.',
        'error_throttle'          => 'Troppo richieste. Riprova tra un minuto.',
    ],

    'markdown_viewer' => [
        'title'       => 'Visualizzatore Markdown',
        'description' => 'Incolla testo Markdown per visualizzarne l\'anteprima renderizzata ed esportarlo in HTML o PDF.',
        'menu'        => 'Markdown Viewer',

        'label_input'          => 'Testo Markdown',
        'placeholder_input'    => "# Titolo\n\nScrivi qui il tuo testo **Markdown**...",
        'label_preview'        => 'Anteprima',
        'placeholder_preview'  => 'L\'anteprima apparirà qui mentre digiti.',

        'btn_export_html' => 'Scarica HTML',
        'btn_export_pdf'  => 'Scarica PDF',

        'hint_limit'  => 'Limite: 100.000 caratteri.',

        'error_too_large' => 'Il testo supera il limite di 100.000 caratteri.',
    ],

    'email_header_analyzer' => [
        'title'       => 'Email Header Analyzer',
        'description' => 'Analizza gli header di un\'email: traccia degli hop di consegna, tempi per tratto, risultati SPF/DKIM/DMARC e riepilogo dei campi principali.',
        'menu'        => 'Email Header Analyzer',

        'label_input'          => 'Header email grezzo',
        'placeholder_input'    => "Incolla qui l'header completo dell'email (tasto destro → Visualizza sorgente → copia la parte dell'header)…",
        'btn_analyze'          => 'Analizza',
        'empty'                => 'Incolla un header email per vedere l\'analisi.',

        'section_summary'      => 'Riepilogo',
        'section_trace'        => 'Traccia di consegna',
        'section_auth'         => 'Autenticazione',
        'section_all_headers'  => 'Tutti gli header',
        'btn_show_headers'     => 'Mostra tutti gli header',
        'btn_hide_headers'     => 'Nascondi gli header',

        'field_from'       => 'From',
        'field_to'         => 'To',
        'field_subject'    => 'Subject',
        'field_date'       => 'Date',
        'field_message_id' => 'Message-ID',
        'field_reply_to'   => 'Reply-To',
        'field_mailer'     => 'Mailer',

        'col_hop'       => '#',
        'col_from'      => 'From',
        'col_by'        => 'By',
        'col_timestamp' => 'Timestamp',
        'col_delay'     => 'Ritardo',
        'row_total'     => 'Totale',
        'no_hops'       => 'Nessun header Received: trovato.',

        'auth_spf'   => 'SPF',
        'auth_dkim'  => 'DKIM',
        'auth_dmarc' => 'DMARC',
        'auth_raw'   => 'Authentication-Results grezzo',
        'auth_none'  => 'Assente',

        'error_no_headers' => 'Nessun header riconosciuto. Verifica che il testo incollato contenga degli header email validi.',
        'error_too_large'  => "L'header supera il limite di 50.000 caratteri.",
    ],

    'email_deliverability' => [
        'title'       => 'Email Deliverability Checker',
        'description' => 'Verifica i record DNS per la deliverability di un dominio: MX, SPF, DMARC e DKIM.',
        'menu'        => 'Deliverability Checker',

        'label_domain'          => 'Dominio',
        'placeholder_domain'    => 'es. example.com o utente@example.com',
        'label_dkim_selector'   => 'Selettore DKIM',
        'placeholder_dkim_selector' => 'es. google, default…',
        'optional'              => 'opzionale',
        'btn_check'             => 'Verifica',
        'empty'                 => 'Inserisci un dominio per vedere l\'analisi.',
        'hint_dkim'             => 'Se non inserisci un selettore DKIM, vengono provati automaticamente quelli più comuni (google, default, k1, mail…).',

        'checking'      => 'Risultati per',
        'found'         => 'Trovato',
        'not_found'     => 'Non trovato',

        'col_priority'  => 'Priorità',
        'col_host'      => 'Host',

        'mx_none'       => 'Nessun record MX trovato per questo dominio.',
        'spf_none'      => 'Nessun record SPF trovato.',
        'spf_mechanism' => 'Meccanismo all:',
        'dmarc_none'    => 'Nessun record DMARC trovato.',
        'dmarc_policy'  => 'Policy',
        'dmarc_sp'      => 'Policy sottodomini',
        'dmarc_pct'     => 'Percentuale',
        'dmarc_rua'     => 'Report aggregati',
        'dkim_selector' => 'Selettore:',
        'dkim_none'     => 'Nessun record DKIM trovato con questo selettore.',
        'dkim_not_found_selector' => 'nessuno trovato automaticamente',

        'error_domain_required' => 'Il dominio è obbligatorio.',
        'error_domain_invalid'  => 'Inserisci un dominio o un indirizzo email valido.',
        'error_domain_too_long' => 'Il dominio non può superare i 253 caratteri.',
    ],

    'blacklist_checker' => [
        'title'       => 'Blacklist / RBL Checker',
        'description' => 'Verifica se un indirizzo IPv4 o un dominio è presente nelle principali DNS blacklist (DNSBL/RBL) usate per il filtraggio dello spam.',
        'menu'        => 'Blacklist Checker',

        'label_target'       => 'Indirizzo IP o Dominio',
        'placeholder_target' => 'es. 1.2.3.4 o example.com',
        'btn_check'          => 'Verifica',
        'empty'              => 'Inserisci un indirizzo IP o un dominio per verificarlo.',
        'hint'               => 'Accetta anche indirizzi email (es. user@example.com) — verrà estratto il dominio.',

        'summary_clean'   => 'Non presente in nessuna blacklist',
        'summary_listed'  => 'Presente in una o più blacklist',
        'checked_ip'      => 'IP verificato:',
        'resolved_from'   => 'risolto da',
        'checked_domain'  => 'Dominio verificato:',
        'ip_unresolved'   => 'IP non risolvibile, verifica solo RBL domain',

        'col_name'    => 'Blacklist',
        'col_zone'    => 'Zona DNS',
        'col_type'    => 'Tipo',
        'col_status'  => 'Stato',
        'col_detail'  => 'Dettaglio',

        'status_listed' => 'In lista',
        'status_clean'  => 'Pulito',

        'error_target_required' => 'Inserisci un indirizzo IP o un dominio.',
        'error_target_invalid'  => 'Inserisci un indirizzo IPv4 valido o un nome di dominio valido.',
        'error_target_too_long' => 'L\'input non può superare i 253 caratteri.',
    ],

    'mx_checker' => [
        'title'       => 'MX Checker avanzato',
        'description' => 'Verifica i server MX di un dominio: priorità, indirizzi IP, raggiungibilità SMTP porta 25, banner di greeting e capacità EHLO (STARTTLS, AUTH, SIZE).',
        'menu'        => 'MX Checker',

        'label_domain'       => 'Dominio',
        'placeholder_domain' => 'es. example.com o utente@example.com',
        'btn_check'          => 'Verifica',
        'empty'              => 'Inserisci un dominio per analizzare i suoi server MX.',
        'hint'               => 'Accetta anche indirizzi email — verrà estratto il dominio. Il test SMTP sulla porta 25 potrebbe richiedere qualche secondo.',

        'no_mx'          => 'Nessun record MX trovato per',
        'server_count'   => 'server MX per :domain',
        'priority'       => 'Priorità',
        'reachable'      => 'Raggiungibile',
        'unreachable'    => 'Non raggiungibile',
        'ip_unresolvable'=> 'Indirizzo IP non risolvibile.',

        'label_banner'       => 'Banner:',
        'label_capabilities' => 'Capacità:',
        'show_ehlo'          => 'Risposta EHLO completa',

        'port25_blocked' => 'Porta 25 non raggiungibile — potrebbe essere filtrata dal firewall del provider o dalla rete locale.',

        'label_captcha'       => 'Copia il codice qui sopra',
        'placeholder_captcha' => 'Inserisci il codice',

        'error_domain_required'  => 'Il dominio è obbligatorio.',
        'error_domain_invalid'   => 'Inserisci un dominio o un indirizzo email valido.',
        'error_domain_too_long'  => 'Il dominio non può superare i 253 caratteri.',
        'error_captcha_required' => 'Inserisci il codice di verifica.',
        'error_captcha'          => 'Codice non corretto. Un nuovo codice è stato generato.',
    ],

    'email_validator' => [
        'title'       => 'Email Validator',
        'description' => 'Verifica se un indirizzo email è valido: controlla sintassi, record MX del dominio e raggiungibilità della mailbox tramite SMTP.',
        'menu'        => 'Email Validator',

        'label_email'       => 'Indirizzo email',
        'placeholder_email' => 'es. utente@example.com',
        'btn_check'         => 'Verifica',
        'empty'             => 'Inserisci un indirizzo email per verificarne la validità.',
        'hint'              => 'Il controllo SMTP si connette al server di posta del dominio — alcuni provider non permettono la verifica e il risultato potrebbe essere "non verificabile".',

        'label_captcha'       => 'Copia il codice qui sopra',
        'placeholder_captcha' => 'Inserisci il codice',

        'section_syntax' => 'Sintassi',
        'section_mx'     => 'Record MX',
        'section_smtp'   => 'Verifica SMTP',
        'section_result' => 'Risultato',

        'syntax_valid'   => 'Valida',
        'syntax_invalid' => 'Non valida',
        'local_part'     => 'Parte locale',
        'domain_part'    => 'Dominio',

        'mx_found'       => 'Trovati :count record MX',
        'mx_not_found'   => 'Nessun record MX — il dominio non può ricevere email',
        'mx_fallback'    => 'Nessun record MX, ma il dominio ha un record A (fallback)',
        'mx_host'        => 'Server',
        'mx_priority'    => 'Priorità',

        'smtp_valid'       => 'Mailbox accettata',
        'smtp_invalid'     => 'Mailbox rifiutata',
        'smtp_catchall'    => 'Catch-all: il dominio accetta qualsiasi indirizzo',
        'smtp_risky'       => 'Risposta temporanea — potrebbe essere un blocco anti-spam',
        'smtp_unavailable' => 'Non verificabile: il server non consente verifiche SMTP',
        'smtp_skipped'     => 'Non eseguito',
        'smtp_code'        => 'Codice risposta',

        'overall_valid'   => 'Indirizzo valido',
        'overall_invalid' => 'Indirizzo non valido',
        'overall_unknown' => 'Non verificabile',
        'overall_risky'   => 'Risultato incerto',

        'error_email_required'   => 'Inserisci un indirizzo email.',
        'error_email_too_long'   => "L'indirizzo email non può superare i 254 caratteri.",
        'error_captcha_required' => 'Inserisci il codice di verifica.',
        'error_captcha'          => 'Codice non corretto. Un nuovo codice è stato generato.',
    ],
];
