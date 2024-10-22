> [!CAUTION]
> This project is deprecated: please use https://github.com/mlocati/vat-lib

[![Tests](https://github.com/mlocati/vies/actions/workflows/tests.yml/badge.svg)](https://github.com/mlocati/vies/actions/workflows/tests.yml)

# A PHP library to validate European VAT numbers with VIES VoW

The European Commission offers [VIES VoW (VAT Information Exchange System - Vies-on-the-web)](https://ec.europa.eu/taxation_customs/vies), an online service that allows the verification of VAT numbers of the member states of the European Union.

This PHP library allows you to use the VIES services in an easy way.

## Checking the status of the VIES services

```php
$vies = new \MLocati\Vies\Client();
$status = $vies->checkStatus();
if ($status->getVowStatus()->isAvailable()) {
    echo "The Vies-on-the-web service is available\n";
} else {
    echo "The Vies-on-the-web service is NOT available\n";
}

$countryCodes = $status->getCountryCodes();
echo 'Vies supports these countries: ', implode(', ', $countryCodes), "\n";
// Sample outout: AT, BE, BG, CY, CZ, DE, DK, EE, EL, ES, FI, FR, HR, HU, IE, IT, LT, LU, LV, MT, NL, PL, PT, RO, SE, SI, SK, XI

$countryStatus = $status->getCountryStatus('IT');
if ($countryStatus->isAvailable()) {
    echo "Italian VAT validation is available\n"; 
} else {
    echo "Italian VAT validation is NOT available (", $countryStatus->getAvailability(), ")\n";
}
```

## Checking the validity of a VAT code

If you want to check if `00159560366` is a valid Italian VAT number, you can write something like this:

```php
$vies = new \MLocati\Vies\Client();
$request = new \MLocati\Vies\CheckVat\Request('IT', '00159560366');
$response = $vies->checkVatNumber($request);
if ($response->isValid()) {
    echo "The VAT number {$request->getCountryCode()}-{$request->getVatNumber()} is correct: it's assigned to the '{$response->getName()}' company\n";
} else {
    echo "The VAT number {$request->getCountryCode()}-{$request->getVatNumber()} is NOT correct\n";
}
```

The code above may output:

```
The VAT number IT-00159560366 is correct: it's assigned to the 'FERRARI S.P.A.' company
```

## Country Codes

VIES uses the [ISO-3166 country codes](https://www.iso.org/iso-3166-country-codes.html), with two exceptions:

- Greece: the ISO 3166 country code is `GR`, the VIES country code is `EL`
- Northern Ireland: after the brexit, VIES only supports the VAT codes of Northern Ireland (VIES identifies it with the `XI` code).

You can use the methods provided `MLocati\Vies\CountryCodes` for conversions: see its well documented code for reference.

## Requirements

This library works with any PHP version from 5.5 to the latest one (PHP 8.3 at the time of writing).

Unlike other libraries, this library uses the VIES REST API: this means that you don't need the SOAP PHP extension.

Furthermore, the REST HTTP calls may be performed with various adapters:

- The [Guzzle library](https://github.com/guzzle/guzzle)
- The [PHP cURL extension](https://www.php.net/manual/en/book.curl.php)
- The [Zend HTTP library](https://github.com/zendframework/zend-http)
- The [PHP `http` stream wrapper](https://www.php.net/manual/en/wrappers.php)

By default, this Vies library automatically detects the available adapter, but of course you can specify your own.

For example, if you are using Guzzle and you need to configure a proxy, you can write something like this:

```php
$guzzle = new \GuzzleHttp\Client([
    'proxy' => 'http://username:password@192.168.16.1:10',
]);
$adapter = new \MLocati\Vies\Http\Adapter\Guzzle($guzzle);
$vies = new \MLocati\Vies\Client($adapter);
```

The same can be done using the cURL PHP extension:

```php
$adapter = new \MLocati\Vies\Http\Adapter\Curl([
    CURLOPT_PROXY => 'http://username:password@192.168.16.1:10',
]);
$vies = new \MLocati\Vies\Client($adapter);
```
