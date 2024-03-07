<?php

namespace MLocati\Vies\CheckStatus\Response;

class VowStatus
{
    /**
     * @var array
     */
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return bool|null
     */
    public function isAvailable()
    {
        return isset($this->data['available']) && is_bool($this->data['available']) ? $this->data['available'] : null;
    }

    /**
     * @return array
     */
    public function getRawData()
    {
        return $this->data;
    }
}
