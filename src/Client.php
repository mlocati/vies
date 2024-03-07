<?php

namespace MLocati\Vies;

use MLocati\Vies\Http\Adapter;
use RuntimeException;

class Client
{
    const DEFAULT_BASE_URL = 'https://ec.europa.eu/taxation_customs/vies/rest-api/';

    /**
     * @var \MLocati\Vies\Http\Adapter
     */
    protected $httpAdapter;

    public function __construct(Adapter $httpAdapter = null)
    {
        $this->httpAdapter = $httpAdapter ?: $this->buildHttpAdapter();
    }

    /**
     * Check the status of each member states.
     * @return \MLocati\Vies\CheckStatus\Response
     */
    public function checkStatus()
    {
        list($statusCode, $responseBody) = $this->httpAdapter->getJson($this->getBaseUrl() . $this->getCheckStatusPath());
        if ($statusCode !== 200) {
            throw $this->buildResponseException($statusCode, $responseBody);
        }

        return new CheckStatus\Response($this->decodeJson($responseBody));
    }

    /**
     * @throws \RuntimeException
     *
     * @return \MLocati\Vies\CheckVat\Response
     */
    public function checkVatNumber(CheckVat\Request $request)
    {
        if (trim($request->getCountryCode()) === '') {
            throw new RuntimeException('Missing field: country code', 400);
        }
        if (trim($request->getVatNumber()) === '') {
            throw new RuntimeException('Missing field: VAT number', 400);
        }
        list($statusCode, $responseBody) = $this->httpAdapter->postJson(
            $this->getBaseUrl() . $this->getCheckVatNumberPath(),
            json_encode($request)
        );
        if ($statusCode !== 200) {
            throw $this->buildResponseException($statusCode, $responseBody);
        }

        return new CheckVat\Response($this->decodeJson($responseBody));
    }

    /**
     * @return \MLocati\Vies\Http\Adapter
     */
    protected function buildHttpAdapter()
    {
        if (Adapter\Guzzle::isAvailable()) {
            return new Adapter\Guzzle();
        }
        if (Adapter\Curl::isAvailable()) {
            return new Adapter\Curl();
        }
        if (Adapter\Zend::isAvailable()) {
            return new Adapter\Zend();
        }
        if (Adapter\Stream::isAvailable()) {
            return new Adapter\Stream();
        }

        throw new RuntimeException('No HTTP adapter is available. You can add Guzzle to your project, or enable the HTTP stream wrapper of PHP');
    }

    /**
     * @return string
     */
    protected function getCheckStatusPath()
    {
        return 'check-status';
    }

    /**
     * @return string
     */
    protected function getCheckVatNumberPath()
    {
        return 'check-vat-number';
    }

    /**
     * @param string $json
     *
     * @throws \RuntimeException
     *
     * @return mixed
     */
    protected function decodeJson($json)
    {
        if ($json === 'null') {
            return null;
        }
        $result = json_decode($json, true);
        if ($result  === null) {
            throw new RuntimeException("Failed to decode the following JSON:\n{$json}");
        }

        return $result;
    }

    /**
     * @param int $statusCode
     * @param string $responseBody
     *
     * @return \RuntimeException
     */
    protected function buildResponseException($statusCode, $responseBody)
    {
        return $this->buildResponseExceptionFromResponseBody($statusCode, $responseBody) ?: $this->buildResponseExceptionFromStatusCode($statusCode, $responseBody);
    }

    /**
     * @param int $statusCode
     * @param string $responseBody
     *
     * @return \RuntimeException|null
     */
    protected function buildResponseExceptionFromResponseBody($statusCode, $responseBody)
    {
        if (!$responseBody) {
            return null;
        }
        try {
            $data = $this->decodeJson($responseBody);
        } catch (RuntimeException $x) {
            return null;
        }
        if (!is_array($data) || !isset($data['errorWrappers']) || !is_array($data['errorWrappers'])) {
            return null;
        }
        $lines = [];
        foreach ($data['errorWrappers'] as $errorWrapper) {
            if (!is_array($errorWrapper)) {
                continue;
            }
            $errorCode = isset($errorWrapper['error']) && is_string($errorWrapper['error']) ? trim($errorWrapper['error']) : '';
            $errorMessage = isset($errorWrapper['message']) && is_string($errorWrapper['message']) ? trim($errorWrapper['message']) : '';
            if ($errorCode !== '' && $errorMessage !== '') {
                $lines[] = "[{$errorCode}] {$errorMessage}";
            } elseif ($errorCode !== '') {
                $lines[] = "[{$errorCode}]";
            } elseif ($errorMessage !== '') {
                $lines[] = $errorMessage;
            }
        }
        return $lines === [] ? null : new RuntimeException(implode("\n", $lines), $statusCode);
    }

    /**
     * @param int $statusCode
     * @param string $responseBody
     *
     * @return \RuntimeException
     */
    protected function buildResponseExceptionFromStatusCode($statusCode, $responseBody)
    {
        switch ($statusCode) {
            case 400:
                throw new RuntimeException('Bad Request', $statusCode);
            case 499:
                throw new RuntimeException($responseBody, $statusCode);
            case 500:
                throw new RuntimeException('Internal server error', $statusCode);
        }

        throw new RuntimeException("Unexpected HTTP response code: {$statusCode}", $statusCode);
    }

    /**
     * @return string
     */
    protected function getBaseUrl()
    {
        return static::DEFAULT_BASE_URL;
    }
}
