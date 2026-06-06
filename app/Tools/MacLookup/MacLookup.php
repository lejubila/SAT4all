<?php

namespace App\Tools\MacLookup;

class MacLookup
{
    // OUI → vendor fallback (used when DB is empty or unavailable).
    // Keys: 6 uppercase hex chars, no separators.
    private static array $oui = [
        // ── Cisco ──────────────────────────────────────────────────────────
        '00000C' => 'Cisco Systems', '000142' => 'Cisco Systems', '000143' => 'Cisco Systems',
        '000196' => 'Cisco Systems', '000217' => 'Cisco Systems', '00036B' => 'Cisco Systems',
        '0003E3' => 'Cisco Systems', '000427' => 'Cisco Systems', '0004C0' => 'Cisco Systems',
        '0004DD' => 'Cisco Systems', '0005DC' => 'Cisco Systems', '000628' => 'Cisco Systems',
        '00070D' => 'Cisco Systems', '000750' => 'Cisco Systems', '0007EB' => 'Cisco Systems',
        '00082F' => 'Cisco Systems', '00087C' => 'Cisco Systems', '0008E2' => 'Cisco Systems',
        '000911' => 'Cisco Systems', '000943' => 'Cisco Systems', '00097B' => 'Cisco Systems',
        '0009B7' => 'Cisco Systems', '0009E8' => 'Cisco Systems', '000A41' => 'Cisco Systems',
        '000A8A' => 'Cisco Systems', '000AB7' => 'Cisco Systems', '000AF3' => 'Cisco Systems',
        '000B45' => 'Cisco Systems', '000B46' => 'Cisco Systems', '000BFC' => 'Cisco Systems',
        '000CCE' => 'Cisco Systems', '000CF1' => 'Cisco Systems', '000D29' => 'Cisco Systems',
        '000DBD' => 'Cisco Systems', '000E08' => 'Cisco Systems', '000E38' => 'Cisco Systems',
        '000E84' => 'Cisco Systems', '000ED7' => 'Cisco Systems', '000F23' => 'Cisco Systems',
        '000F34' => 'Cisco Systems', '000F8F' => 'Cisco Systems', '000F90' => 'Cisco Systems',
        '001001' => 'Cisco Systems', '001121' => 'Cisco Systems', '00112B' => 'Cisco Systems',
        '001185' => 'Cisco Systems', '0011BB' => 'Cisco Systems', '001201' => 'Cisco Systems',
        '001310' => 'Cisco Systems', '001435' => 'Cisco Systems', '001601' => 'Cisco Systems',
        '001706' => 'Cisco Systems', '001A2F' => 'Cisco Systems', '001A6C' => 'Cisco Systems',
        '001AA2' => 'Cisco Systems', '001B0D' => 'Cisco Systems', '001B54' => 'Cisco Systems',
        '001CEF' => 'Cisco Systems', '001D70' => 'Cisco Systems', '001DE5' => 'Cisco Systems',
        '001EBD' => 'Cisco Systems', '001EE5' => 'Cisco Systems', '001F9E' => 'Cisco Systems',
        '002155' => 'Cisco Systems', '0021D8' => 'Cisco Systems', '002189' => 'Cisco Systems',
        '0022BD' => 'Cisco Systems', '002290' => 'Cisco Systems', '0022D4' => 'Cisco Systems',
        '002391' => 'Cisco Systems', '0023AB' => 'Cisco Systems', '0023EA' => 'Cisco Systems',
        '0024C4' => 'Cisco Systems', '002490' => 'Cisco Systems', '0025B4' => 'Cisco Systems',
        '002585' => 'Cisco Systems', '002645' => 'Cisco Systems', '002697' => 'Cisco Systems',
        '0026CB' => 'Cisco Systems', '002702' => 'Cisco Systems', '00274C' => 'Cisco Systems',
        '002790' => 'Cisco Systems', '0028F0' => 'Cisco Systems', '002A10' => 'Cisco Systems',
        '002C31' => 'Cisco Systems', '003A7D' => 'Cisco Systems', '004A77' => 'Cisco Systems',
        '005006' => 'Cisco Systems', '00508A' => 'Cisco Systems', '1CC1DE' => 'Cisco Systems',
        '2C3124' => 'Cisco Systems', '3C5EC3' => 'Cisco Systems', '4403A7' => 'Cisco Systems',
        '50614F' => 'Cisco Systems', '54781A' => 'Cisco Systems', '5C5015' => 'Cisco Systems',
        '6CB217' => 'Cisco Systems', '6CDDBC' => 'Cisco Systems', '74A0BB' => 'Cisco Systems',
        '78DA6E' => 'Cisco Systems', '8478AC' => 'Cisco Systems', '885A92' => 'Cisco Systems',
        '9CBDE7' => 'Cisco Meraki',  'B4E9B0' => 'Cisco Systems', 'C4B2C5' => 'Cisco Systems',
        'D072DC' => 'Cisco Systems', 'D07E28' => 'Cisco Systems', 'DC7B94' => 'Cisco Systems',
        'E4C7F3' => 'Cisco Systems', 'EC3010' => 'Cisco Systems', 'F04DA2' => 'Cisco Systems',
        'F4CFE2' => 'Cisco Systems', 'FCE33C' => 'Cisco Systems', '7CF7F9' => 'Cisco Meraki',
        '0C8DDB' => 'Cisco Meraki',  'AC1766' => 'Cisco Meraki',

        // ── Juniper Networks ──────────────────────────────────────────────
        '000517' => 'Juniper Networks', '0010DB' => 'Juniper Networks', '001966' => 'Juniper Networks',
        '2C6BF5' => 'Juniper Networks', '3C8826' => 'Juniper Networks', '40B4F0' => 'Juniper Networks',
        '508789' => 'Juniper Networks', '5C5EAB' => 'Juniper Networks', '6C9C5E' => 'Juniper Networks',
        '844820' => 'Juniper Networks', '9869D9' => 'Juniper Networks', 'A8D099' => 'Juniper Networks',
        'B4750E' => 'Juniper Networks', 'C0B6F9' => 'Juniper Networks', 'F0A24E' => 'Juniper Networks',

        // ── HP / HPE / Aruba ──────────────────────────────────────────────
        '001321' => 'HP Inc.',        '001560' => 'HP Inc.',        '0017A4' => 'HP Inc.',
        '001A4B' => 'HP Inc.',        '001B78' => 'HP Inc.',        '001CC4' => 'HP Inc.',
        '001E0B' => 'HP Inc.',        '002170' => 'HP Inc.',        '0022F3' => 'HP Inc.',
        '0023EA' => 'HP Inc.',        '002569' => 'HP Inc.',        '0026F2' => 'HP Inc.',
        '001083' => 'Hewlett Packard', '0060B0' => 'Hewlett Packard', '080009' => 'Hewlett Packard',
        '3440B5' => 'Hewlett Packard Enterprise', '3C4A92' => 'Hewlett Packard Enterprise',
        '9CB654' => 'Hewlett Packard Enterprise', 'A0B3CC' => 'Hewlett Packard Enterprise',
        'B499BA' => 'Hewlett Packard Enterprise', 'C4346B' => 'Hewlett Packard Enterprise',
        'D00EA4' => 'Aruba Networks',  '00247D' => 'Aruba Networks',  '201702' => 'Aruba Networks',
        '24DE C6' => 'Aruba Networks', '6C:F3:7F' => 'Aruba Networks', '84D47E' => 'Aruba Networks',
        '94B40A' => 'Aruba Networks',  'AC:A3:1E' => 'Aruba Networks', 'D8C7C8' => 'Aruba Networks',
        'F07959' => 'Aruba Networks',

        // ── Dell ──────────────────────────────────────────────────────────
        '001372' => 'Dell Inc.',       '0014BB' => 'Dell Inc.',       '001422' => 'Dell Inc.',
        '001517' => 'Dell Inc.',       '00188B' => 'Dell Inc.',       '001A7A' => 'Dell Inc.',
        '001B21' => 'Dell Inc.',       '001C23' => 'Dell Inc.',       '001D09' => 'Dell Inc.',
        '001E4F' => 'Dell Inc.',       '002129' => 'Dell Inc.',       '002219' => 'Dell Inc.',
        '002422' => 'Dell Inc.',       '00256F' => 'Dell Inc.',       '0026B9' => 'Dell Inc.',
        '002764' => 'Dell Inc.',       '18A99B' => 'Dell Inc.',       '1C40AF' => 'Dell Inc.',
        '205584' => 'Dell Inc.',       '24B6FD' => 'Dell Inc.',       '2CD05A' => 'Dell Inc.',
        '3417EB' => 'Dell Inc.',       '3440B5' => 'Dell Inc.',       '442A60' => 'Dell Inc.',
        '485B39' => 'Dell Inc.',       '4C8027' => 'Dell Inc.',       '5CE0C5' => 'Dell Inc.',
        '6092BF' => 'Dell Inc.',       '74867A' => 'Dell Inc.',       '788CB5' => 'Dell Inc.',
        '84D4BE' => 'Dell Inc.',       '8863DF' => 'Dell Inc.',       '98901F' => 'Dell Inc.',
        'A41F72' => 'Dell Inc.',       'B083FE' => 'Dell Inc.',       'B4967D' => 'Dell Inc.',
        'B8AC6F' => 'Dell Inc.',       'C8D9D2' => 'Dell Inc.',       'D4AE52' => 'Dell Inc.',
        'E091F5' => 'Dell Inc.',       'F04DA2' => 'Dell Inc.',       'F44A7F' => 'Dell Inc.',
        'F8B156' => 'Dell Inc.',

        // ── Apple ─────────────────────────────────────────────────────────
        '000A27' => 'Apple Inc.',      '000A95' => 'Apple Inc.',      '000D93' => 'Apple Inc.',
        '0011DD' => 'Apple Inc.',      '00142F' => 'Apple Inc.',      '001451' => 'Apple Inc.',
        '001B63' => 'Apple Inc.',      '001CB3' => 'Apple Inc.',      '001D4F' => 'Apple Inc.',
        '001E52' => 'Apple Inc.',      '001EC2' => 'Apple Inc.',      '001F5B' => 'Apple Inc.',
        '001FF3' => 'Apple Inc.',      '0021E9' => 'Apple Inc.',      '002241' => 'Apple Inc.',
        '002312' => 'Apple Inc.',      '0023DF' => 'Apple Inc.',      '002436' => 'Apple Inc.',
        '002500' => 'Apple Inc.',      '0025BC' => 'Apple Inc.',      '0026B0' => 'Apple Inc.',
        '0026BB' => 'Apple Inc.',      '003065' => 'Apple Inc.',      '0050E4' => 'Apple Inc.',
        '089E01' => 'Apple Inc.',      '0C1539' => 'Apple Inc.',      '0C3E9F' => 'Apple Inc.',
        '0C74C2' => 'Apple Inc.',      '0CD746' => 'Apple Inc.',      '100000' => 'Apple Inc.',
        '1499E2' => 'Apple Inc.',      '189EFC' => 'Apple Inc.',      '1C1AC0' => 'Apple Inc.',
        '1C36BB' => 'Apple Inc.',      '1C5CF2' => 'Apple Inc.',      '1C9E46' => 'Apple Inc.',
        '200768' => 'Apple Inc.',      '204E7F' => 'Apple Inc.',      '208090' => 'Apple Inc.',
        '247FB2' => 'Apple Inc.',      '28A02B' => 'Apple Inc.',      '28CFDA' => 'Apple Inc.',
        '28E14C' => 'Apple Inc.',      '2C1F23' => 'Apple Inc.',      '2CF0EE' => 'Apple Inc.',
        '344DF7' => 'Apple Inc.',      '380F4A' => 'Apple Inc.',      '38B54D' => 'Apple Inc.',
        '38C986' => 'Apple Inc.',      '3C07C5' => 'Apple Inc.',      '3C15C2' => 'Apple Inc.',
        '3CEF8C' => 'Apple Inc.',      '40A6D9' => 'Apple Inc.',      '40D32D' => 'Apple Inc.',
        '44D884' => 'Apple Inc.',      '44FB42' => 'Apple Inc.',      '4860BC' => 'Apple Inc.',
        '48A195' => 'Apple Inc.',      '4C57CA' => 'Apple Inc.',      '4C74BF' => 'Apple Inc.',
        '5404A6' => 'Apple Inc.',      '544E90' => 'Apple Inc.',      '54AE27' => 'Apple Inc.',
        '58404E' => 'Apple Inc.',      '5855CA' => 'Apple Inc.',      '5C8D4E' => 'Apple Inc.',
        '5CF938' => 'Apple Inc.',      '60334B' => 'Apple Inc.',      '6033BB' => 'Apple Inc.',
        '60FB42' => 'Apple Inc.',      '6C3E6D' => 'Apple Inc.',      '6C4008' => 'Apple Inc.',
        '6C709F' => 'Apple Inc.',      '6C96CF' => 'Apple Inc.',      '70480F' => 'Apple Inc.',
        '704D7B' => 'Apple Inc.',      '70CD60' => 'Apple Inc.',      '70DEE2' => 'Apple Inc.',
        '7440BB' => 'Apple Inc.',      '74E1B6' => 'Apple Inc.',      '780CE7' => 'Apple Inc.',
        '78CA39' => 'Apple Inc.',      '7C11BE' => 'Apple Inc.',      '7C5049' => 'Apple Inc.',
        '7C6D62' => 'Apple Inc.',      '7CF05F' => 'Apple Inc.',      '8014A8' => 'Apple Inc.',
        '843835' => 'Apple Inc.',      '88C663' => 'Apple Inc.',      '88E87F' => 'Apple Inc.',
        '8C2937' => 'Apple Inc.',      '8C7B9D' => 'Apple Inc.',      '8C8590' => 'Apple Inc.',
        '90272D' => 'Apple Inc.',      '906C3B' => 'Apple Inc.',      '9027E4' => 'Apple Inc.',
        '98010A' => 'Apple Inc.',      '9801A7' => 'Apple Inc.',      '9C84BF' => 'Apple Inc.',
        'A45E60' => 'Apple Inc.',      'A4B197' => 'Apple Inc.',      'A4C361' => 'Apple Inc.',
        'A4D18C' => 'Apple Inc.',      'A81B5A' => 'Apple Inc.',      'A8667F' => 'Apple Inc.',
        'A886DD' => 'Apple Inc.',      'A88742' => 'Apple Inc.',      'ACBC32' => 'Apple Inc.',
        'ACE433' => 'Apple Inc.',      'B065BD' => 'Apple Inc.',      'B09122' => 'Apple Inc.',
        'B4F0AB' => 'Apple Inc.',      'B8782E' => 'Apple Inc.',      'B88D12' => 'Apple Inc.',
        'BC3BAF' => 'Apple Inc.',      'BC4CC4' => 'Apple Inc.',      'BC52B7' => 'Apple Inc.',
        'BC9FEF' => 'Apple Inc.',      'C06338' => 'Apple Inc.',      'C42C03' => 'Apple Inc.',
        'C82A14' => 'Apple Inc.',      'C8E18B' => 'Apple Inc.',      'CC08E0' => 'Apple Inc.',
        'CC44E9' => 'Apple Inc.',      'CCD5A4' => 'Apple Inc.',      'D0254B' => 'Apple Inc.',
        'D023DB' => 'Apple Inc.',      'D09317' => 'Apple Inc.',      'D4619D' => 'Apple Inc.',
        'D4F46F' => 'Apple Inc.',      'D8004D' => 'Apple Inc.',      'D8BB2C' => 'Apple Inc.',
        'DC2B61' => 'Apple Inc.',      'DC9B9C' => 'Apple Inc.',      'E0B9BA' => 'Apple Inc.',
        'E0C767' => 'Apple Inc.',      'E42B34' => 'Apple Inc.',      'E4C63D' => 'Apple Inc.',
        'E4CE8F' => 'Apple Inc.',      'E89E0C' => 'Apple Inc.',      'EC3586' => 'Apple Inc.',
        'F02475' => 'Apple Inc.',      'F0B479' => 'Apple Inc.',      'F0CBA1' => 'Apple Inc.',
        'F0D1A9' => 'Apple Inc.',      'F40B93' => 'Apple Inc.',      'F452EB' => 'Apple Inc.',
        'F81EDF' => 'Apple Inc.',      'F8953C' => 'Apple Inc.',      'FCFC48' => 'Apple Inc.',

        // ── Samsung ───────────────────────────────────────────────────────
        '001247' => 'Samsung Electronics', '001599' => 'Samsung Electronics',
        '0016DB' => 'Samsung Electronics', '001799' => 'Samsung Electronics',
        '0021D1' => 'Samsung Electronics', '002339' => 'Samsung Electronics',
        '0025AB' => 'Samsung Electronics', '002566' => 'Samsung Electronics',
        '10D542' => 'Samsung Electronics', '1C3ADE' => 'Samsung Electronics',
        '2477B8' => 'Samsung Electronics', '2C0E3D' => 'Samsung Electronics',
        '380A94' => 'Samsung Electronics', '3C6200' => 'Samsung Electronics',
        '445566' => 'Samsung Electronics', '4CACF1' => 'Samsung Electronics',
        '5051DA' => 'Samsung Electronics', '5C0A5B' => 'Samsung Electronics',
        '6C2F2C' => 'Samsung Electronics', '78BDBC' => 'Samsung Electronics',
        '84119E' => 'Samsung Electronics', '8CB8BD' => 'Samsung Electronics',
        '90F1AA' => 'Samsung Electronics', '9858BB' => 'Samsung Electronics',
        'A0821F' => 'Samsung Electronics', 'B0726F' => 'Samsung Electronics',
        'B4EF39' => 'Samsung Electronics', 'C4735A' => 'Samsung Electronics',
        'CC05FB' => 'Samsung Electronics', 'D0177E' => 'Samsung Electronics',
        'D016B4' => 'Samsung Electronics', 'F4428F' => 'Samsung Electronics',
        'FCDB96' => 'Samsung Electronics',

        // ── Huawei ────────────────────────────────────────────────────────
        '001882' => 'Huawei Technologies', '0019C6' => 'Huawei Technologies',
        '001A1E' => 'Huawei Technologies', '001E10' => 'Huawei Technologies',
        '002568' => 'Huawei Technologies', '0025D3' => 'Huawei Technologies',
        '0026C6' => 'Huawei Technologies', '002706' => 'Huawei Technologies',
        '04BD70' => 'Huawei Technologies', '0CD668' => 'Huawei Technologies',
        '18C58A' => 'Huawei Technologies', '1CB15D' => 'Huawei Technologies',
        '202BC1' => 'Huawei Technologies', '2848FA' => 'Huawei Technologies',
        '28ED84' => 'Huawei Technologies', '2CAB00' => 'Huawei Technologies',
        '30D17E' => 'Huawei Technologies', '34B354' => 'Huawei Technologies',
        '381022' => 'Huawei Technologies', '40CB40' => 'Huawei Technologies',
        '44C346' => 'Huawei Technologies', '48B02D' => 'Huawei Technologies',
        '4CE17B' => 'Huawei Technologies', '54514E' => 'Huawei Technologies',
        '5CB900' => 'Huawei Technologies', '600112' => 'Huawei Technologies',
        '68A0F6' => 'Huawei Technologies', '70726F' => 'Huawei Technologies',
        '70A8E3' => 'Huawei Technologies', '78166B' => 'Huawei Technologies',
        '7C1CF1' => 'Huawei Technologies', '844DBF' => 'Huawei Technologies',
        '8851FB' => 'Huawei Technologies', '8C0D76' => 'Huawei Technologies',
        '8CC8CD' => 'Huawei Technologies', '90672C' => 'Huawei Technologies',
        '9497FA' => 'Huawei Technologies', '9C7DA3' => 'Huawei Technologies',
        'A001E8' => 'Huawei Technologies', 'AC4E91' => 'Huawei Technologies',
        'AC61EA' => 'Huawei Technologies', 'B472C9' => 'Huawei Technologies',
        'B4C7AA' => 'Huawei Technologies', 'BCADE4' => 'Huawei Technologies',
        'C4072F' => 'Huawei Technologies', 'C80CC8' => 'Huawei Technologies',
        'CC96A0' => 'Huawei Technologies', 'D07AB5' => 'Huawei Technologies',
        'D4614F' => 'Huawei Technologies', 'D46AA3' => 'Huawei Technologies',
        'E0247F' => 'Huawei Technologies', 'E4A8B6' => 'Huawei Technologies',
        'E8B4C8' => 'Huawei Technologies', 'ECC26C' => 'Huawei Technologies',
        'F0489F' => 'Huawei Technologies', 'F49F54' => 'Huawei Technologies',
        'F82354' => 'Huawei Technologies', 'FC4A45' => 'Huawei Technologies',

        // ── Ubiquiti Networks ─────────────────────────────────────────────
        '002722' => 'Ubiquiti Networks', '04180F' => 'Ubiquiti Networks',
        '0418D6' => 'Ubiquiti Networks', '0E0000' => 'Ubiquiti Networks',
        '242C71' => 'Ubiquiti Networks', '44D9E7' => 'Ubiquiti Networks',
        '60224B' => 'Ubiquiti Networks', '687279' => 'Ubiquiti Networks',
        '74ACB9' => 'Ubiquiti Networks', '788A20' => 'Ubiquiti Networks',
        '80A259' => 'Ubiquiti Networks', '9C05D6' => 'Ubiquiti Networks',
        'B4FBE4' => 'Ubiquiti Networks', 'DC9FDB' => 'Ubiquiti Networks',
        'E063DA' => 'Ubiquiti Networks', 'F09FC2' => 'Ubiquiti Networks',
        'FC:EC:DA' => 'Ubiquiti Networks', 'FCED14' => 'Ubiquiti Networks',

        // ── MikroTik ──────────────────────────────────────────────────────
        '000C42' => 'MikroTik',        '18FD74' => 'MikroTik',
        '2CC8F1' => 'MikroTik',        '48A98A' => 'MikroTik',
        '4C5E0C' => 'MikroTik',        '64D154' => 'MikroTik',
        '6C3B6B' => 'MikroTik',        '74:4D:28' => 'MikroTik',
        'B8693B' => 'MikroTik',        'C4AD34' => 'MikroTik',
        'CC2DE0' => 'MikroTik',        'D4CA6D' => 'MikroTik',
        'DC2C6E' => 'MikroTik',        'E4:8D:8C' => 'MikroTik',

        // ── TP-Link ───────────────────────────────────────────────────────
        '000AEB' => 'TP-Link Technologies', '001CF0' => 'TP-Link Technologies',
        '0024E2' => 'TP-Link Technologies', '003AF5' => 'TP-Link Technologies',
        '14CC20' => 'TP-Link Technologies', '18D61F' => 'TP-Link Technologies',
        '1C61B4' => 'TP-Link Technologies', '207002' => 'TP-Link Technologies',
        '244CE3' => 'TP-Link Technologies', '2C27D7' => 'TP-Link Technologies',
        '305A3A' => 'TP-Link Technologies', '3490EA' => 'TP-Link Technologies',
        '3C371A' => 'TP-Link Technologies', '40ED00' => 'TP-Link Technologies',
        '50C7BF' => 'TP-Link Technologies', '54C80F' => 'TP-Link Technologies',
        '5C628B' => 'TP-Link Technologies', '60E327' => 'TP-Link Technologies',
        '6466B3' => 'TP-Link Technologies', '64A3CB' => 'TP-Link Technologies',
        '6C5095' => 'TP-Link Technologies', '74DA38' => 'TP-Link Technologies',
        '789682' => 'TP-Link Technologies', '7C8BCA' => 'TP-Link Technologies',
        '80351C' => 'TP-Link Technologies', '8C21DA' => 'TP-Link Technologies',
        '90F652' => 'TP-Link Technologies', '944445' => 'TP-Link Technologies',
        'A0F3C1' => 'TP-Link Technologies', 'B0487A' => 'TP-Link Technologies',
        'B04FAB' => 'TP-Link Technologies', 'B0BE76' => 'TP-Link Technologies',
        'BC4601' => 'TP-Link Technologies', 'C00A95' => 'TP-Link Technologies',
        'C44D57' => 'TP-Link Technologies', 'C46E1F' => 'TP-Link Technologies',
        'D46E5C' => 'TP-Link Technologies', 'D84F57' => 'TP-Link Technologies',
        'EC0898' => 'TP-Link Technologies', 'ECFA1A' => 'TP-Link Technologies',
        'F04E3B' => 'TP-Link Technologies', 'F81A67' => 'TP-Link Technologies',
        'FC7516' => 'TP-Link Technologies',

        // ── Netgear ───────────────────────────────────────────────────────
        '001B2F' => 'Netgear',         '001E2A' => 'Netgear',         '001F33' => 'Netgear',
        '002064' => 'Netgear',         '00223F' => 'Netgear',         '002496' => 'Netgear',
        '00265A' => 'Netgear',         '044E06' => 'Netgear',         '08BD43' => 'Netgear',
        '0CB6D2' => 'Netgear',         '10941E' => 'Netgear',         '20E52A' => 'Netgear',
        '28C68E' => 'Netgear',         '2CB05D' => 'Netgear',         '40167E' => 'Netgear',
        '44946C' => 'Netgear',         '4C60DE' => 'Netgear',         '5C4CA9' => 'Netgear',
        '6027AA' => 'Netgear',         '6CB0CE' => 'Netgear',         '78D29B' => 'Netgear',
        '9C3DCF' => 'Netgear',         'A00460' => 'Netgear',         'A040A0' => 'Netgear',
        'A42354' => 'Netgear',         'B03986' => 'Netgear',         'C0FF28' => 'Netgear',
        'C4044A' => 'Netgear',         'E0469A' => 'Netgear',         'E09796' => 'Netgear',

        // ── ASUS ──────────────────────────────────────────────────────────
        '001A92' => 'ASUSTek Computer', '002227' => 'ASUSTek Computer', '0023A2' => 'ASUSTek Computer',
        '002401' => 'ASUSTek Computer', '107B44' => 'ASUSTek Computer', '148F7E' => 'ASUSTek Computer',
        '1C872C' => 'ASUSTek Computer', '20CF30' => 'ASUSTek Computer', '2C56DC' => 'ASUSTek Computer',
        '30B49E' => 'ASUSTek Computer', '38D547' => 'ASUSTek Computer', '40167E' => 'ASUSTek Computer',
        '485798' => 'ASUSTek Computer', '4CEDFB' => 'ASUSTek Computer', '50465D' => 'ASUSTek Computer',
        '5404A6' => 'ASUSTek Computer', '60A44C' => 'ASUSTek Computer', '6045CB' => 'ASUSTek Computer',
        '707285' => 'ASUSTek Computer', '74D435' => 'ASUSTek Computer', '78241A' => 'ASUSTek Computer',
        '88D7F6' => 'ASUSTek Computer', '90E6BA' => 'ASUSTek Computer', 'AC220B' => 'ASUSTek Computer',
        'AC9E17' => 'ASUSTek Computer', 'B06EBF' => 'ASUSTek Computer', 'BC9746' => 'ASUSTek Computer',
        'C86000' => 'ASUSTek Computer', 'D850E6' => 'ASUSTek Computer', 'E03F49' => 'ASUSTek Computer',
        'F07959' => 'ASUSTek Computer', 'F8F6EB' => 'ASUSTek Computer',

        // ── D-Link ────────────────────────────────────────────────────────
        '001195' => 'D-Link Corporation', '001346' => 'D-Link Corporation',
        '0015E9' => 'D-Link Corporation', '001CF0' => 'D-Link Corporation',
        '0021E7' => 'D-Link Corporation', '002401' => 'D-Link Corporation',
        '00265A' => 'D-Link Corporation', '1062EB' => 'D-Link Corporation',
        '14D64D' => 'D-Link Corporation', '1C5F2B' => 'D-Link Corporation',
        '28107B' => 'D-Link Corporation', '5CD998' => 'D-Link Corporation',
        '617B30' => 'D-Link Corporation', '749142' => 'D-Link Corporation',
        '84C9B2' => 'D-Link Corporation', 'A0AB1B' => 'D-Link Corporation',
        'B8A386' => 'D-Link Corporation', 'C0A0BB' => 'D-Link Corporation',

        // ── Fortinet ──────────────────────────────────────────────────────
        '000866' => 'Fortinet',        '001831' => 'Fortinet',
        '09C2CB' => 'Fortinet',        '1809C4' => 'Fortinet',
        '2C9148' => 'Fortinet',        '3005AF' => 'Fortinet',
        '544F4C' => 'Fortinet',        '5C8D4E' => 'Fortinet',
        '70680B' => 'Fortinet',        '8C7CFF' => 'Fortinet',
        '90F652' => 'Fortinet',        'A076EE' => 'Fortinet',
        'E8BEAD' => 'Fortinet',

        // ── Palo Alto Networks ────────────────────────────────────────────
        '003085' => 'Palo Alto Networks', '5C:50:15' => 'Palo Alto Networks',
        '689C45' => 'Palo Alto Networks',

        // ── VMware ────────────────────────────────────────────────────────
        '000569' => 'VMware Inc.',     '000C29' => 'VMware Inc.',
        '001C14' => 'VMware Inc.',     '005056' => 'VMware Inc.',

        // ── VirtualBox / Oracle ───────────────────────────────────────────
        '080027' => 'Oracle VirtualBox',

        // ── Microsoft (Hyper-V) ───────────────────────────────────────────
        '000D3A' => 'Microsoft Hyper-V', '001DD8' => 'Microsoft Corporation',
        '002248' => 'Microsoft Corporation', '0050F2' => 'Microsoft Corporation',
        '2855FF' => 'Microsoft Corporation', '3845E8' => 'Microsoft Corporation',
        '485073' => 'Microsoft Corporation', '60451D' => 'Microsoft Corporation',
        '7018F0' => 'Microsoft Corporation', '984F64' => 'Microsoft Corporation',
        'A85E45' => 'Microsoft Corporation', 'BC8385' => 'Microsoft Corporation',
        'C8AF21' => 'Microsoft Corporation', 'CC1568' => 'Microsoft Corporation',
        'DC1BAAD' => 'Microsoft Corporation',

        // ── QEMU / KVM ────────────────────────────────────────────────────
        '525400' => 'QEMU/KVM Virtual Machine',

        // ── Intel ─────────────────────────────────────────────────────────
        '000732' => 'Intel Corporate',   '000E0C' => 'Intel Corporate',
        '0012F0' => 'Intel Corporate',   '001302' => 'Intel Corporate',
        '001320' => 'Intel Corporate',   '001517' => 'Intel Corporate',
        '0016EA' => 'Intel Corporate',   '0016EB' => 'Intel Corporate',
        '00176F' => 'Intel Corporate',   '001900' => 'Intel Corporate',
        '001BB9' => 'Intel Corporate',   '001D7D' => 'Intel Corporate',
        '001EE6' => 'Intel Corporate',   '0021CC' => 'Intel Corporate',
        '002241' => 'Intel Corporate',   '00224D' => 'Intel Corporate',
        '002567' => 'Intel Corporate',   '0026C7' => 'Intel Corporate',
        '183452' => 'Intel Corporate',   '1CB3CB' => 'Intel Corporate',
        '207CF4' => 'Intel Corporate',   '242AE1' => 'Intel Corporate',
        '2C76DE' => 'Intel Corporate',   '344CCA' => 'Intel Corporate',
        '3C970E' => 'Intel Corporate',   '40E2C8' => 'Intel Corporate',
        '4C7974' => 'Intel Corporate',   '50765E' => 'Intel Corporate',
        '547FEE' => 'Intel Corporate',   '58940C' => 'Intel Corporate',
        '5C514F' => 'Intel Corporate',   '60674A' => 'Intel Corporate',
        '68D3DC' => 'Intel Corporate',   '74E549' => 'Intel Corporate',
        '78920E' => 'Intel Corporate',   '7CCA36' => 'Intel Corporate',
        '80861B' => 'Intel Corporate',   '804646' => 'Intel Corporate',
        '8478AC' => 'Intel Corporate',   '88532E' => 'Intel Corporate',
        '8C7042' => 'Intel Corporate',   '94659C' => 'Intel Corporate',
        '9C2A70' => 'Intel Corporate',   'A4C494' => 'Intel Corporate',
        'AC7BA1' => 'Intel Corporate',   'B07786' => 'Intel Corporate',
        'B4B634' => 'Intel Corporate',   'C4D989' => 'Intel Corporate',
        'C83288' => 'Intel Corporate',   'CC3D82' => 'Intel Corporate',
        'D40085' => 'Intel Corporate',   'D4619D' => 'Intel Corporate',
        'D8FC93' => 'Intel Corporate',   'E0D55E' => 'Intel Corporate',
        'E47F97' => 'Intel Corporate',   'E4F8EF' => 'Intel Corporate',
        'F40E11' => 'Intel Corporate',   'F4960D' => 'Intel Corporate',
        'FC1591' => 'Intel Corporate',

        // ── Broadcom ──────────────────────────────────────────────────────
        '000AF7' => 'Broadcom',        '001018' => 'Broadcom',
        '00108B' => 'Broadcom',        '207985' => 'Broadcom',
        '28EF01' => 'Broadcom',        '44D3CA' => 'Broadcom',
        '74E1B6' => 'Broadcom',        '8893EB' => 'Broadcom',

        // ── Realtek ───────────────────────────────────────────────────────
        '001631' => 'Realtek Semiconductor', '008C88' => 'Realtek Semiconductor',
        '041E64' => 'Realtek Semiconductor', '102C6B' => 'Realtek Semiconductor',
        '1C3949' => 'Realtek Semiconductor', '2C4D54' => 'Realtek Semiconductor',
        '30B4B8' => 'Realtek Semiconductor', '38CAD8' => 'Realtek Semiconductor',
        '3C1E04' => 'Realtek Semiconductor', '5029CB' => 'Realtek Semiconductor',
        '58FB84' => 'Realtek Semiconductor', '5C3A45' => 'Realtek Semiconductor',
        '6045CB' => 'Realtek Semiconductor', '7C76FB' => 'Realtek Semiconductor',
        '807B85' => 'Realtek Semiconductor', 'A0F3C1' => 'Realtek Semiconductor',
        'B467F1' => 'Realtek Semiconductor', 'D8F8EE' => 'Realtek Semiconductor',
        'E01FEB' => 'Realtek Semiconductor',

        // ── Raspberry Pi Foundation ───────────────────────────────────────
        'B827EB' => 'Raspberry Pi Foundation',
        'DCA632' => 'Raspberry Pi Foundation',
        'E45F01' => 'Raspberry Pi Foundation',

        // ── Amazon ────────────────────────────────────────────────────────
        '0015B3' => 'Amazon Technologies', '002D59' => 'Amazon Technologies',
        '0C4DE9' => 'Amazon Technologies', '184F32' => 'Amazon Technologies',
        '28EF01' => 'Amazon Technologies', '34D270' => 'Amazon Technologies',
        '40B4CD' => 'Amazon Technologies', '44650D' => 'Amazon Technologies',
        '50F5DA' => 'Amazon Technologies', '680571' => 'Amazon Technologies',
        '6C5697' => 'Amazon Technologies', '74C246' => 'Amazon Technologies',
        '78E103' => 'Amazon Technologies', '843835' => 'Amazon Technologies',
        '9499E3' => 'Amazon Technologies', 'A002DC' => 'Amazon Technologies',
        'AC63BE' => 'Amazon Technologies', 'B406DC' => 'Amazon Technologies',
        'B47C9C' => 'Amazon Technologies', 'B8BEB6' => 'Amazon Technologies',
        'CC9917' => 'Amazon Technologies', 'F0F249' => 'Amazon Technologies',
        'F81ED0' => 'Amazon Technologies', 'FC6525' => 'Amazon Technologies',

        // ── Google ────────────────────────────────────────────────────────
        '001A11' => 'Google Inc.',     '3C5AB4' => 'Google Inc.',
        '54607E' => 'Google Inc.',     'A47733' => 'Google Inc.',
        'E4F0AB' => 'Google Inc.',     'F4F5E8' => 'Google Inc.',

        // ── IBM ───────────────────────────────────────────────────────────
        '000103' => 'IBM Corporation', '0004AC' => 'IBM Corporation',
        '000559' => 'IBM Corporation', '00065A' => 'IBM Corporation',
        '0009C9' => 'IBM Corporation', '000D60' => 'IBM Corporation',
        '001125' => 'IBM Corporation', '0019CF' => 'IBM Corporation',
        '001C27' => 'IBM Corporation', '00212A' => 'IBM Corporation',
        '0024D7' => 'IBM Corporation', '002598' => 'IBM Corporation',
        '00265A' => 'IBM Corporation', '3868DD' => 'IBM Corporation',
        '40F2E9' => 'IBM Corporation', '78A285' => 'IBM Corporation',
        '9860A0' => 'IBM Corporation', 'AC7A4D' => 'IBM Corporation',

        // ── Lenovo ────────────────────────────────────────────────────────
        '000016' => 'Lenovo',          '003048' => 'Lenovo',
        '04BFE3' => 'Lenovo',          '0C8DDB' => 'Lenovo',
        '1092F0' => 'Lenovo',          '100001' => 'Lenovo',
        '14853B' => 'Lenovo',          '1C6F65' => 'Lenovo',
        '2819DB' => 'Lenovo',          '28D244' => 'Lenovo',
        '34736E' => 'Lenovo',          '3CAD72' => 'Lenovo',
        '4050A0' => 'Lenovo',          '44391C' => 'Lenovo',
        '48F8B3' => 'Lenovo',          '5CF3FC' => 'Lenovo',
        '60D94E' => 'Lenovo',          '7CE9D3' => 'Lenovo',
        '88703C' => 'Lenovo',          '9009D0' => 'Lenovo',
        '9C539E' => 'Lenovo',          'A4C361' => 'Lenovo',
        'ACF7F3' => 'Lenovo',          'B84BBB' => 'Lenovo',
        'C8994B' => 'Lenovo',          'E4A471' => 'Lenovo',
        'F0DEF1' => 'Lenovo',          'F44E39' => 'Lenovo',
        'F8BC12' => 'Lenovo',

        // ── Supermicro ────────────────────────────────────────────────────
        '002590' => 'Super Micro Computer', '003048' => 'Super Micro Computer',
        '0025C7' => 'Super Micro Computer', '0030C8' => 'Super Micro Computer',
        '3CECEF' => 'Super Micro Computer', 'AC1F6B' => 'Super Micro Computer',

        // ── NetApp ────────────────────────────────────────────────────────
        '000F52' => 'NetApp',          '001635' => 'NetApp',
        '001A64' => 'NetApp',          '002170' => 'NetApp',
        '00A098' => 'NetApp',          '0060482' => 'NetApp',
        'D039EA' => 'NetApp',

        // ── Arista Networks ───────────────────────────────────────────────
        '001C73' => 'Arista Networks', '2899C7' => 'Arista Networks',
        '444C0C' => 'Arista Networks', 'FCBB8C' => 'Arista Networks',

        // ── F5 Networks ───────────────────────────────────────────────────
        '000A49' => 'F5 Networks',

        // ── Check Point ───────────────────────────────────────────────────
        '001C7F' => 'Check Point Software',

        // ── AVM (Fritz!Box) ───────────────────────────────────────────────
        '001C4A' => 'AVM GmbH',        '002418' => 'AVM GmbH',        '007A4C' => 'AVM GmbH',
        '2C9EFC' => 'AVM GmbH',        '30000A' => 'AVM GmbH',        '3C915C' => 'AVM GmbH',
        'A0244B' => 'AVM GmbH',        'E4D4E6' => 'AVM GmbH',        'F03453' => 'AVM GmbH',

        // ── Extreme Networks ──────────────────────────────────────────────
        '00E02B' => 'Extreme Networks', '0004FF' => 'Extreme Networks',
        '001A4F' => 'Extreme Networks', '00122F' => 'Extreme Networks',
        '001863' => 'Extreme Networks', '001CD1' => 'Extreme Networks',
        '1CA3B7' => 'Extreme Networks', '58400F' => 'Extreme Networks',

        // ── Brocade / Broadcom ────────────────────────────────────────────
        '000860' => 'Brocade Communications',  '00051E' => 'Brocade Communications',
        '001088' => 'Brocade Communications',  '001CB0' => 'Brocade Communications',
        '748E8B' => 'Brocade Communications',  'B0C540' => 'Brocade Communications',

        // ── Mellanox / NVIDIA ─────────────────────────────────────────────
        '000259' => 'Mellanox Technologies',   '0002C9' => 'Mellanox Technologies',
        'E41D2D' => 'Mellanox Technologies',   'E4D8B9' => 'Mellanox Technologies',
        'F45214' => 'Mellanox Technologies',

        // ── Xiaomi ────────────────────────────────────────────────────────
        '001795' => 'Xiaomi Communications',   '1C5F2B' => 'Xiaomi Communications',
        '28E31F' => 'Xiaomi Communications',   '34CE00' => 'Xiaomi Communications',
        '38A4ED' => 'Xiaomi Communications',   '642737' => 'Xiaomi Communications',
        '6C5AB0' => 'Xiaomi Communications',   '74510E' => 'Xiaomi Communications',
        '8C97EA' => 'Xiaomi Communications',   'AC2574' => 'Xiaomi Communications',
        'B0E235' => 'Xiaomi Communications',   'F4F5DB' => 'Xiaomi Communications',
        'FC64BA' => 'Xiaomi Communications',

        // ── Sony ──────────────────────────────────────────────────────────
        '002618' => 'Sony Corporation',  '001D0D' => 'Sony Corporation',
        '003C9D' => 'Sony Corporation',  '0082D4' => 'Sony Corporation',
        '1CC0E1' => 'Sony Corporation',  '28FDA1' => 'Sony Corporation',
        '3C0771' => 'Sony Corporation',  '4C1684' => 'Sony Corporation',
        '5C514F' => 'Sony Corporation',  '90C115' => 'Sony Corporation',
        'AC9B0A' => 'Sony Corporation',  'B8E673' => 'Sony Corporation',
        'F8B568' => 'Sony Corporation',

        // ── Nintendo ──────────────────────────────────────────────────────
        '001656' => 'Nintendo Co.',     '001F32' => 'Nintendo Co.',
        '002659' => 'Nintendo Co.',     '00224C' => 'Nintendo Co.',
        '002709' => 'Nintendo Co.',     '7CF6DE' => 'Nintendo Co.',
        '9458CB' => 'Nintendo Co.',     'E00C7F' => 'Nintendo Co.',

        // ── Synology ──────────────────────────────────────────────────────
        '001132' => 'Synology Inc.',    '0011AB' => 'Synology Inc.',
        '002590' => 'Synology Inc.',    '089817' => 'Synology Inc.',
        '0C1762' => 'Synology Inc.',    'BC97B1' => 'Synology Inc.',

        // ── QNAP ──────────────────────────────────────────────────────────
        '002433' => 'QNAP Systems',     '247E6B' => 'QNAP Systems',
        'A4AE12' => 'QNAP Systems',

        // ── Xerox (first 3 OUI blocks assigned to Xerox) ─────────────────
        '000000' => 'Xerox Corporation', '000001' => 'Xerox Corporation',
        '000002' => 'Xerox Corporation',
    ];

    public static function normalize(string $mac): ?string
    {
        // Strip all separators: colons, dashes, dots, spaces
        $clean = strtoupper(preg_replace('/[:\-.\s]/', '', $mac));

        // Accept 6 hex chars (OUI only) or 12 hex chars (full MAC)
        if (! preg_match('/^[0-9A-F]{6}$/', $clean) && ! preg_match('/^[0-9A-F]{12}$/', $clean)) {
            return null;
        }

        return $clean;
    }

    public static function format(string $normalized): array
    {
        $parts = str_split($normalized, 2);

        return [
            'colon' => implode(':', $parts),
            'dash'  => implode('-', $parts),
            'dot'   => implode('.', [
                substr($normalized, 0, 4),
                substr($normalized, 4, 4),
                substr($normalized, 8, 4),
            ]),
            'plain' => $normalized,
        ];
    }

    public static function lookup(string $mac): array
    {
        $normalized = self::normalize($mac);

        if ($normalized === null) {
            return ['found' => false, 'error' => 'invalid_mac'];
        }

        $ouiOnly   = strlen($normalized) === 6;
        $oui       = substr($normalized, 0, 6);
        $vendor    = self::resolveVendor($oui);

        $firstByte = hexdec(substr($normalized, 0, 2));
        $multicast = ($firstByte & 0x01) === 1;
        $locally   = ($firstByte & 0x02) === 2;

        $result = [
            'found'               => $vendor !== null,
            'vendor'              => $vendor,
            'oui'                 => implode(':', str_split($oui, 2)),
            'oui_only'            => $ouiOnly,
            'multicast'           => $multicast,
            'locally_administered'=> $locally,
            'error'               => null,
        ];

        if (! $ouiOnly) {
            $result['nic']    = implode(':', str_split(substr($normalized, 6), 2));
            $result['format'] = self::format($normalized);
        }

        return $result;
    }

    // Try DB first; fall back to embedded array.
    private static function resolveVendor(string $oui): ?string
    {
        try {
            $vendor = \Illuminate\Support\Facades\DB::table('oui_vendors')
                ->where('prefix', $oui)
                ->value('vendor');

            if ($vendor !== null) {
                return $vendor;
            }
        } catch (\Throwable) {
            // DB unavailable (e.g. migration not yet run) — use fallback
        }

        return self::$oui[$oui] ?? null;
    }

    // Exposed for OuiVendorSeeder fallback.
    public static function ouiArray(): array
    {
        return self::$oui;
    }

    public static function validate(string $mac): bool
    {
        return self::normalize($mac) !== null;
    }
}
