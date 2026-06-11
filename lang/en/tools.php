<?php

/*
| Per-tool translation strings (key: tools.{tool_name}.{key}).
| Populated as the Phase 1 tools are implemented.
*/

return [
    'subnet_calculator' => [
        'title'       => 'Subnet Calculator (IPv4)',
        'description' => 'Compute network, broadcast, host range and masks from an IP address and CIDR prefix.',
        'menu'        => 'Subnet Calculator',

        'input_ip'    => 'IP address',
        'input_cidr'  => 'CIDR prefix',
        'calculate'   => 'Calculate',
        'placeholder_ip' => 'e.g. 192.168.1.10',

        'result_title'     => 'Result',
        'result_network'   => 'Network address',
        'result_broadcast' => 'Broadcast',
        'result_netmask'   => 'Netmask',
        'result_wildcard'  => 'Wildcard',
        'result_host_min'  => 'First host',
        'result_host_max'  => 'Last host',
        'result_usable'    => 'Usable hosts',
        'result_total'     => 'Total addresses',
        'result_class'     => 'Class',
        'result_type'      => 'Type',
        'type_private'     => 'Private',
        'type_public'      => 'Public',

        'empty'       => 'Enter an IP address and a CIDR prefix to see the result.',

        'error_ip_required'   => 'The IP address is required.',
        'error_ip_invalid'    => 'The IP address is not a valid IPv4.',
        'error_cidr_required' => 'The CIDR prefix is required.',
        'error_cidr_invalid'  => 'The CIDR prefix must be an integer.',
        'error_cidr_range'    => 'The CIDR prefix must be between 0 and 32.',
    ],

    'ipv6_calculator' => [
        'title'       => 'IPv6 Subnet Calculator',
        'description' => 'Compute network, first/last address, expanded/compressed form and number of addresses from an IPv6 and its prefix.',
        'menu'        => 'IPv6 Subnet Calculator',

        'input_address'   => 'IPv6 address',
        'input_prefix'    => 'Prefix length',
        'calculate'       => 'Calculate',
        'placeholder_address' => 'e.g. 2001:db8::1',

        'result_title'        => 'Result',
        'result_compressed'   => 'Compressed form',
        'result_expanded'     => 'Expanded form',
        'result_network'      => 'Network',
        'result_network_expanded' => 'Network (expanded)',
        'result_first'        => 'First address',
        'result_last'         => 'Last address',
        'result_total'        => 'Total addresses',
        'result_type'         => 'Type',

        'type_global_unicast' => 'Global Unicast',
        'type_link_local'     => 'Link-Local',
        'type_unique_local'   => 'Unique Local',
        'type_multicast'      => 'Multicast',
        'type_loopback'       => 'Loopback',
        'type_unspecified'    => 'Unspecified',

        'empty'       => 'Enter an IPv6 address and a prefix to see the result.',

        'error_address_required' => 'The IPv6 address is required.',
        'error_address_invalid'  => 'The IPv6 address is not valid.',
        'error_prefix_required'  => 'The prefix length is required.',
        'error_prefix_invalid'   => 'The prefix length must be an integer.',
        'error_prefix_range'     => 'The prefix length must be between 0 and 128.',
    ],

    'port_reference' => [
        'title'       => 'Port Reference (TCP/UDP)',
        'description' => 'Search well-known ports by number, service or protocol.',
        'menu'        => 'Port Reference',

        'search_placeholder' => 'Search by number or service (e.g. 443, SSH, MySQL)…',
        'protocol_label'     => 'Protocol',
        'protocol_all'       => 'All',
        'protocol_tcp'       => 'TCP',
        'protocol_udp'       => 'UDP',

        'col_port'        => 'Port',
        'col_protocol'    => 'Protocol',
        'col_service'     => 'Service',
        'col_description' => 'Description',

        'count'       => ':count ports',
        'no_results'  => 'No port matches the search criteria.',
    ],

    'cable_schemas' => [
        'title'       => 'Network Cable Schemas',
        'description' => 'RJ45 wiring standards: T568A, T568B, straight-through, crossover and rollover cables.',
        'menu'        => 'Cable Schemas',

        'section_standards' => 'T568A and T568B Standards',
        'section_cables'    => 'Cable Types',

        'col_pin'    => 'Pin',
        'col_color'  => 'Color',

        'end_a' => 'End A',
        'end_b' => 'End B',

        'cable_straight'  => 'Straight-Through Cable',
        'cable_crossover' => 'Crossover Cable',
        'cable_rollover'  => 'Rollover (Console) Cable',

        'cable_straight_use'  => 'PC ↔ Switch, PC ↔ Hub, Switch ↔ Router',
        'cable_crossover_use' => 'PC ↔ PC, Switch ↔ Switch, Router ↔ Router',
        'cable_rollover_use'  => 'PC ↔ Router/Switch Console port (Cisco)',

        'note_mdix' => 'Modern devices with Auto-MDI/MDI-X detect the cable type automatically — crossover cables are only needed with older equipment.',
    ],

    'cidr_cheatsheet' => [
        'title'       => 'CIDR Cheat Sheet',
        'description' => 'Quick reference table for all IPv4 prefixes from /0 to /32 with netmask, wildcard and host count.',
        'menu'        => 'CIDR Cheat Sheet',

        'col_cidr'    => 'Prefix',
        'col_netmask' => 'Netmask',
        'col_wildcard'=> 'Wildcard',
        'col_total'   => 'Total addresses',
        'col_usable'  => 'Usable hosts',
        'col_note'    => 'Notes',
    ],

    'vlan_calculator' => [
        'title'       => 'VLAN Calculator',
        'description' => 'Split a base network into equal-sized subnets and assign sequential VLAN IDs.',
        'menu'        => 'VLAN Calculator',

        'input_network'     => 'Base network',
        'input_base_cidr'   => 'Base network prefix',
        'input_subnet_cidr' => 'Subnet prefix (per VLAN)',
        'input_start_vlan'  => 'Starting VLAN ID',
        'placeholder_network' => 'e.g. 10.0.0.0',
        'calculate'         => 'Calculate',

        'result_title'        => 'VLAN Allocation',
        'result_base'         => 'Base network',
        'result_total'        => 'Total subnets',
        'result_shown'        => 'Shown',
        'result_truncated'    => 'Results limited to the first :max — the network contains :total subnets in total.',

        'col_vlan'      => 'VLAN ID',
        'col_network'   => 'Subnet',
        'col_gateway'   => 'Gateway',
        'col_broadcast' => 'Broadcast',
        'col_netmask'   => 'Netmask',
        'col_usable'    => 'Hosts',

        'empty' => 'Enter the parameters and press Calculate to see the allocation.',

        'error_network_required'     => 'The base network address is required.',
        'error_network_invalid'      => 'The base network address is not a valid IPv4.',
        'error_base_cidr_required'   => 'The base network prefix is required.',
        'error_base_cidr_range'      => 'The base network prefix must be between 0 and 30.',
        'error_subnet_cidr_required' => 'The subnet prefix is required.',
        'error_subnet_cidr_range'    => 'The subnet prefix must be between 1 and 32.',
        'error_subnet_too_small'     => 'The subnet prefix must be greater than the base network prefix.',
        'error_start_vlan_required'  => 'The starting VLAN ID is required.',
        'error_start_vlan_range'     => 'The starting VLAN ID must be between 1 and 4094.',
    ],

    'dns_lookup' => [
        'title'       => 'DNS Lookup',
        'description' => 'Query DNS records for a hostname or IP address (A, AAAA, MX, NS, TXT, CNAME, SOA, PTR).',
        'menu'        => 'DNS Lookup',

        'input_host'  => 'Hostname or IP address',
        'input_type'  => 'Record type',
        'lookup'      => 'Resolve',
        'placeholder_host' => 'e.g. example.com or 8.8.8.8',

        'result_title'   => 'Result',
        'result_host'    => 'Query',
        'result_type'    => 'Type',
        'result_count'   => ':count record(s) found',
        'result_none'    => 'No records found for this query.',

        'col_type'    => 'Type',
        'col_ttl'     => 'TTL',
        'col_data'    => 'Data',

        'empty' => 'Enter a hostname or IP and choose the record type.',

        'error_host_required' => 'The hostname is required.',
        'error_host_invalid'  => 'Please enter a valid hostname or IP address.',
        'error_host_too_long' => 'The hostname cannot exceed 253 characters.',
        'error_type_required' => 'The record type is required.',
        'error_type_invalid'  => 'The selected record type is not valid.',
        'error_throttle'      => 'Too many requests: 60 queries per second limit reached. Please wait a moment.',
    ],

    'ip_geolocation' => [
        'title'       => 'IP Geolocation',
        'description' => 'Get geographic and network information for an IPv4 or IPv6 address.',
        'menu'        => 'IP Geolocation',

        'input_ip'    => 'IP address',
        'placeholder' => 'e.g. 8.8.8.8 or 2001:db8::1',
        'lookup'      => 'Look up',

        'result_title'    => 'Result',
        'field_ip'        => 'IP address',
        'field_country'   => 'Country',
        'field_region'    => 'Region',
        'field_city'      => 'City',
        'field_zip'       => 'ZIP / Postal code',
        'field_coords'    => 'Coordinates',
        'field_timezone'  => 'Timezone',
        'field_isp'       => 'ISP',
        'field_org'       => 'Organization',
        'field_as'        => 'AS Number',

        'open_map'  => 'Open map',
        'empty'     => 'Enter an IP address to see geographic information.',
        'no_coords' => 'Coordinates not available',

        'error_ip_required'      => 'The IP address is required.',
        'error_ip_invalid'       => 'Please enter a valid IPv4 or IPv6 address.',
        'error_api_unavailable'  => 'The geolocation service is unreachable. Please try again later.',
    ],

    'osi_model' => [
        'title'       => 'OSI Model — Reference',
        'description' => 'The 7 layers of the OSI model with protocols, data units (PDU) and associated devices.',
        'menu'        => 'OSI Model',

        'col_layer'     => 'Layer',
        'col_name'      => 'Name',
        'col_pdu'       => 'PDU',
        'col_protocols' => 'Protocols / Standards',
        'col_devices'   => 'Devices',
        'col_desc'      => 'Description',

        'layer_7_application_name' => 'Application',
        'layer_7_application_desc' => 'Direct interface with user applications. Provides network services such as file transfer, email and web browsing.',

        'layer_6_presentation_name' => 'Presentation',
        'layer_6_presentation_desc' => 'Translates, encrypts and compresses data. Ensures the data format is understandable by the Application layer.',

        'layer_5_session_name' => 'Session',
        'layer_5_session_desc' => 'Manages the opening, maintaining and closing of communication sessions between applications.',

        'layer_4_transport_name' => 'Transport',
        'layer_4_transport_desc' => 'Ensures reliable end-to-end data delivery, handling flow control, error correction and port multiplexing.',

        'layer_3_network_name' => 'Network',
        'layer_3_network_desc' => 'Handles logical addressing (IP) and routing of packets across different networks.',

        'layer_2_data_link_name' => 'Data Link',
        'layer_2_data_link_desc' => 'Manages reliable frame transmission between adjacent nodes on the same network, including MAC addressing and error detection.',

        'layer_1_physical_name' => 'Physical',
        'layer_1_physical_desc' => 'Transmits raw bits over the physical medium. Defines electrical, mechanical and functional characteristics of the link.',
    ],

    'ping_traceroute' => [
        'title'       => 'Ping / Traceroute',
        'description' => 'Check host reachability and trace the path of packets across the network.',
        'menu'        => 'Ping / Traceroute',

        'input_target'       => 'Host / IP Address',
        'placeholder_target' => 'e.g. 8.8.8.8 or example.com',
        'input_tool'         => 'Tool',
        'tool_ping'          => 'Ping',
        'tool_traceroute'    => 'Traceroute',
        'input_count'        => 'Ping count',
        'input_hops'         => 'Max hops',
        'run'                => 'Run',
        'running'            => 'Running…',

        'result_title'    => 'Result',
        'result_exit_ok'  => 'Completed successfully',
        'result_exit_err' => 'Completed with errors (exit :code)',
        'empty'           => 'Enter a host and press Run.',

        'note_traceroute' => 'Traceroute may take up to 90 seconds.',

        'error_target_required' => 'The destination host is required.',
        'error_target_invalid'  => 'Please enter a valid IP address or hostname (e.g. example.com).',
        'error_tool_required'   => 'Please select a tool (ping or traceroute).',
        'error_tool_invalid'    => 'Invalid tool.',
        'error_binary_missing'  => 'The requested command is not available on this system.',
    ],

    'linux_cheatsheet' => [
        'title'       => 'Linux Commands Cheatsheet',
        'description' => 'Quick reference for the most common Linux commands, organized by category. Use the search to filter.',
        'menu'        => 'Linux Cheatsheet',

        'search_placeholder' => 'Filter by command or description…',
        'no_results'         => 'No commands match your search.',
        'col_command'        => 'Command',
        'col_description'    => 'Description',
        'col_example'        => 'Example',

        'cat_filesystem' => 'Filesystem',
        'cat_text'       => 'Text & Search',
        'cat_processes'  => 'Processes & Services',
        'cat_network'    => 'Network',
        'cat_system'     => 'System & Hardware',
        'cat_users'      => 'Users & Permissions',
        'cat_archives'   => 'Archives & Compression',
        'cat_disk'       => 'Disk & Partitions',
    ],

    'cable_colors' => [
        'title'       => 'Cable Colors T568A / T568B',
        'description' => 'Visual reference for RJ45 network cable color coding per T568A and T568B standards.',
        'menu'        => 'RJ45 Cable Colors',

        'col_pin'       => 'Pin',
        'col_color'     => 'Color',
        'col_pair'      => 'Pair',
        'col_func_fast' => '10/100 Mb',
        'col_func_gig'  => '1 Gb (802.3ab)',

        'differs_badge'  => 'differs',
        'stripe_label'   => 'stripe',

        'pair_blue'   => 'Pair 1 — Blue',
        'pair_orange' => 'Pair 2 — Orange',
        'pair_green'  => 'Pair 3 — Green',
        'pair_brown'  => 'Pair 4 — Brown',

        'color_white_green'  => 'White/Green',
        'color_green'        => 'Green',
        'color_white_orange' => 'White/Orange',
        'color_orange'       => 'Orange',
        'color_white_blue'   => 'White/Blue',
        'color_blue'         => 'Blue',
        'color_white_brown'  => 'White/Brown',
        'color_brown'        => 'Brown',

        'note_diff'  => 'Highlighted pins differ between T568A and T568B (pairs 2 and 3 are swapped).',
        'note_usage' => 'T568B is the most common standard in commercial installations. T568A is required for US government installations (TIA-568). Both are acceptable as long as the same standard is used on both ends.',
        'legend'     => 'Pair legend',
    ],

    'rfc_browser' => [
        'title'       => 'RFC Browser',
        'description' => 'Browse and search the most important Internet RFCs by number, title, category or status.',
        'menu'        => 'RFC Browser',

        'search_placeholder' => 'Search by number, title or keyword…',
        'no_results'         => 'No RFC matches the search criteria.',
        'filter_all'         => 'All',
        'filter_category'    => 'Category',
        'filter_status'      => 'Status',
        'count_unit'         => 'RFC',

        'col_number'   => 'RFC',
        'col_title'    => 'Title',
        'col_year'     => 'Year',
        'col_status'   => 'Status',
        'col_category' => 'Category',
        'col_link'     => 'Link',
        'open_rfc'     => 'Open',

        'cat_networking'  => 'Networking',
        'cat_routing'     => 'Routing',
        'cat_dns'         => 'DNS',
        'cat_email'       => 'Email',
        'cat_web'         => 'Web',
        'cat_security'    => 'Security',
        'cat_management'  => 'Management',
        'cat_reference'   => 'References',

        'status_std'  => 'Internet Standard',
        'status_ps'   => 'Proposed Standard',
        'status_bcp'  => 'Best Current Practice',
        'status_info' => 'Informational',
        'status_hist' => 'Historic',

        'note_source' => 'Official source: IETF RFC Editor — rfc-editor.org',
    ],

    'http_status_codes' => [
        'title'       => 'HTTP Status Codes',
        'description' => 'Complete reference of HTTP status codes with description and defining RFC.',
        'menu'        => 'HTTP Status Codes',

        'search_placeholder' => 'Search by code or name…',
        'no_results'         => 'No code matches the search.',
        'filter_all'         => 'All',
        'count_unit'         => 'codes',

        'col_code'  => 'Code',
        'col_name'  => 'Name',
        'col_desc'  => 'Description',
        'col_rfc'   => 'RFC',

        'cat_1xx' => '1xx — Informational',
        'cat_2xx' => '2xx — Success',
        'cat_3xx' => '3xx — Redirection',
        'cat_4xx' => '4xx — Client Error',
        'cat_5xx' => '5xx — Server Error',
    ],

    'mac_lookup' => [
        'title'       => 'MAC Address Lookup',
        'description' => 'Identify the manufacturer of a device from its MAC address (OUI).',
        'menu'        => 'MAC Address Lookup',

        'input_mac'       => 'MAC Address',
        'placeholder_mac' => 'e.g. 00:1A:2B or 00:1A:2B:3C:4D:5E',
        'lookup'          => 'Look up',

        'result_title'  => 'Result',
        'field_vendor'  => 'Manufacturer',
        'field_oui'     => 'OUI (first 3 octets)',
        'field_nic'     => 'NIC (last 3 octets)',
        'field_type'    => 'Type',
        'field_formats' => 'Formats',

        'type_unicast'    => 'Unicast',
        'type_multicast'  => 'Multicast',
        'type_global'     => 'Globally administered (IEEE)',
        'type_local'      => 'Locally administered',

        'oui_only_badge'  => 'OUI only',
        'vendor_unknown'  => 'Manufacturer not found',
        'vendor_hint'     => 'The embedded OUI database covers the most common vendors. For a full search consult IEEE RA.',

        'empty'           => 'Enter a MAC address to identify the manufacturer.',
        'error_required'  => 'The MAC address is required.',
        'error_invalid'   => 'Invalid MAC address. Accepted formats: AA:BB:CC (OUI only) or AA:BB:CC:DD:EE:FF (full MAC), with separators : - . or none.',
    ],

    'whois' => [
        'title'       => 'Whois Lookup',
        'description' => 'Query the Whois registry for domains and IP addresses.',
        'menu'        => 'Whois Lookup',

        'input_target'       => 'Domain or IP address',
        'placeholder_target' => 'e.g. example.com or 8.8.8.8',
        'lookup'             => 'Look up',
        'running'            => 'Running…',

        'result_title'    => 'Whois Result',
        'result_for'      => 'Query for:',
        'truncated_notice'=> 'Output truncated to the first :n lines.',
        'empty'           => 'Enter a domain or IP address and press Look up.',

        'error_target_required' => 'The domain or IP address is required.',
        'error_target_invalid'  => 'Please enter a valid domain (e.g. example.com) or IP address.',
        'error_binary_missing'  => 'The whois command is not available on this system.',
        'error_lookup_failed'   => 'The Whois query returned no results.',
    ],

    'regex_tester' => [
        'title'       => 'Regex Tester',
        'description' => 'Test regular expressions with match highlighting and capture group support.',
        'menu'        => 'Regex Tester',

        'label_pattern'           => 'Pattern',
        'placeholder_pattern'     => 'e.g. \b\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\b',
        'label_flags'             => 'Flags',
        'flag_i'                  => 'i — case-insensitive',
        'flag_m'                  => 'm — multiline',
        'flag_s'                  => 's — dotall',
        'flag_u'                  => 'u — unicode',
        'label_subject'           => 'Test string',
        'placeholder_subject'     => 'Paste the text to test here…',
        'label_replacement'       => 'Replacement (optional)',
        'placeholder_replacement' => 'e.g. [REDACTED] or $1-$2',
        'btn_test'                => 'Test',

        'match_count_one'   => '1 match found',
        'match_count_many'  => ':count matches found',
        'match_count_zero'  => 'No matches',
        'truncated_notice'  => 'Showing first :n matches.',
        'section_highlight' => 'Highlighted text',
        'section_matches'   => 'Matches',
        'section_replace'   => 'Replacement result',
        'col_n'             => '#',
        'col_match'         => 'Match',
        'col_start'         => 'Start',
        'col_end'           => 'End',
        'col_groups'        => 'Groups',
        'group_label'       => 'Group :n',
        'no_groups'         => '—',
        'idle'              => 'Enter a pattern and a test string to begin.',
        'error_invalid'     => 'Invalid pattern:',
    ],

    'ssl_checker' => [
        'title'       => 'SSL/TLS Checker',
        'description' => 'Check an SSL/TLS certificate: validity, expiry, SANs and protocol details.',
        'menu'        => 'SSL/TLS Checker',

        'label_host'         => 'Hostname',
        'placeholder_host'   => 'e.g. example.com',
        'label_port'         => 'Port',
        'placeholder_port'   => '443',
        'btn_check'          => 'Check',
        'running'            => 'Connecting…',

        'status_valid'       => 'Valid certificate',
        'status_expired'     => 'Certificate expired',
        'status_expiring'    => 'Expiring soon',
        'status_error'       => 'Connection error',
        'status_trusted'     => 'Chain trusted',
        'status_untrusted'   => 'Chain not trusted',
        'status_trust_unknown' => 'Trust not verified',

        'days_left_many'     => ':n days remaining',
        'days_left_one'      => '1 day remaining',
        'days_left_zero'     => 'Expired',

        'section_cert'       => 'Certificate details',
        'section_tls'        => 'TLS protocol',
        'section_sans'       => 'Subject Alternative Names',

        'field_subject_cn'   => 'Common Name',
        'field_subject_o'    => 'Organization',
        'field_issuer_cn'    => 'Issued by',
        'field_issuer_o'     => 'Issuer Org',
        'field_valid_from'   => 'Valid from',
        'field_valid_to'     => 'Valid to',
        'field_serial'       => 'Serial number',
        'field_fingerprint'  => 'SHA-256 fingerprint',
        'field_protocol'     => 'Protocol',
        'field_cipher'       => 'Cipher suite',
        'field_bits'         => 'Bits',

        'empty'              => 'Enter a hostname and press Check.',
        'error_host_required'=> 'The hostname is required.',
        'error_host_invalid' => 'Please enter a valid hostname (e.g. example.com).',
        'error_port_invalid' => 'Port must be a number between 1 and 65535.',
    ],

    'base_converter' => [
        'title'       => 'Base Converter',
        'description' => 'Convert numbers between binary, octal, decimal, hexadecimal, and any base from 2 to 36.',
        'menu'        => 'Base Converter',

        'label_number'      => 'Number',
        'placeholder_number'=> 'Enter a number…',
        'label_from_base'   => 'Source base',
        'btn_convert'       => 'Convert',

        'base_bin'   => 'BIN',
        'base_oct'   => 'OCT',
        'base_dec'   => 'DEC',
        'base_hex'   => 'HEX',

        'section_results' => 'Results',
        'field_bit_length'=> 'Bit length',
        'field_ascii'     => 'ASCII character',

        'idle'              => 'Enter a number and select the source base.',
        'error_invalid_chars' => 'The number contains characters not valid for the selected base.',
        'error_overflow'    => 'Number too large (limit: signed 64-bit integer).',
        'error_base_required'=> 'Source base is required.',
        'error_base_invalid' => 'Base must be an integer between 2 and 36.',
    ],

    'bandwidth_calculator' => [
        'title'       => 'Bandwidth Calculator',
        'description' => 'Calculate transfer time, transferable file size, or required bandwidth from any two known values.',
        'menu'        => 'Bandwidth Calculator',

        'mode_time'      => 'Transfer time',
        'mode_size'      => 'Transferable size',
        'mode_bandwidth' => 'Required bandwidth',

        'label_file_size'      => 'File size',
        'label_bandwidth'      => 'Available bandwidth',
        'label_time'           => 'Time',
        'label_overhead'       => 'Protocol overhead (%)',
        'placeholder_overhead' => 'e.g. 4',
        'btn_calculate'        => 'Calculate',

        'result_time'      => 'Transfer time',
        'result_size'      => 'Transferable data',
        'result_bandwidth' => 'Required bandwidth',
        'throughput'       => 'Effective throughput',
        'section_details'  => 'Unit breakdown',
        'col_unit'         => 'Unit',
        'col_value'        => 'Value',

        'error_positive'   => 'Values must be greater than zero.',
        'error_validation' => 'Invalid input.',
    ],

    'formatter' => [
        'title'       => 'Formatter',
        'description' => 'Format and indent JSON, XML, and HTML with syntax highlighting.',
        'menu'        => 'Formatter',

        'label_input'       => 'Input',
        'placeholder_input' => 'Paste your JSON, XML, or HTML here…',
        'label_format'      => 'Format',
        'label_indent'      => 'Indentation',
        'btn_format'        => 'Format',

        'format_auto' => 'Auto',
        'format_json' => 'JSON',
        'format_xml'  => 'XML',
        'format_html' => 'HTML',
        'indent_2'    => '2 spaces',
        'indent_4'    => '4 spaces',

        'section_output'  => 'Output',
        'detected_format' => 'Format',
        'lines_count'     => 'Lines',
        'size_in'         => 'Input',
        'size_out'        => 'Output',
        'btn_copy'        => 'Copy',
        'copied'          => 'Copied!',

        'idle'                 => 'Paste some text and press Format.',
        'error_invalid'        => 'The content is not valid :format.',
        'error_too_large'      => 'Input exceeds the 200 KB limit.',
        'error_unknown_format' => 'Format not recognised. Select JSON, XML or HTML manually.',
    ],

    'port_checker' => [
        'title'       => 'Port Checker',
        'description' => 'Check whether a TCP or UDP port is open on a remote host.',
        'menu'        => 'Port Checker',

        'label_host'        => 'Host / IP Address',
        'placeholder_host'  => 'e.g. example.com or 192.168.1.1',
        'label_port'        => 'Port',
        'placeholder_port'  => 'e.g. 80',
        'label_protocol'    => 'Protocol',
        'label_captcha'     => 'Type the code shown above',
        'placeholder_captcha' => 'Enter the code',
        'btn_check'         => 'Check',

        'status_open'         => 'Open',
        'status_closed'       => 'Closed',
        'status_filtered'     => 'Filtered',
        'status_open_filtered'=> 'Open / Filtered',

        'result_host'      => 'Host',
        'result_port'      => 'Port',
        'result_protocol'  => 'Protocol',
        'result_latency'   => 'Latency',
        'result_latency_ms'=> ':ms ms',

        'udp_note' => 'UDP: no response does not guarantee the port is open — firewalls may silently drop ICMP packets.',

        'error_host_required'     => 'Host is required.',
        'error_host_invalid'      => 'Enter a valid hostname or IP address.',
        'error_port_required'     => 'Port is required.',
        'error_port_invalid'      => 'Port must be a number between 1 and 65535.',
        'error_protocol_invalid'  => 'Select TCP or UDP.',
        'error_captcha_required'  => 'Enter the verification code.',
        'error_captcha'           => 'Incorrect code. A new code has been generated.',
        'error_throttle'          => 'Too many requests. Please try again in a minute.',
    ],

    'markdown_viewer' => [
        'title'       => 'Markdown Viewer',
        'description' => 'Paste Markdown text to preview the rendered output and export it as HTML or PDF.',
        'menu'        => 'Markdown Viewer',

        'label_input'         => 'Markdown Input',
        'placeholder_input'   => "# Title\n\nWrite your **Markdown** here...",
        'label_preview'       => 'Preview',
        'placeholder_preview' => 'Preview will appear here as you type.',

        'btn_export_html' => 'Download HTML',
        'btn_export_pdf'  => 'Download PDF',

        'hint_limit'  => 'Limit: 100,000 characters.',

        'error_too_large' => 'The text exceeds the 100,000 character limit.',
    ],

    'email_header_analyzer' => [
        'title'       => 'Email Header Analyzer',
        'description' => 'Analyze an email header: delivery hop trace with per-hop delays, SPF/DKIM/DMARC results, and a summary of key fields.',
        'menu'        => 'Email Header Analyzer',

        'label_input'          => 'Raw email header',
        'placeholder_input'    => "Paste the full email header here (right-click → View source → copy the header section)…",
        'btn_analyze'          => 'Analyze',
        'empty'                => 'Paste an email header to see the analysis.',

        'section_summary'      => 'Summary',
        'section_trace'        => 'Delivery trace',
        'section_auth'         => 'Authentication',
        'section_all_headers'  => 'All headers',
        'btn_show_headers'     => 'Show all headers',
        'btn_hide_headers'     => 'Hide headers',

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
        'col_delay'     => 'Delay',
        'row_total'     => 'Total',
        'no_hops'       => 'No Received: headers found.',

        'auth_spf'   => 'SPF',
        'auth_dkim'  => 'DKIM',
        'auth_dmarc' => 'DMARC',
        'auth_raw'   => 'Raw Authentication-Results',
        'auth_none'  => 'Absent',

        'error_no_headers' => 'No recognisable headers found. Make sure the pasted text contains valid email headers.',
        'error_too_large'  => 'The header exceeds the 50,000 character limit.',
    ],

    'email_deliverability' => [
        'title'       => 'Email Deliverability Checker',
        'description' => 'Check the DNS records for a domain\'s email deliverability: MX, SPF, DMARC and DKIM.',
        'menu'        => 'Deliverability Checker',

        'label_domain'          => 'Domain',
        'placeholder_domain'    => 'e.g. example.com or user@example.com',
        'label_dkim_selector'   => 'DKIM Selector',
        'placeholder_dkim_selector' => 'e.g. google, default…',
        'optional'              => 'optional',
        'btn_check'             => 'Check',
        'empty'                 => 'Enter a domain to see the analysis.',
        'hint_dkim'             => 'If no DKIM selector is entered, common ones are tried automatically (google, default, k1, mail…).',

        'checking'      => 'Results for',
        'found'         => 'Found',
        'not_found'     => 'Not found',

        'col_priority'  => 'Priority',
        'col_host'      => 'Host',

        'mx_none'       => 'No MX records found for this domain.',
        'spf_none'      => 'No SPF record found.',
        'spf_mechanism' => 'All mechanism:',
        'dmarc_none'    => 'No DMARC record found.',
        'dmarc_policy'  => 'Policy',
        'dmarc_sp'      => 'Subdomain policy',
        'dmarc_pct'     => 'Percentage',
        'dmarc_rua'     => 'Aggregate reports',
        'dkim_selector' => 'Selector:',
        'dkim_none'     => 'No DKIM record found with this selector.',
        'dkim_not_found_selector' => 'none found automatically',

        'error_domain_required' => 'Domain is required.',
        'error_domain_invalid'  => 'Enter a valid domain or email address.',
        'error_domain_too_long' => 'Domain cannot exceed 253 characters.',
    ],

    'blacklist_checker' => [
        'title'       => 'Blacklist / RBL Checker',
        'description' => 'Check whether an IPv4 address or domain is listed in the major DNS blacklists (DNSBL/RBL) used for spam filtering.',
        'menu'        => 'Blacklist Checker',

        'label_target'       => 'IP Address or Domain',
        'placeholder_target' => 'e.g. 1.2.3.4 or example.com',
        'btn_check'          => 'Check',
        'empty'              => 'Enter an IP address or domain to check it.',
        'hint'               => 'Also accepts email addresses (e.g. user@example.com) — the domain will be extracted.',

        'summary_clean'   => 'Not listed in any blacklist',
        'summary_listed'  => 'Listed in one or more blacklists',
        'checked_ip'      => 'Checked IP:',
        'resolved_from'   => 'resolved from',
        'checked_domain'  => 'Checked domain:',
        'ip_unresolved'   => 'IP could not be resolved, only domain RBLs checked',

        'col_name'    => 'Blacklist',
        'col_zone'    => 'DNS Zone',
        'col_type'    => 'Type',
        'col_status'  => 'Status',
        'col_detail'  => 'Detail',

        'status_listed' => 'Listed',
        'status_clean'  => 'Clean',

        'error_target_required' => 'Enter an IP address or domain.',
        'error_target_invalid'  => 'Enter a valid IPv4 address or a valid domain name.',
        'error_target_too_long' => 'Input cannot exceed 253 characters.',
    ],

    'mx_checker' => [
        'title'       => 'Advanced MX Checker',
        'description' => 'Check a domain\'s MX servers: priority, IP addresses, SMTP port 25 reachability, greeting banner and EHLO capabilities (STARTTLS, AUTH, SIZE).',
        'menu'        => 'MX Checker',

        'label_domain'       => 'Domain',
        'placeholder_domain' => 'e.g. example.com or user@example.com',
        'btn_check'          => 'Check',
        'empty'              => 'Enter a domain to analyse its MX servers.',
        'hint'               => 'Also accepts email addresses — the domain will be extracted. The SMTP test on port 25 may take a few seconds.',

        'no_mx'          => 'No MX records found for',
        'server_count'   => 'MX servers for :domain',
        'priority'       => 'Priority',
        'reachable'      => 'Reachable',
        'unreachable'    => 'Unreachable',
        'ip_unresolvable'=> 'IP address could not be resolved.',

        'label_banner'       => 'Banner:',
        'label_capabilities' => 'Capabilities:',
        'show_ehlo'          => 'Full EHLO response',

        'port25_blocked' => 'Port 25 unreachable — it may be blocked by the provider firewall or local network.',

        'label_captcha'       => 'Type the code shown above',
        'placeholder_captcha' => 'Enter the code',

        'error_domain_required'  => 'Domain is required.',
        'error_domain_invalid'   => 'Enter a valid domain or email address.',
        'error_domain_too_long'  => 'Domain cannot exceed 253 characters.',
        'error_captcha_required' => 'Enter the verification code.',
        'error_captcha'          => 'Incorrect code. A new code has been generated.',
    ],

    'email_validator' => [
        'title'       => 'Email Validator',
        'description' => 'Check if an email address is valid: validates syntax, domain MX records, and mailbox reachability via SMTP.',
        'menu'        => 'Email Validator',

        'label_email'       => 'Email address',
        'placeholder_email' => 'e.g. user@example.com',
        'btn_check'         => 'Check',
        'empty'             => 'Enter an email address to check its validity.',
        'hint'              => 'The SMTP check connects to the domain mail server — some providers block verification and the result may be "unverifiable".',

        'label_captcha'       => 'Type the code shown above',
        'placeholder_captcha' => 'Enter the code',

        'section_syntax' => 'Syntax',
        'section_mx'     => 'MX Records',
        'section_smtp'   => 'SMTP Check',
        'section_result' => 'Result',

        'syntax_valid'   => 'Valid',
        'syntax_invalid' => 'Invalid',
        'local_part'     => 'Local part',
        'domain_part'    => 'Domain',

        'mx_found'       => ':count MX record(s) found',
        'mx_not_found'   => 'No MX records — domain cannot receive email',
        'mx_fallback'    => 'No MX records, but domain has an A record (fallback)',
        'mx_host'        => 'Server',
        'mx_priority'    => 'Priority',

        'smtp_valid'       => 'Mailbox accepted',
        'smtp_invalid'     => 'Mailbox rejected',
        'smtp_catchall'    => 'Catch-all: domain accepts any address',
        'smtp_risky'       => 'Temporary response — may be an anti-spam block',
        'smtp_unavailable' => 'Unverifiable: server does not allow SMTP checks',
        'smtp_skipped'     => 'Not performed',
        'smtp_code'        => 'Response code',

        'overall_valid'   => 'Valid address',
        'overall_invalid' => 'Invalid address',
        'overall_unknown' => 'Unverifiable',
        'overall_risky'   => 'Uncertain result',

        'error_email_required'   => 'Enter an email address.',
        'error_email_too_long'   => 'Email address cannot exceed 254 characters.',
        'error_captcha_required' => 'Enter the verification code.',
        'error_captcha'          => 'Incorrect code. A new code has been generated.',
    ],
];
