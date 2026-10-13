<?php

declare(strict_types=1);

namespace Moudarir\Helpers\Datetime;

use DateTimeImmutable;
use DateTimeZone;
use IntlDateFormatter;
use MessageFormatter;
use Moudarir\Helpers\Datetime\Traits\DatetimeCalculationTrait;
use Moudarir\Helpers\Datetime\Traits\DatetimeFormattingTrait;
use Moudarir\Helpers\Datetime\Traits\DatetimeModificationTrait;
use Moudarir\Helpers\Datetime\Traits\DatetimeConfigurationTrait;
use Moudarir\Helpers\Datetime\Traits\DatetimeMagicMethodsTrait;
use Throwable;

class DatetimeHelper extends DateTimeImmutable
{

    use DatetimeMagicMethodsTrait;
    use DatetimeConfigurationTrait;
    use DatetimeModificationTrait;
    use DatetimeFormattingTrait;
    use DatetimeCalculationTrait;

    protected static string $defaultTimezone = "UTC";

    protected static string $defaultLocale = "fr";

    protected static ?DateTimeZone $tzCache = null;

    /**
     * @var array<string, IntlDateFormatter>
     */
    protected static array $formatterCache = [];

    /**
     * @var array<string, MessageFormatter>
     */
    protected static array $messageFormatterCache = [];

    private static ?array $languages = null;

    public static function now(): static
    {
        return new static('now', static::timeZone());
    }

    public static function parseFromFormat(int|string $time): ?static
    {
        if (is_int($time)) {
            return static::getFromTimestamp($time);
        }

        if (preg_match('/^-?\d+$/', $time) === 1) {
            return static::getFromTimestamp((int) $time);
        }

        return static::getFromFormat($time);
    }

    public static function getFromTimestamp(int $timestamp): ?static
    {
        try {
            return static::createFromTimestamp($timestamp)->setTimezone(static::timeZone());
        } catch (Throwable $exception) {
            static::reportException($exception);

            return null;
        }
    }

    public static function getFromFormat(string $time): ?static
    {
        try {
            return new static($time, static::timeZone());
        } catch (Throwable $exception) {
            static::reportException($exception);

            return null;
        }
    }

    /**
     * Reports an exception.
     *
     * The default implementation intentionally does nothing.
     * Subclasses can override this method to add exception reporting.
     */
    protected static function reportException(Throwable $exception): void
    {
    }
}
