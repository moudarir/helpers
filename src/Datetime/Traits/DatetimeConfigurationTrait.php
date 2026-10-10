<?php

declare(strict_types=1);

namespace Moudarir\Helpers\Datetime\Traits;

use DateInvalidTimeZoneException;
use DateTimeZone;
use InvalidArgumentException;

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
        static::$formatterCache = [];
    }

    /**
     * Applies partial, recursively validated language text replacements.
     *
     * Unknown keys and incompatible value types are rejected.
     * All replacements are validated before any configuration is changed.
     *
     * @param array<int, array{0?: string, 1?: string}>|null $unitsShort
     * @param array<int, array{0?: string, 1?: string}>|null $unitsLong
     * @param array{short?: string, long?: string}|null $zeroText
     * @param array{
     *     past?: string,
     *     future?: string,
     *     now?: string,
     *     units?: array{
     *         year?: array{0?: string, 1?: string},
     *         month?: array{0?: string, 1?: string},
     *         week?: array{0?: string, 1?: string},
     *         day?: array{0?: string, 1?: string},
     *         hour?: array{0?: string, 1?: string},
     *         minute?: array{0?: string, 1?: string},
     *         second?: array{0?: string, 1?: string}
     *     }
     * }|null $agoTexts
     *
     * @throws InvalidArgumentException
     */
    public static function setLanguageTexts(
        ?array $unitsShort = null,
        ?array $unitsLong = null,
        ?array $zeroText = null,
        ?array $agoTexts = null
    ): void {
        $nextUnitsShort = static::$unitsShort;
        $nextUnitsLong = static::$unitsLong;
        $nextZeroText = static::$zeroText;
        $nextAgoTexts = static::$agoTexts;

        if ($unitsShort !== null && $unitsShort !== []) {
            $nextUnitsShort = self::replaceLanguageTexts(
                $nextUnitsShort,
                $unitsShort,
                'unitsShort'
            );
        }

        if ($unitsLong !== null && $unitsLong !== []) {
            $nextUnitsLong = self::replaceLanguageTexts(
                $nextUnitsLong,
                $unitsLong,
                'unitsLong'
            );
        }

        if ($zeroText !== null && $zeroText !== []) {
            $nextZeroText = self::replaceLanguageTexts(
                $nextZeroText,
                $zeroText,
                'zeroText'
            );
        }

        if ($agoTexts !== null && $agoTexts !== []) {
            $nextAgoTexts = self::replaceLanguageTexts(
                $nextAgoTexts,
                $agoTexts,
                'agoTexts'
            );
        }

        if ($unitsShort !== null && $unitsShort !== []) {
            static::$unitsShort = $nextUnitsShort;
        }

        if ($unitsLong !== null && $unitsLong !== []) {
            static::$unitsLong = $nextUnitsLong;
        }

        if ($zeroText !== null && $zeroText !== []) {
            static::$zeroText = $nextZeroText;
        }

        if ($agoTexts !== null && $agoTexts !== []) {
            static::$agoTexts = $nextAgoTexts;
        }
    }

    /**
     * Recursively replaces values while preserving unspecified keys.
     *
     * @param array<int|string, mixed> $current
     * @param array<int|string, mixed> $replacement
     * @return array<int|string, mixed>
     *
     * @throws InvalidArgumentException
     */
    private static function replaceLanguageTexts(array $current, array $replacement, string $path): array
    {
        foreach ($replacement as $key => $value) {
            if (array_key_exists($key, $current) === false) {
                throw new InvalidArgumentException(
                    sprintf('Unknown language text key `%s` in `%s`.', $key, $path)
                );
            }

            $keyPath = $path . '[' . $key . ']';
            $expected = $current[$key];

            if (is_array($expected)) {
                if (is_array($value) === false) {
                    throw new InvalidArgumentException(
                        sprintf('Expected an array for `%s`.', $keyPath)
                    );
                }

                $current[$key] = self::replaceLanguageTexts($expected, $value, $keyPath);

                continue;
            }

            if (is_string($value) === false) {
                throw new InvalidArgumentException(
                    sprintf('Expected a string for `%s`.', $keyPath)
                );
            }

            $current[$key] = $value;
        }

        return $current;
    }
}
