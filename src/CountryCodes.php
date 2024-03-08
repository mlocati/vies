<?php

namespace MLocati\Vies;

class CountryCodes
{
    const FLAG_NOTHERNIRELAND = 1;

    /**
     * VIES country code of Greece.
     *
     * @var string
     */
    const VIES_GREECE = 'EL';

    /**
     * ISO 3166 country code of Greece.
     *
     * @var string
     */
    const ISO3166_GREECE = 'GR';

    /**
     * VIES code for Northern Ireland (which is part of the United Kingdom).
     *
     * @var string
     */
    const VIES_NOTHERNIRELAND = 'XI';

    /**
     * ISO 3166 country code of the United Kingdom.
     *
     * @var string
     */
    const ISO3166_UNITEDKINGDOM = 'GB';

    /**
     * Convert a VIES country code to an ISO 3166 country code.
     *
     * @param string $viesCode the VIES code
     * @param int|null $flags bitmask of FLAG_... constants (if NULL: we'll assume FLAG_NOTHERNIRELAND)
     *
     * @return string empty string if $viesCode is XI and $flags is not null and doesn't contain FLAG_NOTHERNIRELAND
     */
    public static function viesToIso3166($viesCode, $flags = null)
    {
        $flags = $flags === null ? static::FLAG_NOTHERNIRELAND : (int) $flags;

        switch ($viesCode) {
            case static::VIES_GREECE:
                return static::ISO3166_GREECE;
            case static::VIES_NOTHERNIRELAND:
                return ($flags & static::FLAG_NOTHERNIRELAND) === 0 ? '' : static::ISO3166_UNITEDKINGDOM;
            default:
                return $viesCode;
        }
    }

    /**
     * Convert an ISO 3166 country code to a VIES country code.
     *
     * @param string $iso3166Code The ISO 3166 country code
     * @param int|null $flags bitmask of FLAG_... constants (if NULL: we'll assume FLAG_NOTHERNIRELAND)
     *
     * @return string empty string if $iso3166Code is 'GB' and $flags is not null and doesn't contain FLAG_NOTHERNIRELAND
     */
    public static function iso3166ToVies($iso3166Code, $flags = null)
    {
        $flags = $flags === null ? static::FLAG_NOTHERNIRELAND : (int) $flags;
        switch ($iso3166Code) {
            case static::ISO3166_GREECE:
                return static::VIES_GREECE;
            case static::ISO3166_UNITEDKINGDOM:
                return ($flags & static::FLAG_NOTHERNIRELAND) === 0 ? '' : static::VIES_NOTHERNIRELAND;
            default:
                return $iso3166Code;
        }
    }
}
