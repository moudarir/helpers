<?php

declare(strict_types=1);

namespace Moudarir\Helpers\Datetime\Traits;

use IntlDateFormatter;
use MessageFormatter;
use RuntimeException;
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
            $texts = static::languageTexts();
            $icuPattern = $texts['isoFormatMap'][$pattern] ?? $pattern;
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

    /**
     * Formats a number and selects its ICU plural category.
     */
    private static function formatPluralText(int $count, string $pattern): string
    {
        try {
            if (class_exists(MessageFormatter::class) === false) {
                throw new RuntimeException('ICU plural formatting requires the intl extension.');
            }

            $locale = static::languageLocale();
            $key = $locale . '|' . $pattern;

            $formatter = static::$messageFormatterCache[$key]
                ??= MessageFormatter::create($locale, $pattern);

            if ($formatter === null) {
                throw new RuntimeException('Invalid ICU pattern for locale "' . $locale . '".');
            }

            $message = $formatter->format([
                'count' => $count,
                'number' => (string) $count,
            ]);

            if ($message === false) {
                throw new RuntimeException('Unable to format ICU message: ' . $formatter->getErrorMessage());
            }

            return $message;
        } catch (Throwable $exception) {
            static::reportException($exception);

            return '';
        }
    }
}
