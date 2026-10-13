<?php

declare(strict_types=1);

namespace Moudarir\Helpers\Datetime\Traits;

use DateInvalidTimeZoneException;
use DateTimeZone;

trait DatetimeConfigurationTrait
{

    /**
     * @throws DateInvalidTimeZoneException
     */
    public static function timeZone(): DateTimeZone
    {
        return static::$tzCache ??= new DateTimeZone(static::$defaultTimezone);
    }

    /**
     * @throws DateInvalidTimeZoneException
     */
    public static function changeTimeZone(string $timezone): void
    {
        $timezoneObject = new DateTimeZone($timezone);

        static::$defaultTimezone = $timezoneObject->getName();
        static::$tzCache = $timezoneObject;
        static::$formatterCache = [];
    }

    public static function setLocale(string $locale): void
    {
        static::$defaultLocale = $locale;
        static::$languages = null;
        static::$formatterCache = [];
        static::$messageFormatterCache = [];
    }

    protected static function languageLocale(): string
    {
        $locale = str_replace('-', '_', static::$defaultLocale);
        $language = strtolower(explode('_', $locale, 2)[0]);

        if (preg_match('/^[a-z]{2,3}$/D', $language) !== 1) {
            return 'fr';
        }

        $path = dirname(__DIR__) . '/Lang/' . $language . '.php';

        return is_file($path) ? $language : 'fr';
    }

    protected static function languageTexts(): array
    {
        if (static::$languages !== null) {
            return static::$languages;
        }

        $language = static::languageLocale();
        $dirname = dirname(__DIR__) . '/Lang/';
        $texts = require $dirname . $language . '.php';

        if (is_array($texts) === false) {
            $texts = require $dirname . 'fr.php';
        }

        return static::$languages = $texts;
    }
}
