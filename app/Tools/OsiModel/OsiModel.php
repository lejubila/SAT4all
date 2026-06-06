<?php

namespace App\Tools\OsiModel;

class OsiModel
{
    /**
     * Dati statici dei 7 layer OSI (dall'Application al Physical).
     * Le chiavi di traduzione per nome e descrizione sono in lang/{it,en}/tools.php.
     *
     * @return array<int, array{
     *   number: int,
     *   key: string,
     *   pdu: string,
     *   protocols: string[],
     *   devices: string[],
     *   color: string,
     * }>
     */
    public static function layers(): array
    {
        return [
            [
                'number'    => 7,
                'key'       => 'application',
                'pdu'       => 'Data',
                'protocols' => ['HTTP', 'HTTPS', 'FTP', 'SMTP', 'DNS', 'DHCP', 'SSH', 'Telnet', 'SNMP', 'POP3', 'IMAP'],
                'devices'   => ['Host', 'Server', 'Firewall applicativo'],
                'color'     => 'amber',
            ],
            [
                'number'    => 6,
                'key'       => 'presentation',
                'pdu'       => 'Data',
                'protocols' => ['SSL/TLS', 'JPEG', 'MPEG', 'GIF', 'PNG', 'ASCII', 'EBCDIC', 'MIME'],
                'devices'   => ['Gateway'],
                'color'     => 'yellow',
            ],
            [
                'number'    => 5,
                'key'       => 'session',
                'pdu'       => 'Data',
                'protocols' => ['NetBIOS', 'RPC', 'PPTP', 'SAP', 'SQL', 'NFS', 'SMB'],
                'devices'   => ['Gateway'],
                'color'     => 'lime',
            ],
            [
                'number'    => 4,
                'key'       => 'transport',
                'pdu'       => 'Segment / Datagram',
                'protocols' => ['TCP', 'UDP', 'SCTP', 'DCCP'],
                'devices'   => ['Firewall', 'Load balancer'],
                'color'     => 'emerald',
            ],
            [
                'number'    => 3,
                'key'       => 'network',
                'pdu'       => 'Packet',
                'protocols' => ['IPv4', 'IPv6', 'ICMP', 'ICMPv6', 'OSPF', 'BGP', 'RIP', 'EIGRP'],
                'devices'   => ['Router', 'Switch L3', 'Firewall'],
                'color'     => 'sky',
            ],
            [
                'number'    => 2,
                'key'       => 'data_link',
                'pdu'       => 'Frame',
                'protocols' => ['Ethernet', 'Wi-Fi (802.11)', 'PPP', 'ARP', 'STP', 'VLAN (802.1Q)', 'LACP'],
                'devices'   => ['Switch L2', 'Bridge', 'NIC', 'Access point'],
                'color'     => 'violet',
            ],
            [
                'number'    => 1,
                'key'       => 'physical',
                'pdu'       => 'Bit',
                'protocols' => ['Ethernet (cavo)', 'DSL', 'ISDN', 'RS-232', 'USB', 'Bluetooth', 'Fiber'],
                'devices'   => ['Hub', 'Repeater', 'Cavo', 'Modem', 'Transceiver'],
                'color'     => 'slate',
            ],
        ];
    }

    /**
     * Mappa colore Tailwind → classi CSS per badge e bordo.
     *
     * @return array<string, array{badge: string, border: string, num_bg: string, num_text: string}>
     */
    public static function colorClasses(): array
    {
        return [
            'amber'   => ['badge' => 'bg-amber-100 text-amber-800',   'border' => 'border-amber-300',   'num_bg' => 'bg-amber-400',   'num_text' => 'text-slate-900'],
            'yellow'  => ['badge' => 'bg-yellow-100 text-yellow-800',  'border' => 'border-yellow-300',  'num_bg' => 'bg-yellow-400',  'num_text' => 'text-slate-900'],
            'lime'    => ['badge' => 'bg-lime-100 text-lime-800',      'border' => 'border-lime-300',    'num_bg' => 'bg-lime-400',    'num_text' => 'text-slate-900'],
            'emerald' => ['badge' => 'bg-emerald-100 text-emerald-800','border' => 'border-emerald-300', 'num_bg' => 'bg-emerald-500', 'num_text' => 'text-white'],
            'sky'     => ['badge' => 'bg-sky-100 text-sky-800',        'border' => 'border-sky-300',     'num_bg' => 'bg-sky-500',     'num_text' => 'text-white'],
            'violet'  => ['badge' => 'bg-violet-100 text-violet-800',  'border' => 'border-violet-300',  'num_bg' => 'bg-violet-500',  'num_text' => 'text-white'],
            'slate'   => ['badge' => 'bg-slate-100 text-slate-700',    'border' => 'border-slate-300',   'num_bg' => 'bg-slate-500',   'num_text' => 'text-white'],
        ];
    }
}
