<?php

namespace MLocati\Vies\Http\Adapter;

use MLocati\Vies\Http\Adapter;

use GuzzleHttp\Client;

class Guzzle implements Adapter
{
    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * {@inheritdoc}
     *
     * @see \MLocati\Vies\Http\Adapter::isAvailable()
     */
    public static function isAvailable()
    {
        return class_exists(Client::class);
    }

    public function __construct(Client $client = null)
    {
        $this->client = $client === null ? new Client() : $client;
    }

    /**
     * {@inheritdoc}
     *
     * @see \MLocati\Vies\Http\Adapter::getJson()
     */
    public function getJson($url)
    {
        return $this->invoke('GET', $url, []);
    }

    /**
     * {@inheritdoc}
     *
     * @see \MLocati\Vies\Http\Adapter::postJson()
     */
    public function postJson($url, $json)
    {
        return $this->invoke('POST', $url, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => $json,
        ]);
    }

    /**
     * @param string $method
     * @param string $url
     *
     * @return array{0: int, 1: string}
     */
    protected function invoke($method, $url, array $options)
    {
        $response = $this->performRequest($method, $url, $options);

        return $this->parseResponse($response);
    }

    /**
     * @param string $method
     * @param string $url
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function performRequest($method, $url, array $options)
    {
        return $this->client->request($method, $url, array_merge_recursive([
            'headers' => [
                'Accept' => 'application/json',
            ],
            'http_errors' => false,
        ], $options));
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return array{0: int, 1: string}
     */
    protected function parseResponse($response)
    {
        return [
            $response->getStatusCode(),
            (string) $response->getBody()
        ];
    }
}
