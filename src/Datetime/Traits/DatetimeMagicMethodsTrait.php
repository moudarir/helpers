<?php

declare(strict_types=1);

namespace Moudarir\Helpers\Datetime\Traits;

use DateTimeZone;
use Throwable;

/**
 * @property-read int $year
 * @property-read int $month
 * @property-read int $day
 * @property-read int $hour
 * @property-read int $minute
 * @property-read int $second
 * @property-read int $micro
 * @property-read int $microsecond
 * @property-read int $timestamp
 * @property-read int $dayOfWeek (0 = Sunday, 6 = Saturday)
 * @property-read int $dayOfYear (1 to 366)
 * @property-read int $weekOfYear (1 to 53)
 * @property-read int $daysInMonth (28 to 31)
 * @property-read string $timezone
 * @property-read string $timezoneName
 */
trait DatetimeMagicMethodsTrait
{

    private const array DYNAMIC_PROPERTIES = [
        'year',
        'month',
        'day',
        'hour',
        'minute',
        'second',
        'micro',
        'microsecond',
        'timestamp',
        'dayOfWeek',
        'dayOfYear',
        'weekOfYear',
        'daysInMonth',
        'timezone',
        'timezoneName',
    ];

    public function __serialize(): array
    {
        return [
            'date' => $this->format('Y-m-d H:i:s.u'),
            'timezone' => $this->getTimezone()->getName(),
        ];
    }

    /**
     * @param array{date?: string, timezone?: string} $data
     */
    public function __unserialize(array $data): void
    {
        try {
            $timezone = new DateTimeZone($data['timezone'] ?? static::$defaultTimezone);

            $this->__construct($data['date'] ?? 'now', $timezone);
        } catch (Throwable $exception) {
            static::reportException($exception);

            $this->__construct('now', new DateTimeZone('UTC'));
        }
    }

    /**
     * Returns the calculated value of a supported date property.
     */
    public function __get(string $name): string|int|null
    {
        return match ($name) {
            'year' => (int) $this->format('Y'),
            'month' => (int) $this->format('m'),
            'day' => (int) $this->format('d'),
            'hour' => (int) $this->format('H'),
            'minute' => (int) $this->format('i'),
            'second' => (int) $this->format('s'),
            'micro',
            'microsecond' => (int) $this->format('u'),
            'timestamp' => $this->getTimestamp(),
            'dayOfWeek' => (int) $this->format('w'),
            'dayOfYear' => (int) $this->format('z') + 1,
            'weekOfYear' => (int) $this->format('W'),
            'daysInMonth' => (int) $this->format('t'),
            'timezone',
            'timezoneName' => $this->getTimezone()->getName(),
            default => null,
        };
    }

    /**
     * Indicates whether a supported dynamic property exists.
     */
    public function __isset(string $name): bool
    {
        return in_array($name, self::DYNAMIC_PROPERTIES, true);
    }
}
