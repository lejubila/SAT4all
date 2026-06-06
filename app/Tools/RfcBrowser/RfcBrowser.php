<?php

namespace App\Tools\RfcBrowser;

class RfcBrowser
{
    public static function rfcs(): array
    {
        return [
            // ── Networking fundamentals ───────────────────────────────────────
            ['number' => 768,  'title' => 'User Datagram Protocol (UDP)',                    'year' => 1980, 'status' => 'std',  'category' => 'networking'],
            ['number' => 791,  'title' => 'Internet Protocol (IP)',                          'year' => 1981, 'status' => 'std',  'category' => 'networking'],
            ['number' => 792,  'title' => 'Internet Control Message Protocol (ICMP)',        'year' => 1981, 'status' => 'std',  'category' => 'networking'],
            ['number' => 793,  'title' => 'Transmission Control Protocol (TCP)',             'year' => 1981, 'status' => 'std',  'category' => 'networking'],
            ['number' => 826,  'title' => 'Ethernet Address Resolution Protocol (ARP)',      'year' => 1982, 'status' => 'std',  'category' => 'networking'],
            ['number' => 1122, 'title' => 'Requirements for Internet Hosts — Communication', 'year' => 1989, 'status' => 'std',  'category' => 'networking'],
            ['number' => 1123, 'title' => 'Requirements for Internet Hosts — Applications',  'year' => 1989, 'status' => 'std',  'category' => 'networking'],
            ['number' => 1918, 'title' => 'Address Allocation for Private Internets',        'year' => 1996, 'status' => 'bcp',  'category' => 'networking'],
            ['number' => 4291, 'title' => 'IPv6 Addressing Architecture',                   'year' => 2006, 'status' => 'ps',   'category' => 'networking'],
            ['number' => 4443, 'title' => 'ICMPv6 for IPv6',                                'year' => 2006, 'status' => 'std',  'category' => 'networking'],
            ['number' => 5737, 'title' => 'IPv4 Address Blocks Reserved for Documentation', 'year' => 2010, 'status' => 'info', 'category' => 'networking'],
            ['number' => 3849, 'title' => 'IPv6 Address Prefix Reserved for Documentation', 'year' => 2004, 'status' => 'info', 'category' => 'networking'],
            ['number' => 6890, 'title' => 'Special-Purpose IP Address Registries',          'year' => 2013, 'status' => 'bcp',  'category' => 'networking'],
            ['number' => 8200, 'title' => 'Internet Protocol, Version 6 (IPv6)',             'year' => 2017, 'status' => 'std',  'category' => 'networking'],
            ['number' => 9293, 'title' => 'Transmission Control Protocol (TCP) — revised',  'year' => 2022, 'status' => 'std',  'category' => 'networking'],

            // ── Routing ───────────────────────────────────────────────────────
            ['number' => 2453, 'title' => 'RIP Version 2',                                  'year' => 1998, 'status' => 'std',  'category' => 'routing'],
            ['number' => 2328, 'title' => 'OSPF Version 2',                                 'year' => 1998, 'status' => 'std',  'category' => 'routing'],
            ['number' => 4271, 'title' => 'BGP-4',                                          'year' => 2006, 'status' => 'ps',   'category' => 'routing'],
            ['number' => 4760, 'title' => 'Multiprotocol Extensions for BGP-4',             'year' => 2007, 'status' => 'ps',   'category' => 'routing'],
            ['number' => 4364, 'title' => 'BGP/MPLS IP Virtual Private Networks',           'year' => 2006, 'status' => 'ps',   'category' => 'routing'],
            ['number' => 5340, 'title' => 'OSPF for IPv6 (OSPFv3)',                         'year' => 2008, 'status' => 'ps',   'category' => 'routing'],
            ['number' => 4861, 'title' => 'Neighbor Discovery for IPv6',                    'year' => 2007, 'status' => 'std',  'category' => 'routing'],
            ['number' => 3031, 'title' => 'Multiprotocol Label Switching Architecture (MPLS)', 'year' => 2001, 'status' => 'ps', 'category' => 'routing'],
            ['number' => 2460, 'title' => 'Internet Protocol, Version 6 (IPv6) — original', 'year' => 1998, 'status' => 'hist', 'category' => 'routing'],

            // ── DNS ───────────────────────────────────────────────────────────
            ['number' => 1034, 'title' => 'Domain Names — Concepts and Facilities',         'year' => 1987, 'status' => 'std',  'category' => 'dns'],
            ['number' => 1035, 'title' => 'Domain Names — Implementation and Specification', 'year' => 1987, 'status' => 'std',  'category' => 'dns'],
            ['number' => 2136, 'title' => 'Dynamic Updates in the DNS (DNS UPDATE)',         'year' => 1997, 'status' => 'ps',   'category' => 'dns'],
            ['number' => 4035, 'title' => 'Protocol Modifications for DNSSEC',              'year' => 2005, 'status' => 'ps',   'category' => 'dns'],
            ['number' => 5321, 'title' => 'Simple Mail Transfer Protocol (SMTP)',            'year' => 2008, 'status' => 'std',  'category' => 'email'],
            ['number' => 6891, 'title' => 'Extension Mechanisms for DNS (EDNS(0))',          'year' => 2013, 'status' => 'std',  'category' => 'dns'],
            ['number' => 7858, 'title' => 'DNS over TLS (DoT)',                             'year' => 2016, 'status' => 'ps',   'category' => 'dns'],
            ['number' => 8484, 'title' => 'DNS Queries over HTTPS (DoH)',                   'year' => 2018, 'status' => 'ps',   'category' => 'dns'],

            // ── Email ─────────────────────────────────────────────────────────
            ['number' => 5322, 'title' => 'Internet Message Format',                        'year' => 2008, 'status' => 'std',  'category' => 'email'],
            ['number' => 1939, 'title' => 'Post Office Protocol — Version 3 (POP3)',        'year' => 1996, 'status' => 'std',  'category' => 'email'],
            ['number' => 3501, 'title' => 'INTERNET MESSAGE ACCESS PROTOCOL — Version 4rev1 (IMAP)', 'year' => 2003, 'status' => 'std', 'category' => 'email'],
            ['number' => 2045, 'title' => 'MIME Part 1: Format of Internet Message Bodies', 'year' => 1996, 'status' => 'std',  'category' => 'email'],
            ['number' => 2046, 'title' => 'MIME Part 2: Media Types',                       'year' => 1996, 'status' => 'std',  'category' => 'email'],
            ['number' => 7208, 'title' => 'Sender Policy Framework (SPF) for Authorizing Use of Domains in Email', 'year' => 2014, 'status' => 'ps', 'category' => 'email'],
            ['number' => 6376, 'title' => 'DomainKeys Identified Mail (DKIM)',              'year' => 2011, 'status' => 'std',  'category' => 'email'],
            ['number' => 7489, 'title' => 'DMARC: Domain-based Message Authentication, Reporting, and Conformance', 'year' => 2015, 'status' => 'info', 'category' => 'email'],

            // ── Web ───────────────────────────────────────────────────────────
            ['number' => 3986, 'title' => 'Uniform Resource Identifier (URI): Generic Syntax', 'year' => 2005, 'status' => 'std', 'category' => 'web'],
            ['number' => 6265, 'title' => 'HTTP State Management Mechanism (Cookies)',       'year' => 2011, 'status' => 'ps',   'category' => 'web'],
            ['number' => 6455, 'title' => 'The WebSocket Protocol',                         'year' => 2011, 'status' => 'ps',   'category' => 'web'],
            ['number' => 6797, 'title' => 'HTTP Strict Transport Security (HSTS)',           'year' => 2012, 'status' => 'ps',   'category' => 'web'],
            ['number' => 7159, 'title' => 'JSON Data Interchange Format (obsoleted)',        'year' => 2014, 'status' => 'hist', 'category' => 'web'],
            ['number' => 7540, 'title' => 'Hypertext Transfer Protocol Version 2 (HTTP/2)', 'year' => 2015, 'status' => 'hist', 'category' => 'web'],
            ['number' => 8259, 'title' => 'The JavaScript Object Notation (JSON) Data Interchange Format', 'year' => 2017, 'status' => 'std', 'category' => 'web'],
            ['number' => 9110, 'title' => 'HTTP Semantics',                                 'year' => 2022, 'status' => 'std',  'category' => 'web'],
            ['number' => 9112, 'title' => 'HTTP/1.1',                                       'year' => 2022, 'status' => 'std',  'category' => 'web'],
            ['number' => 9113, 'title' => 'HTTP/2',                                         'year' => 2022, 'status' => 'ps',   'category' => 'web'],
            ['number' => 9114, 'title' => 'HTTP/3',                                         'year' => 2022, 'status' => 'ps',   'category' => 'web'],

            // ── Security ──────────────────────────────────────────────────────
            ['number' => 4251, 'title' => 'The Secure Shell (SSH) Protocol Architecture',   'year' => 2006, 'status' => 'ps',   'category' => 'security'],
            ['number' => 4253, 'title' => 'The SSH Transport Layer Protocol',               'year' => 2006, 'status' => 'ps',   'category' => 'security'],
            ['number' => 4301, 'title' => 'Security Architecture for IP (IPsec)',           'year' => 2005, 'status' => 'ps',   'category' => 'security'],
            ['number' => 4303, 'title' => 'IP Encapsulating Security Payload (ESP)',        'year' => 2005, 'status' => 'ps',   'category' => 'security'],
            ['number' => 5246, 'title' => 'The Transport Layer Security (TLS) 1.2 Protocol', 'year' => 2008, 'status' => 'hist', 'category' => 'security'],
            ['number' => 6749, 'title' => 'The OAuth 2.0 Authorization Framework',          'year' => 2012, 'status' => 'ps',   'category' => 'security'],
            ['number' => 7519, 'title' => 'JSON Web Token (JWT)',                           'year' => 2015, 'status' => 'ps',   'category' => 'security'],
            ['number' => 8446, 'title' => 'The Transport Layer Security (TLS) 1.3 Protocol', 'year' => 2018, 'status' => 'ps',  'category' => 'security'],
            ['number' => 4949, 'title' => 'Internet Security Glossary, Version 2',          'year' => 2007, 'status' => 'info', 'category' => 'security'],
            ['number' => 5280, 'title' => 'Internet X.509 Public Key Infrastructure (PKIX)', 'year' => 2008, 'status' => 'ps',  'category' => 'security'],

            // ── Management ────────────────────────────────────────────────────
            ['number' => 854,  'title' => 'Telnet Protocol Specification',                  'year' => 1983, 'status' => 'std',  'category' => 'management'],
            ['number' => 959,  'title' => 'File Transfer Protocol (FTP)',                   'year' => 1985, 'status' => 'std',  'category' => 'management'],
            ['number' => 2131, 'title' => 'Dynamic Host Configuration Protocol (DHCP)',     'year' => 1997, 'status' => 'std',  'category' => 'management'],
            ['number' => 3315, 'title' => 'DHCPv6 (obsoleted by 8415)',                    'year' => 2003, 'status' => 'hist', 'category' => 'management'],
            ['number' => 8415, 'title' => 'Dynamic Host Configuration Protocol for IPv6 (DHCPv6)', 'year' => 2018, 'status' => 'ps', 'category' => 'management'],
            ['number' => 5905, 'title' => 'Network Time Protocol Version 4 (NTPv4)',        'year' => 2010, 'status' => 'ps',   'category' => 'management'],
            ['number' => 1157, 'title' => 'SNMP Version 1',                                 'year' => 1990, 'status' => 'hist', 'category' => 'management'],
            ['number' => 3411, 'title' => 'SNMPv3 Architecture',                            'year' => 2002, 'status' => 'std',  'category' => 'management'],
            ['number' => 862,  'title' => 'Echo Protocol',                                  'year' => 1983, 'status' => 'std',  'category' => 'management'],
            ['number' => 2544, 'title' => 'Benchmarking Methodology for Network Interconnect Devices', 'year' => 1999, 'status' => 'info', 'category' => 'management'],
            ['number' => 7042, 'title' => 'IANA Considerations for Ethernet',               'year' => 2013, 'status' => 'bcp',  'category' => 'management'],

            // ── References ────────────────────────────────────────────────────
            ['number' => 2119, 'title' => 'Key words for use in RFCs to Indicate Requirement Levels (MUST, SHOULD…)', 'year' => 1997, 'status' => 'bcp', 'category' => 'reference'],
            ['number' => 8174, 'title' => 'Ambiguity of Uppercase vs Lowercase in RFC 2119 Key Words', 'year' => 2017, 'status' => 'bcp', 'category' => 'reference'],
            ['number' => 5226, 'title' => 'Guidelines for Writing an IANA Considerations Section in RFCs', 'year' => 2008, 'status' => 'hist', 'category' => 'reference'],
            ['number' => 8126, 'title' => 'Guidelines for Writing an IANA Considerations Section in RFCs (revised)', 'year' => 2017, 'status' => 'bcp', 'category' => 'reference'],
        ];
    }

    public static function categories(): array
    {
        return ['networking', 'routing', 'dns', 'email', 'web', 'security', 'management', 'reference'];
    }

    public static function statuses(): array
    {
        return ['std', 'ps', 'bcp', 'info', 'hist'];
    }
}
