<?php

declare(strict_types=1);

namespace Moudarir\Helpers\Datetime\Traits;

use IntlDateFormatter;
use Throwable;

trait DatetimeFormattingTrait
{

    public function toAtomString(): string
    {
        return $this->format(self::ATOM);
    }

    public function toIso8601String(): string
    {
        return $this->toAtomString();
    }

    public function toRfc822String(): string
    {
        return $this->format(self::RFC822);
    }

    public function toW3cString(): string
    {
        return $this->format(self::W3C);
    }

    public function toDateString(): string
    {
        return $this->format('Y-m-d');
    }

    public function toDateTimeString(): string
    {
        return $this->format('Y-m-d H:i:s');
    }

    public function isoFormat(string $pattern): string
    {
        try {
            $icuPattern = static::$isoFormatMap[$pattern] ?? $pattern;
            $timezone = $this->getTimezone()->getName();
            $formatter = static::getDateFormatter($icuPattern, $timezone);

            return $formatter->format($this) ?: '';
        } catch (Throwable $exception) {
            static::reportException($exception);

            return '';
        }
    }

    private static function getDateFormatter(string $pattern, string $timezone): IntlDateFormatter
    {
        $key = static::$defaultLocale . '|' . $timezone . '|' . $pattern;

        return static::$formatterCache[$key] ??= new IntlDateFormatter(
            static::$defaultLocale,
            IntlDateFormatter::FULL,
            IntlDateFormatter::FULL,
            $timezone,
            IntlDateFormatter::GREGORIAN,
            $pattern
        );
    }
}
