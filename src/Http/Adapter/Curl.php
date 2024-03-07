<?php

namespace MLocati\Vies\Http\Adapter;

use MLocati\Vies\Http\Adapter;
use RuntimeException;

class Curl implements Adapter
{
    /**
     * @var array
     */
    protected $customOptions;

    public function __construct(array $customOptions = [])
    {
        $this->customOptions = $customOptions;
    }

    /**
     * {@inheritdoc}
     *
     * @see \MLocati\Vies\Http\Adapter::isAvailable()
     */
    public static function isAvailable()
    {
        return extension_loaded('curl');
    }

    /**
     * {@inheritdoc}
     *
     * @see \MLocati\Vies\Http\Adapter::getJson()
     */
    public function getJson($url)
    {
        return $this->invoke($url, []);
    }

    /**
     * {@inheritdoc}
     *
     * @see \MLocati\Vies\Http\Adapter::postJson()
     */
    public function postJson($url, $json)
    {
        return $this->invoke($url, [
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $json,
        ]);
    }

    /**
     * @param string $url
     *
     * @return array{0: int, 1: string}
     */
    protected function invoke($url, array $options)
    {
        $ch = $this->createCurl();
        try {
            $this->setCurlOptions($ch, [CURLOPT_URL => $url] + $options);
            $result = $this->runCurl($ch);
        } finally {
            curl_close($ch);
        }

        return $result;
    }

    /**
     * @return \CurlHandle|resource
     */
    protected function createCurl()
    {
        $ch = curl_init();
        if ($ch === false) {
            throw new RuntimeException('curl_init() failed');
        }

        return $ch;
    }

    /**
     * @param \CurlHandle|resource $ch
     */
    protected function setCurlOptions($ch, array $options)
    {
        $actualOptions = [
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
            ],
            CURLOPT_RETURNTRANSFER => true,
        ];
        foreach ([$options, $this->customOptions] as $merge) {
            foreach ($merge as $key => $value) {
                if (!isset($actualOptions[$key])) {
                    $actualOptions[$key] = $value;
                    continue;
                }
                if (is_array($actualOptions[$key]) && is_array($value)) {
                    $actualOptions[$key] = array_merge($actualOptions[$key], $value);
                }
            }
        }
        if (!curl_setopt_array($ch, $actualOptions)) {
            throw $this->createCurlException('curl_setopt_array() failed', $ch);
        }
    }

    /**
     * @param \CurlHandle|resource $ch
     *
     * @return array{0: int, 1: string}
     */
    protected function runCurl($ch)
    {
        $response = curl_exec($ch);
        if (!is_string($response)) {
            throw $this->createCurlException('curl_exec() failed', $ch);
        }
        $statusCode = curl_getinfo($ch, defined('CURLINFO_RESPONSE_CODE') ? CURLINFO_RESPONSE_CODE : CURLINFO_HTTP_CODE);
        if (!is_int($statusCode)) {
            throw $this->createCurlException('curl_getinfo() failed', $ch);
        }
        return [$statusCode, $response];
    }

    /**
     * @param \CurlHandle|resource $ch

     * @return \RuntimeException
     */
    protected function createCurlException($message, $ch)
    {
        $err = curl_error($ch);
        $err = is_string($err) ? trim($err) : '';
        if ($err !== '') {
            return new RuntimeException("{$message}: {$err}");
        }
        $errno = curl_errno($ch);
        if (is_int($errno) && $errno !== 0) {
            return new RuntimeException("{$message}: error code {$errno}");
        }

        return new RuntimeException($message);
    }
}
