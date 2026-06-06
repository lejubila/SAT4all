<?php

namespace App\Tools\PortReference;

/**
 * Riferimento delle porte TCP/UDP piu' comuni con logica di ricerca.
 *
 * Dataset statico (nessun database) + filtro per testo libero e protocollo.
 * Logica pura: nessuna dipendenza da Laravel o dal layer HTTP.
 */
class PortReference
{
    private ?string $query;

    private string $protocol;

    /**
     * @param array{q?: string|null, protocol?: string|null} $input
     */
    public function __construct(array $input = [])
    {
        $this->query = isset($input['q']) ? trim((string) $input['q']) : null;
        $this->protocol = $input['protocol'] ?? 'all';
    }

    /**
     * Applica i filtri correnti e ritorna le porte corrispondenti.
     *
     * @return array<int, array{port: int, protocol: string, service: string, description: string}>
     */
    public function filter(): array
    {
        return array_values(array_filter(self::all(), function (array $entry): bool {
            return $this->matchesProtocol($entry) && $this->matchesQuery($entry);
        }));
    }

    /**
     * @param array{port: int, protocol: string} $entry
     */
    private function matchesProtocol(array $entry): bool
    {
        if ($this->protocol === 'all') {
            return true;
        }

        return str_contains($entry['protocol'], $this->protocol);
    }

    /**
     * @param array{port: int, service: string, description: string} $entry
     */
    private function matchesQuery(array $entry): bool
    {
        if ($this->query === null || $this->query === '') {
            return true;
        }

        $needle = $this->query;

        if (ctype_digit($needle)) {
            return str_contains((string) $entry['port'], $needle);
        }

        return stripos($entry['service'], $needle) !== false
            || stripos($entry['description'], $needle) !== false;
    }

    /**
     * Opzioni protocollo per la select.
     *
     * @return array<int, string>
     */
    public static function protocols(): array
    {
        return ['all', 'tcp', 'udp'];
    }

    /**
     * Dataset completo delle porte note.
     *
     * @return array<int, array{port: int, protocol: string, service: string, description: string}>
     */
    public static function all(): array
    {
        return [
            ['port' => 20,    'protocol' => 'tcp',     'service' => 'FTP-DATA',     'description' => 'File Transfer Protocol (canale dati)'],
            ['port' => 21,    'protocol' => 'tcp',     'service' => 'FTP',          'description' => 'File Transfer Protocol (controllo)'],
            ['port' => 22,    'protocol' => 'tcp',     'service' => 'SSH',          'description' => 'Secure Shell, SCP, SFTP'],
            ['port' => 23,    'protocol' => 'tcp',     'service' => 'Telnet',       'description' => 'Accesso remoto in chiaro'],
            ['port' => 25,    'protocol' => 'tcp',     'service' => 'SMTP',         'description' => 'Simple Mail Transfer Protocol'],
            ['port' => 37,    'protocol' => 'tcp/udp', 'service' => 'TIME',         'description' => 'Time Protocol'],
            ['port' => 53,    'protocol' => 'tcp/udp', 'service' => 'DNS',          'description' => 'Domain Name System'],
            ['port' => 67,    'protocol' => 'udp',     'service' => 'DHCP/BOOTP',   'description' => 'DHCP server (BOOTP)'],
            ['port' => 68,    'protocol' => 'udp',     'service' => 'DHCP/BOOTP',   'description' => 'DHCP client (BOOTP)'],
            ['port' => 69,    'protocol' => 'udp',     'service' => 'TFTP',         'description' => 'Trivial File Transfer Protocol'],
            ['port' => 80,    'protocol' => 'tcp',     'service' => 'HTTP',         'description' => 'HyperText Transfer Protocol'],
            ['port' => 88,    'protocol' => 'tcp/udp', 'service' => 'Kerberos',     'description' => 'Autenticazione Kerberos'],
            ['port' => 110,   'protocol' => 'tcp',     'service' => 'POP3',         'description' => 'Post Office Protocol v3'],
            ['port' => 111,   'protocol' => 'tcp/udp', 'service' => 'RPCbind',      'description' => 'ONC RPC / Portmapper'],
            ['port' => 119,   'protocol' => 'tcp',     'service' => 'NNTP',         'description' => 'Network News Transfer Protocol'],
            ['port' => 123,   'protocol' => 'udp',     'service' => 'NTP',          'description' => 'Network Time Protocol'],
            ['port' => 135,   'protocol' => 'tcp',     'service' => 'MS-RPC',       'description' => 'Microsoft RPC Endpoint Mapper'],
            ['port' => 137,   'protocol' => 'udp',     'service' => 'NetBIOS-NS',   'description' => 'NetBIOS Name Service'],
            ['port' => 138,   'protocol' => 'udp',     'service' => 'NetBIOS-DGM',  'description' => 'NetBIOS Datagram Service'],
            ['port' => 139,   'protocol' => 'tcp',     'service' => 'NetBIOS-SSN',  'description' => 'NetBIOS Session Service'],
            ['port' => 143,   'protocol' => 'tcp',     'service' => 'IMAP',         'description' => 'Internet Message Access Protocol'],
            ['port' => 161,   'protocol' => 'udp',     'service' => 'SNMP',         'description' => 'Simple Network Management Protocol'],
            ['port' => 162,   'protocol' => 'udp',     'service' => 'SNMP-TRAP',    'description' => 'SNMP Trap'],
            ['port' => 179,   'protocol' => 'tcp',     'service' => 'BGP',          'description' => 'Border Gateway Protocol'],
            ['port' => 389,   'protocol' => 'tcp/udp', 'service' => 'LDAP',         'description' => 'Lightweight Directory Access Protocol'],
            ['port' => 443,   'protocol' => 'tcp',     'service' => 'HTTPS',        'description' => 'HTTP over TLS/SSL'],
            ['port' => 445,   'protocol' => 'tcp',     'service' => 'SMB',          'description' => 'Microsoft-DS / SMB over TCP'],
            ['port' => 465,   'protocol' => 'tcp',     'service' => 'SMTPS',        'description' => 'SMTP over TLS implicito'],
            ['port' => 500,   'protocol' => 'udp',     'service' => 'IKE',          'description' => 'IPsec Internet Key Exchange'],
            ['port' => 514,   'protocol' => 'udp',     'service' => 'Syslog',       'description' => 'Syslog'],
            ['port' => 515,   'protocol' => 'tcp',     'service' => 'LPD',          'description' => 'Line Printer Daemon'],
            ['port' => 520,   'protocol' => 'udp',     'service' => 'RIP',          'description' => 'Routing Information Protocol'],
            ['port' => 587,   'protocol' => 'tcp',     'service' => 'SMTP-MSA',     'description' => 'SMTP Submission (mail client)'],
            ['port' => 631,   'protocol' => 'tcp',     'service' => 'IPP',          'description' => 'Internet Printing Protocol (CUPS)'],
            ['port' => 636,   'protocol' => 'tcp',     'service' => 'LDAPS',        'description' => 'LDAP over TLS/SSL'],
            ['port' => 989,   'protocol' => 'tcp',     'service' => 'FTPS-DATA',    'description' => 'FTP over TLS (dati)'],
            ['port' => 990,   'protocol' => 'tcp',     'service' => 'FTPS',         'description' => 'FTP over TLS (controllo)'],
            ['port' => 993,   'protocol' => 'tcp',     'service' => 'IMAPS',        'description' => 'IMAP over TLS/SSL'],
            ['port' => 995,   'protocol' => 'tcp',     'service' => 'POP3S',        'description' => 'POP3 over TLS/SSL'],
            ['port' => 1080,  'protocol' => 'tcp',     'service' => 'SOCKS',        'description' => 'SOCKS proxy'],
            ['port' => 1194,  'protocol' => 'udp',     'service' => 'OpenVPN',      'description' => 'OpenVPN'],
            ['port' => 1433,  'protocol' => 'tcp',     'service' => 'MSSQL',        'description' => 'Microsoft SQL Server'],
            ['port' => 1521,  'protocol' => 'tcp',     'service' => 'Oracle',       'description' => 'Oracle Database Listener'],
            ['port' => 1701,  'protocol' => 'udp',     'service' => 'L2TP',         'description' => 'Layer 2 Tunneling Protocol'],
            ['port' => 1723,  'protocol' => 'tcp',     'service' => 'PPTP',         'description' => 'Point-to-Point Tunneling Protocol'],
            ['port' => 1812,  'protocol' => 'udp',     'service' => 'RADIUS',       'description' => 'RADIUS Authentication'],
            ['port' => 1813,  'protocol' => 'udp',     'service' => 'RADIUS-ACCT',  'description' => 'RADIUS Accounting'],
            ['port' => 2049,  'protocol' => 'tcp/udp', 'service' => 'NFS',          'description' => 'Network File System'],
            ['port' => 2375,  'protocol' => 'tcp',     'service' => 'Docker',       'description' => 'Docker API (non cifrato)'],
            ['port' => 2376,  'protocol' => 'tcp',     'service' => 'Docker-TLS',   'description' => 'Docker API (TLS)'],
            ['port' => 3128,  'protocol' => 'tcp',     'service' => 'Squid',        'description' => 'Squid HTTP proxy'],
            ['port' => 3306,  'protocol' => 'tcp',     'service' => 'MySQL',        'description' => 'MySQL / MariaDB'],
            ['port' => 3389,  'protocol' => 'tcp',     'service' => 'RDP',          'description' => 'Remote Desktop Protocol'],
            ['port' => 3690,  'protocol' => 'tcp',     'service' => 'SVN',          'description' => 'Apache Subversion'],
            ['port' => 4500,  'protocol' => 'udp',     'service' => 'IPsec-NAT-T',  'description' => 'IPsec NAT Traversal'],
            ['port' => 5060,  'protocol' => 'tcp/udp', 'service' => 'SIP',          'description' => 'Session Initiation Protocol'],
            ['port' => 5061,  'protocol' => 'tcp',     'service' => 'SIP-TLS',      'description' => 'SIP over TLS'],
            ['port' => 5432,  'protocol' => 'tcp',     'service' => 'PostgreSQL',   'description' => 'PostgreSQL'],
            ['port' => 5672,  'protocol' => 'tcp',     'service' => 'AMQP',         'description' => 'RabbitMQ / AMQP'],
            ['port' => 5900,  'protocol' => 'tcp',     'service' => 'VNC',          'description' => 'Virtual Network Computing'],
            ['port' => 5985,  'protocol' => 'tcp',     'service' => 'WinRM',        'description' => 'Windows Remote Management (HTTP)'],
            ['port' => 5986,  'protocol' => 'tcp',     'service' => 'WinRM-HTTPS',  'description' => 'Windows Remote Management (HTTPS)'],
            ['port' => 6379,  'protocol' => 'tcp',     'service' => 'Redis',        'description' => 'Redis'],
            ['port' => 6443,  'protocol' => 'tcp',     'service' => 'Kubernetes',   'description' => 'Kubernetes API server'],
            ['port' => 8080,  'protocol' => 'tcp',     'service' => 'HTTP-ALT',     'description' => 'HTTP alternativo / proxy'],
            ['port' => 8443,  'protocol' => 'tcp',     'service' => 'HTTPS-ALT',    'description' => 'HTTPS alternativo'],
            ['port' => 8883,  'protocol' => 'tcp',     'service' => 'MQTT-TLS',     'description' => 'MQTT over TLS'],
            ['port' => 9090,  'protocol' => 'tcp',     'service' => 'Prometheus',   'description' => 'Prometheus'],
            ['port' => 9092,  'protocol' => 'tcp',     'service' => 'Kafka',        'description' => 'Apache Kafka'],
            ['port' => 9200,  'protocol' => 'tcp',     'service' => 'Elasticsearch', 'description' => 'Elasticsearch REST'],
            ['port' => 11211, 'protocol' => 'tcp/udp', 'service' => 'Memcached',    'description' => 'Memcached'],
            ['port' => 27017, 'protocol' => 'tcp',     'service' => 'MongoDB',      'description' => 'MongoDB'],
        ];
    }
}
