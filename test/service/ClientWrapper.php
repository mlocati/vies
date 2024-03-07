<?php

namespace MLocati\Vies\Test\Service;

use MLocati\Vies\Client;

class ClientWrapper extends Client
{
    /**
     * @var bool
     */
    private $useFakeServer = true;

    /**
     * @return \MLocati\Vies\Http\Adapter
     */
    public function getHttpAdapter()
    {
        return $this->httpAdapter;
    }

    /**
     * @param bool $useFakeServer
     */
    public function setUseFakeServer($value)
    {
        $this->useFakeServer = (bool) $value;
    }

    /**
     * {@inheritdoc}
     *
     * @see \MLocati\Vies\Client::getBaseUrl()
     */
    protected function getBaseUrl()
    {
        return $this->useFakeServer ? FakeServerManager::getRootURL() : parent::getBaseUrl();
    }

    /**
     * {@inheritdoc}
     *
     * @see \MLocati\Vies\Client::getCheckStatusPath()
     */
    protected function getCheckStatusPath()
    {
        return parent::getCheckStatusPath() . ($this->useFakeServer ? '.php' : '');
    }

    /**
     * {@inheritdoc}
     *
     * @see \MLocati\Vies\Client::getCheckVatNumberPath()
     */
    protected function getCheckVatNumberPath()
    {
        return parent::getCheckVatNumberPath() . ($this->useFakeServer ? '.php' : '');
    }
}
