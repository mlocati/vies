<?php

namespace MLocati\Vies\Test;

use DateInterval;
use DateTimeImmutable;
use MLocati\Vies\Http\Adapter;
use MLocati\Vies\Test\Service\TestCase;
use MLocati\Vies\Test\Service\ClientWrapper;
use MLocati\Vies;

class HttpAdaptersTest extends TestCase
{
    public function testGuzzle()
    {
        if (!class_exists(\GuzzleHttp\Client::class)) {
            $this->assertFalse(Adapter\Guzzle::isAvailable());
            return;
        }
        $this->assertTrue(Adapter\Guzzle::isAvailable());
        $adapter = new Adapter\Guzzle();
        $vies = new ClientWrapper($adapter);
        $this->assertSame($adapter, $vies->getHttpAdapter());
        $this->checkCommunication($vies);
    }

    public function testStream()
    {
        if (!in_array('http', stream_get_wrappers(), true)) {
            $this->assertFalse(Adapter\Stream::isAvailable());
            return;
        }
        $this->assertTrue(Adapter\Stream::isAvailable());
        $adapter = new Adapter\Stream();
        $vies = new ClientWrapper($adapter);
        $this->assertSame($adapter, $vies->getHttpAdapter());
        $this->checkCommunication($vies);
    }

    public function testZend()
    {
        if (!class_exists(\Zend\Http\Client::class)) {
            $this->assertFalse(Adapter\Zend::isAvailable());
            return;
        }
        $this->assertTrue(Adapter\Zend::isAvailable());
        $adapter = new Adapter\Zend();
        $vies = new ClientWrapper($adapter);
        $this->assertSame($adapter, $vies->getHttpAdapter());
        $this->checkCommunication($vies);
    }

    public function testCurl()
    {
        if (!function_exists('curl_init')) {
            $this->assertFalse(Adapter\Curl::isAvailable());
            return;
        }
        $this->assertTrue(Adapter\Curl::isAvailable());
        $adapter = new Adapter\Curl();
        $vies = new ClientWrapper($adapter);
        $this->assertSame($adapter, $vies->getHttpAdapter());
        $this->checkCommunication($vies);
    }

    private function checkCommunication(ClientWrapper $vies)
    {
        $this->checkStatus($vies);
        $this->checkVatNumber($vies);
    }

    private function checkStatus(ClientWrapper $vies)
    {
        $status = $vies->checkStatus();
        $this->assertInstanceOf(Vies\CheckStatus\Response::class, $status);
        $vowStatus = $status->getVowStatus();
        $this->assertInstanceOf(Vies\CheckStatus\Response\VowStatus::class, $vowStatus);
        $this->assertIsBool($vowStatus->isAvailable());
        $this->assertNotContains('12', $status->getCountryCodes());
        $this->assertNull($status->getCountryStatus('12'));
        $countryCodes = $status->getCountryCodes();
        $this->assertContains('IT', $status->getCountryCodes());
        foreach ($countryCodes as $countryCode) {
            $countryStatus = $status->getCountryStatus($countryCode);
            $this->assertInstanceOf(Vies\CheckStatus\Response\CountryStatus::class, $countryStatus);
            $this->assertSame($countryCode, $countryStatus->getCountryCode());
            $availability = $countryStatus->getAvailability();
            $this->assertContains($availability, [
                Vies\CheckStatus\Response\CountryStatus::AVAILABILITY_AVAILABLE,
                Vies\CheckStatus\Response\CountryStatus::AVAILABILITY_MONITORING_DISABLED,
                Vies\CheckStatus\Response\CountryStatus::AVAILABILITY_UNAVAILABLE,
            ]);
            if ($availability === Vies\CheckStatus\Response\CountryStatus::AVAILABILITY_AVAILABLE) {
                $this->assertTrue($countryStatus->isAvailable());
            } else {
                $this->assertFalse($countryStatus->isAvailable());
            }
        }
    }

    private function checkVatNumber(ClientWrapper $vies)
    {
        $request = new Vies\CheckVat\Request();
        $request->setCountryCode('IT')->setVatNumber('00159560366');
        $now = new DateTimeImmutable('now');
        $beforeRequest = $now->sub(new DateInterval('PT1S'));
        $response = $vies->checkVatNumber($request);
        $now = new DateTimeImmutable('now');
        $afterRequest = $now->add(new DateInterval('PT1S'));
        $this->assertInstanceOf(Vies\CheckVat\Response::class, $response);
        $this->assertSame('IT', $response->getCountryCode());
        $this->assertSame('00159560366', $response->getVatNumber());
        $requestDate = $response->getRequestDate();
        $this->assertInstanceOf(DateTimeImmutable::class, $requestDate);
        $this->assertSame($beforeRequest->getTimezone()->getName(), $requestDate->getTimezone()->getName());
        $this->assertGreaterThanOrEqual($beforeRequest, $requestDate);
        $this->assertLessThanOrEqual($afterRequest, $requestDate);
        $this->assertSame(true, $response->isValid());
        $this->assertIsString($response->getRequestIdentifier());
        $this->assertIsString($response->getName());
        $this->assertMatchRegExp('/\bFerrari\b/i', $response->getName());
        $this->assertIsString($response->getAddress());
        $this->assertMatchRegExp('/\bModena\b/i', $response->getAddress());
        $this->assertIsString($response->getTraderName());
        $this->assertIsString($response->getTraderStreet());
        $this->assertIsString($response->getTraderPostalCode());
        $this->assertIsString($response->getTraderCity());
        $this->assertIsString($response->getTraderCompanyType());
        $matches = [
            Vies\CheckVat\Response::MATCH_VALID,
            Vies\CheckVat\Response::MATCH_INVALID,
            Vies\CheckVat\Response::MATCH_NOT_PROCESSED,
        ];
        $this->assertContains($response->getTraderNameMatch(), $matches);
        $this->assertContains($response->getTraderStreetMatch(), $matches);
        $this->assertContains($response->getTraderPostalCodeMatch(), $matches);
        $this->assertContains($response->getTraderCityMatch(), $matches);
        $this->assertContains($response->getTraderCompanyTypeMatch(), $matches);
    }
}
