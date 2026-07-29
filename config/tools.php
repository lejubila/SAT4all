<?php

/**
 * Registro flat di tutti i tool dell'applicazione.
 * Usato per generare window.SAT_TOOLS nel layout e tracciare l'utilizzo.
 */
return [
    // Subnet & IP
    ['key' => 'subnet-calculator',    'route' => 'tools.subnet-calculator.index',    'name_key' => 'tools.subnet_calculator.menu',      'cat' => 'group_subnet_ip'],
    ['key' => 'ipv6-calculator',      'route' => 'tools.ipv6-calculator.index',      'name_key' => 'tools.ipv6_calculator.menu',        'cat' => 'group_subnet_ip'],
    ['key' => 'cidr-cheatsheet',      'route' => 'tools.cidr-cheatsheet.index',      'name_key' => 'tools.cidr_cheatsheet.menu',        'cat' => 'group_subnet_ip'],
    ['key' => 'vlan-calculator',      'route' => 'tools.vlan-calculator.index',      'name_key' => 'tools.vlan_calculator.menu',        'cat' => 'group_subnet_ip'],
    ['key' => 'ip-geolocation',       'route' => 'tools.ip-geolocation.index',       'name_key' => 'tools.ip_geolocation.menu',         'cat' => 'group_subnet_ip'],
    // Diagnostica & Lookup
    ['key' => 'dns-lookup',           'route' => 'tools.dns-lookup.index',           'name_key' => 'tools.dns_lookup.menu',             'cat' => 'group_diagnostics'],
    ['key' => 'ping-traceroute',      'route' => 'tools.ping-traceroute.index',      'name_key' => 'tools.ping_traceroute.menu',        'cat' => 'group_diagnostics'],
    ['key' => 'whois',                'route' => 'tools.whois.index',                'name_key' => 'tools.whois.menu',                  'cat' => 'group_diagnostics'],
    ['key' => 'mac-lookup',           'route' => 'tools.mac-lookup.index',           'name_key' => 'tools.mac_lookup.menu',             'cat' => 'group_diagnostics'],
    ['key' => 'ssl-checker',          'route' => 'tools.ssl-checker.index',          'name_key' => 'tools.ssl_checker.menu',            'cat' => 'group_diagnostics'],
    ['key' => 'port-checker',         'route' => 'tools.port-checker.index',         'name_key' => 'tools.port_checker.menu',           'cat' => 'group_diagnostics'],
    // Riferimenti
    ['key' => 'port-reference',       'route' => 'tools.port-reference.index',       'name_key' => 'tools.port_reference.menu',         'cat' => 'group_references'],
    ['key' => 'osi-model',            'route' => 'tools.osi-model.index',            'name_key' => 'tools.osi_model.menu',              'cat' => 'group_references'],
    ['key' => 'linux-cheatsheet',     'route' => 'tools.linux-cheatsheet.index',     'name_key' => 'tools.linux_cheatsheet.menu',       'cat' => 'group_references'],
    ['key' => 'http-status-codes',    'route' => 'tools.http-status-codes.index',    'name_key' => 'tools.http_status_codes.menu',      'cat' => 'group_references'],
    ['key' => 'rfc-browser',          'route' => 'tools.rfc-browser.index',          'name_key' => 'tools.rfc_browser.menu',            'cat' => 'group_references'],
    // Email
    ['key' => 'email-header-analyzer','route' => 'tools.email-header-analyzer.index','name_key' => 'tools.email_header_analyzer.menu', 'cat' => 'group_email'],
    ['key' => 'email-deliverability', 'route' => 'tools.email-deliverability.index', 'name_key' => 'tools.email_deliverability.menu',  'cat' => 'group_email'],
    ['key' => 'blacklist-checker',    'route' => 'tools.blacklist-checker.index',    'name_key' => 'tools.blacklist_checker.menu',      'cat' => 'group_email'],
    ['key' => 'mx-checker',           'route' => 'tools.mx-checker.index',           'name_key' => 'tools.mx_checker.menu',             'cat' => 'group_email'],
    ['key' => 'email-validator',      'route' => 'tools.email-validator.index',      'name_key' => 'tools.email_validator.menu',        'cat' => 'group_email'],
    // Cablaggio
    ['key' => 'cable-schemas',        'route' => 'tools.cable-schemas.index',        'name_key' => 'tools.cable_schemas.menu',          'cat' => 'group_cabling'],
    ['key' => 'cable-colors',         'route' => 'tools.cable-colors.index',         'name_key' => 'tools.cable_colors.menu',           'cat' => 'group_cabling'],
    // Strumenti
    ['key' => 'regex-tester',         'route' => 'tools.regex-tester.index',         'name_key' => 'tools.regex_tester.menu',           'cat' => 'group_tools'],
    ['key' => 'base-converter',       'route' => 'tools.base-converter.index',       'name_key' => 'tools.base_converter.menu',         'cat' => 'group_tools'],
    ['key' => 'bandwidth-calculator', 'route' => 'tools.bandwidth-calculator.index', 'name_key' => 'tools.bandwidth_calculator.menu',   'cat' => 'group_tools'],
    ['key' => 'formatter',            'route' => 'tools.formatter.index',            'name_key' => 'tools.formatter.menu',              'cat' => 'group_tools'],
    ['key' => 'markdown-viewer',      'route' => 'tools.markdown-viewer.index',      'name_key' => 'tools.markdown_viewer.menu',        'cat' => 'group_tools'],
];
