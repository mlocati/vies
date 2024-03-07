<?php

namespace MLocati\Vies\Http\Adapter;

use MLocati\Vies\Http\Adapter;

class Stream implements Adapter
{
    /**
     * {@inheritdoc}
     *
     * @see \MLocati\Vies\Http\Adapter::isAvailable()
     */
    public static function isAvailable()
    {
        return in_array('http', stream_get_wrappers(), true);
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
            'http' => [
                'header' => [
                    'Content-Type: application/json',
                ],
                'method' => 'POST',
                'content' => $json,
            ],
        ]);
    }

    /**
     * @param string $url
     *
     * @return array{0: int, 1: string}
     */
    protected function invoke($url, array $contextOptions)
    {
        $context = $this->createContext($contextOptions);
        $http_response_header = [];
        $response = file_get_contents($url, false, $context);

        return $this->parseResponse($response, $http_response_header);
    }

    /**
     * @return resource
     */
    protected function createContext(array $options)
    {
        $actualOptions = array_merge_recursive([
            'http' => [
                'header' => [
                    'Accept: application/json',
                ],
                'ignore_errors' => true,
            ],
        ], $options);

        return stream_context_create($actualOptions);
    }

    /**
     * @param string|false $response
     * @param string[] $httpResponseHeaders
     *
     * @return array{0: int, 1: string}
     */
    protected function parseResponse($response, array $httpResponseHeaders)
    {
        $chunks = $httpResponseHeaders === [] ? [] : explode(' ', $httpResponseHeaders[0], 3);

        return [
            isset($chunks[1]) && is_numeric($chunks[1]) ? (int) $chunks[1] : 0,
            $response === false ? '' : $response,
        ];
    }
}
