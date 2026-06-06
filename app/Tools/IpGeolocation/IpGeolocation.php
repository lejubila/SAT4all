<?php

namespace App\Tools\IpGeolocation;

/**
 * Normalizza la risposta grezza di ip-api.com in un array uniforme.
 *
 * Logica pura: nessuna dipendenza da Laravel, HTTP o I/O.
 */
class IpGeolocation
{
    /**
     * Campi richiesti all'API (evita dati non necessari).
     */
    public const FIELDS = 'status,message,country,countryCode,region,regionName,city,zip,lat,lon,timezone,isp,org,as,query';

    /**
     * URL base dell'endpoint.
     */
    public const ENDPOINT = 'http://ip-api.com/json/';

    /**
     * Normalizza la risposta JSON dell'API in un risultato applicativo.
     *
     * @param  array<string, mixed>  $raw
     * @return array{
     *   ip: string,
     *   country: string,
     *   country_code: string,
     *   region: string,
     *   city: string,
     *   zip: string,
     *   lat: float|null,
     *   lon: float|null,
     *   timezone: string,
     *   isp: string,
     *   org: string,
     *   as: string,
     *   error: string|null,
     * }
     */
    public static function normalize(array $raw): array
    {
        if (($raw['status'] ?? '') !== 'success') {
            return array_merge(self::empty(), [
                'ip'    => $raw['query'] ?? '',
                'error' => $raw['message'] ?? 'API error',
            ]);
        }

        return [
            'ip'           => $raw['query']      ?? '',
            'country'      => $raw['country']     ?? '',
            'country_code' => $raw['countryCode'] ?? '',
            'region'       => ($raw['region'] ?? '').($raw['region'] && $raw['regionName'] ? ' — '.$raw['regionName'] : ($raw['regionName'] ?? '')),
            'city'         => $raw['city']        ?? '',
            'zip'          => $raw['zip']         ?? '',
            'lat'          => isset($raw['lat']) ? (float) $raw['lat'] : null,
            'lon'          => isset($raw['lon']) ? (float) $raw['lon'] : null,
            'timezone'     => $raw['timezone']    ?? '',
            'isp'          => $raw['isp']         ?? '',
            'org'          => $raw['org']         ?? '',
            'as'           => $raw['as']          ?? '',
            'error'        => null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function empty(): array
    {
        return [
            'ip' => '', 'country' => '', 'country_code' => '',
            'region' => '', 'city' => '', 'zip' => '',
            'lat' => null, 'lon' => null, 'timezone' => '',
            'isp' => '', 'org' => '', 'as' => '', 'error' => null,
        ];
    }
}
