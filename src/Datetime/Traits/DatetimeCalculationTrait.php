<?php

declare(strict_types=1);

namespace Moudarir\Helpers\Datetime\Traits;

use DateInterval;
use DateTimeInterface;
use Throwable;

trait DatetimeCalculationTrait
{

    public function isNowOrFuture(): bool
    {
        return $this->getTimestamp() >= time();
    }

    /**
     * Calculates the number of seconds elapsed since midnight.
     */
    public function secondsSinceMidnight(): int
    {
        return ((int) $this->format('H') * 3600)
            + ((int) $this->format('i') * 60)
            + (int) $this->format('s');
    }

    /**
     * Returns a human-readable representation of a date difference.
     *
     * @param DateTimeInterface|null $other Comparison date, or now if null
     * @param int $parts Number of units to display
     * @param bool $absolute Whether to omit the past/future prefix
     */
    public function ago(?DateTimeInterface $other = null, int $parts = 1, bool $absolute = false): string
    {
        try {
            $target = $other ?? static::now();
            $totalSeconds = abs($target->getTimestamp() - $this->getTimestamp());

            if ($totalSeconds < 5 && $absolute === false) {
                return static::$agoTexts['now'];
            }

            $diff = $this->diff($target, true);
            $days = $diff->d;
            $weeks = intdiv($days, 7);
            $remainingDays = $days % 7;
            $unitsQueue = [];

            if ($diff->y > 0) {
                $unitsQueue[] = [$diff->y, 'year'];
            }

            if ($diff->m > 0) {
                $unitsQueue[] = [$diff->m, 'month'];
            }

            if ($weeks > 0) {
                $unitsQueue[] = [$weeks, 'week'];
            }

            if ($remainingDays > 0) {
                $unitsQueue[] = [$remainingDays, 'day'];
            }

            if ($diff->h > 0) {
                $unitsQueue[] = [$diff->h, 'hour'];
            }

            if ($diff->i > 0) {
                $unitsQueue[] = [$diff->i, 'minute'];
            }

            if ($diff->s > 0) {
                $unitsQueue[] = [$diff->s, 'second'];
            }

            if ($unitsQueue === []) {
                $unitsQueue[] = [0, 'second'];
            }

            $parts = min(7, max(1, $parts));
            $selectedUnits = array_slice($unitsQueue, 0, $parts);
            $formattedParts = [];

            foreach ($selectedUnits as [$value, $unitKey]) {
                $pattern = static::$agoTexts['units'][$unitKey];
                $formatted = static::formatPluralText($value, $pattern);

                if ($formatted === '') {
                    return '';
                }

                $formattedParts[] = $formatted;
            }

            $formattedText = implode(' ', $formattedParts);

            if ($absolute === true) {
                return $formattedText;
            }

            $isPast = $this->getTimestamp() <= $target->getTimestamp();
            $template = $isPast ? static::$agoTexts['past'] : static::$agoTexts['future'];

            return sprintf($template, $formattedText);
        } catch (Throwable $exception) {
            static::reportException($exception);

            return '';
        }
    }

    /**
     * Returns a human-readable representation of a duration in seconds.
     */
    public static function elapsedTime(float|int $time, int $parts = 2, bool $short = true): string
    {
        $totalMs = (int) round($time * 1000);

        if ($totalMs <= 0) {
            $pattern = $short ? static::$zeroText['short'] : static::$zeroText['long'];

            return static::formatPluralText(0, $pattern);
        }

        $parts = max($parts, 1);
        $units = $short ? static::$unitsShort : static::$unitsLong;

        $output = [];
        $count = 0;

        foreach ($units as $msInUnit => $pattern) {
            if ($totalMs < $msInUnit) {
                continue;
            }

            $value = intdiv($totalMs, $msInUnit);
            $totalMs %= $msInUnit;

            $formatted = static::formatPluralText($value, $pattern);

            if ($formatted === '') {
                return '';
            }

            $output[] = $formatted;

            ++$count;

            if ($count === $parts) {
                break;
            }
        }

        return implode(' ', $output);
    }

    /**
     * Converts an ISO 8601 duration to seconds.
     */
    public static function secondsFromIsoDuration(string $isoDuration): int
    {
        try {
            if ($isoDuration === '') {
                return 0;
            }

            $interval = new DateInterval($isoDuration);

            if ($interval->y !== 0 || $interval->m !== 0) {
                return 0;
            }

            return ($interval->d * 86_400)
                + ($interval->h * 3_600)
                + ($interval->i * 60)
                + $interval->s;
        } catch (Throwable $exception) {
            static::reportException($exception);

            return 0;
        }
    }
}
