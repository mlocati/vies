<?php

namespace MLocati\Vies\Http;

interface Adapter
{
    /**
     * Check if the specific adapter is available.
     */
    public static function isAvailable();

    /**
     * @param string $url
     *
     * @return array{0: int, 1: string}
     */
    public function getJson($url);

    /**
     * @param string $url
     * @param string $json
     *
     * @return array{0: int, 1: string}
     */
    public function postJson($url, $json);
}
