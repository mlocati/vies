<?php

if (class_exists(PHPUnit\Runner\Version::class) && version_compare(PHPUnit\Runner\Version::id(), '9') >= 0) {
    class_alias(MLocati\Vies\Test\Service\TestCase9::class, MLocati\Vies\Test\Service\TestCase::class);
} elseif (class_exists(PHPUnit\Runner\Version::class) && version_compare(PHPUnit\Runner\Version::id(), '7') >= 0) {
    class_alias(MLocati\Vies\Test\Service\TestCase7::class, MLocati\Vies\Test\Service\TestCase::class);
} else {
    class_alias(MLocati\Vies\Test\Service\TestCase4::class, MLocati\Vies\Test\Service\TestCase::class);
}

if (class_exists(PHPUnit\Runner\Version::class) && version_compare(PHPUnit\Runner\Version::id(), '7') >= 0) {
    class_alias(MLocati\Vies\Test\Service\EventListener7::class, MLocati\Vies\Test\Service\EventListener::class);
} elseif (class_exists(PHPUnit\Runner\Version::class) && version_compare(PHPUnit\Runner\Version::id(), '6') >= 0) {
    class_alias(MLocati\Vies\Test\Service\EventListener6::class, MLocati\Vies\Test\Service\EventListener::class);
} else {
    class_alias(MLocati\Vies\Test\Service\EventListener4::class, MLocati\Vies\Test\Service\EventListener::class);
}

define('ML_VIES_TEST_ROOTDIR', str_replace(DIRECTORY_SEPARATOR, '/', __DIR__));
