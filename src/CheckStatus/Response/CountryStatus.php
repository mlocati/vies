<?php

namespace MLocati\Vies\CheckStatus\Response;

class CountryStatus
{
    const AVAILABILITY_AVAILABLE = 'Available';

    const AVAILABILITY_UNAVAILABLE = 'Unavailable';

    const AVAILABILITY_MONITORING_DISABLED = 'Monitoring Disabled';

    /**
     * @var array
     */
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->data['countryCode'];
    }

    /**
     * @return string
     *
     * @see \MLocati\Vies\CheckStatus\Response::AVAILABILITY_AVAILABLE
     * @see \MLocati\Vies\CheckStatus\Response::AVAILABILITY_UNAVAILABLE
     * @see \MLocati\Vies\CheckStatus\Response::AVAILABILITY_MONITORING_DISABLED
     */
    public function getAvailability()
    {
        return isset($this->data['availability']) && is_string($this->data['availability']) ? $this->data['availability'] : '';
    }

    /**
     * @return bool
     */
    public function isAvailable()
    {
        return $this->getAvailability() === static::AVAILABILITY_AVAILABLE;
    }

    /**
     * @return array
     */
    public function getRawData()
    {
        return $this->data;
    }
}
