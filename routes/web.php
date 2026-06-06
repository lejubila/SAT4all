<?php

use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Tools\CableColorsController;
use App\Http\Controllers\Tools\RfcBrowserController;
use App\Http\Controllers\Tools\HttpStatusCodesController;
use App\Http\Controllers\Tools\MacLookupController;
use App\Http\Controllers\Tools\BandwidthCalculatorController;
use App\Http\Controllers\Tools\FormatterController;
use App\Http\Controllers\Tools\BaseConverterController;
use App\Http\Controllers\Tools\RegexTesterController;
use App\Http\Controllers\Tools\SslCheckerController;
use App\Http\Controllers\Tools\WhoisController;
use App\Http\Controllers\Tools\LinuxCheatsheetController;
use App\Http\Controllers\Tools\OsiModelController;
use App\Http\Controllers\Tools\PingTracerouteController;
use App\Http\Controllers\Tools\CableSchemasController;
use App\Http\Controllers\Tools\CidrCheatsheetController;
use App\Http\Controllers\Tools\DnsLookupController;
use App\Http\Controllers\Tools\IpGeolocationController;
use App\Http\Controllers\Tools\VlanCalculatorController;
use App\Http\Controllers\Tools\Ipv6CalculatorController;
use App\Http\Controllers\Tools\PortReferenceController;
use App\Http\Controllers\Tools\SubnetCalculatorController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('home'))->name('home');

Route::post('/language/{locale}', [LanguageController::class, 'switch'])
    ->name('language.switch');

/*
|--------------------------------------------------------------------------
| Tools
|--------------------------------------------------------------------------
| Rotte dei singoli tool: prefisso /tools, nome tools.{nome-tool}.{azione}.
*/
Route::prefix('tools')->name('tools.')->group(function (): void {
    Route::get('subnet-calculator', [SubnetCalculatorController::class, 'index'])
        ->name('subnet-calculator.index');
    Route::post('subnet-calculator', [SubnetCalculatorController::class, 'calculate'])
        ->name('subnet-calculator.calculate');

    Route::get('ipv6-calculator', [Ipv6CalculatorController::class, 'index'])
        ->name('ipv6-calculator.index');
    Route::post('ipv6-calculator', [Ipv6CalculatorController::class, 'calculate'])
        ->name('ipv6-calculator.calculate');

    Route::get('port-reference', [PortReferenceController::class, 'index'])
        ->name('port-reference.index');
    Route::get('port-reference/search', [PortReferenceController::class, 'lookup'])
        ->name('port-reference.lookup');

    Route::get('cable-schemas', [CableSchemasController::class, 'index'])
        ->name('cable-schemas.index');

    Route::get('cidr-cheatsheet', [CidrCheatsheetController::class, 'index'])
        ->name('cidr-cheatsheet.index');

    Route::get('vlan-calculator', [VlanCalculatorController::class, 'index'])
        ->name('vlan-calculator.index');
    Route::post('vlan-calculator', [VlanCalculatorController::class, 'calculate'])
        ->name('vlan-calculator.calculate');

    Route::get('dns-lookup', [DnsLookupController::class, 'index'])
        ->name('dns-lookup.index');
    Route::post('dns-lookup', [DnsLookupController::class, 'lookup'])
        ->name('dns-lookup.lookup')
        ->middleware('throttle:dns-lookup');

    Route::get('ip-geolocation', [IpGeolocationController::class, 'index'])
        ->name('ip-geolocation.index');
    Route::post('ip-geolocation', [IpGeolocationController::class, 'lookup'])
        ->name('ip-geolocation.lookup');

    Route::get('osi-model', [OsiModelController::class, 'index'])
        ->name('osi-model.index');

    Route::get('ping-traceroute', [PingTracerouteController::class, 'index'])
        ->name('ping-traceroute.index');
    Route::post('ping-traceroute', [PingTracerouteController::class, 'run'])
        ->name('ping-traceroute.run');

    Route::get('linux-cheatsheet', [LinuxCheatsheetController::class, 'index'])
        ->name('linux-cheatsheet.index');

    Route::get('cable-colors', [CableColorsController::class, 'index'])
        ->name('cable-colors.index');

    Route::get('rfc-browser', [RfcBrowserController::class, 'index'])
        ->name('rfc-browser.index');

    Route::get('http-status-codes', [HttpStatusCodesController::class, 'index'])
        ->name('http-status-codes.index');

    Route::get('mac-lookup', [MacLookupController::class, 'index'])
        ->name('mac-lookup.index');
    Route::post('mac-lookup', [MacLookupController::class, 'lookup'])
        ->name('mac-lookup.lookup');

    Route::get('whois', [WhoisController::class, 'index'])
        ->name('whois.index');
    Route::post('whois', [WhoisController::class, 'lookup'])
        ->name('whois.lookup');

    Route::get('regex-tester', [RegexTesterController::class, 'index'])
        ->name('regex-tester.index');
    Route::post('regex-tester/test', [RegexTesterController::class, 'test'])
        ->name('regex-tester.test');

    Route::get('ssl-checker', [SslCheckerController::class, 'index'])
        ->name('ssl-checker.index');
    Route::post('ssl-checker/check', [SslCheckerController::class, 'check'])
        ->name('ssl-checker.check');

    Route::get('base-converter', [BaseConverterController::class, 'index'])
        ->name('base-converter.index');
    Route::post('base-converter/convert', [BaseConverterController::class, 'convert'])
        ->name('base-converter.convert');

    Route::get('bandwidth-calculator', [BandwidthCalculatorController::class, 'index'])
        ->name('bandwidth-calculator.index');
    Route::post('bandwidth-calculator/calculate', [BandwidthCalculatorController::class, 'calculate'])
        ->name('bandwidth-calculator.calculate');

    Route::get('formatter', [FormatterController::class, 'index'])
        ->name('formatter.index');
    Route::post('formatter/format', [FormatterController::class, 'format'])
        ->name('formatter.format');
});
