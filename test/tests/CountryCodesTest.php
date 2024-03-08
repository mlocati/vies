<?php

namespace MLocati\Vies\Test;

use MLocati\Vies\Test\Service\TestCase;
use MLocati\Vies\CountryCodes;

class CountryCodesTest extends TestCase
{
    /**
     * @return array
     */
    public static function iso3166ToViesProvider()
    {
        return [
            ['AT'],
            ['BE'],
            ['BG'],
            ['CY'],
            ['CZ'],
            ['DE'],
            ['DK'],
            ['EE'],
            ['GR', 'EL'],
            ['ES'],
            ['FI'],
            ['FR'],
            ['HR'],
            ['HU'],
            ['IE'],
            ['IT'],
            ['LT'],
            ['LU'],
            ['LV'],
            ['MT'],
            ['NL'],
            ['PL'],
            ['PT'],
            ['RO'],
            ['SE'],
            ['SI'],
            ['SK'],
            ['GB', 'XI'],
            ['GB', '', 0],
            ['GB', 'XI', CountryCodes::FLAG_NOTHERNIRELAND],
            ['US'],
        ];
    }

    /**
     * @dataProvider iso3166ToViesProvider
     *
     * @param string $iso
     * @param string|true $expectedVies true if the VIES code is the same as the ISO 3166 code
     * @param int|null $flags
     */
    public function testIso3166ToVies($iso3166Code, $expectedViesCode = true, $flags = null)
    {
        if ($expectedViesCode === true) {
            $expectedViesCode = $iso3166Code;
        }
        $viesCode = CountryCodes::iso3166ToVies($iso3166Code, $flags);
        $this->assertSame($expectedViesCode, $viesCode);
    }

    /**
     * @return array
     */
    public static function viesToIso3166Provider()
    {
        return [
            ['AT'],
            ['BE'],
            ['BG'],
            ['CY'],
            ['CZ'],
            ['DE'],
            ['DK'],
            ['EE'],
            ['EL', 'GR'],
            ['ES'],
            ['FI'],
            ['FR'],
            ['HR'],
            ['HU'],
            ['IE'],
            ['IT'],
            ['LT'],
            ['LU'],
            ['LV'],
            ['MT'],
            ['NL'],
            ['PL'],
            ['PT'],
            ['RO'],
            ['SE'],
            ['SI'],
            ['SK'],
            ['XI', 'GB'],
            ['XI', '', 0],
            ['XI', 'GB', CountryCodes::FLAG_NOTHERNIRELAND],
            ['US'],
        ];
    }

    /**
     * @dataProvider viesToIso3166Provider
     *
     * @param string $viesCode
     * @param string|true $expectedIso3166Code true if the ISO 3166 code is the same as the VIES code
     * @param int|null $flags
     */
    public function testViesToIso3166($viesCode, $expectedIso3166Code = true, $flags = null)
    {
        if ($expectedIso3166Code === true) {
            $expectedIso3166Code = $viesCode;
        }
        $iso3166Code = CountryCodes::viesToIso3166($viesCode, $flags);
        $this->assertSame($expectedIso3166Code, $iso3166Code);
    }
}
