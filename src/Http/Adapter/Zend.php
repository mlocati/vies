<?php

namespace MLocati\Vies\Http\Adapter;

use MLocati\Vies\Http\Adapter;

use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Http\Response;

class Zend implements Adapter
{
    /**
     * @var \Zend\Http\Client
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
        $request = new Request();
        $request
            ->setUri($url)
            ->setMethod('GET')
        ;

        return $this->invoke($request);
    }

    /**
     * {@inheritdoc}
     *
     * @see \MLocati\Vies\Http\Adapter::postJson()
     */
    public function postJson($url, $json)
    {
        $request = new Request();
        $request
            ->setUri($url)
            ->setMethod('POST')
            ->setContent($json)
        ;
        $request->getHeaders()->addHeaderLine('Content-Type', 'application/json');

        return $this->invoke($request);
    }

    /**
     * @param string $method
     * @param string $url
     *
     * @return array{0: int, 1: string}
     */
    protected function invoke(Request $request)
    {
        if (!$request->getHeaders()->has('Accept')) {
            $request->getHeaders()->addHeaderLine('Accept', 'application/json');
        }
        $response = $this->performRequest($request);

        return $this->parseResponse($response);
    }

    /**
     * @return \Zend\Http\Response
     */
    protected function performRequest(Request $request)
    {
        return $this->client->send($request);
    }

    /**
     * @return array{0: int, 1: string}
     */
    protected function parseResponse(Response $response)
    {
        return [
            $response->getStatusCode(),
            $response->getBody()
        ];
    }
}
