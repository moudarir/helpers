<?php

declare(strict_types=1);

namespace Moudarir\Helpers\Tests\Unit\Datetime;

use DateInvalidTimeZoneException;
use DateTimeImmutable;
use DateTimeZone;
use MessageFormatter;
use Moudarir\Helpers\Datetime\DatetimeHelper;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DatetimeHelperTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        DatetimeHelper::changeTimeZone('UTC');
        DatetimeHelper::setLocale('fr_FR');
    }

    #[Test]
    public function timeZone_returnsConfiguredTimezone(): void
    {
        DatetimeHelper::changeTimeZone('Europe/Paris');

        $this->assertSame('Europe/Paris', DatetimeHelper::timeZone()->getName());
    }

    #[Test]
    public function changeTimeZone_rejectsInvalidTimezone(): void
    {
        $this->expectException(DateInvalidTimeZoneException::class);

        DatetimeHelper::changeTimeZone('Invalid/Timezone');
    }

    #[Test]
    public function now_usesConfiguredTimezone(): void
    {
        DatetimeHelper::changeTimeZone('Europe/Paris');

        $this->assertSame('Europe/Paris', DatetimeHelper::now()->getTimezone()->getName());
    }

    #[Test]
    public function getFromTimestamp_usesConfiguredTimezone(): void
    {
        DatetimeHelper::changeTimeZone('Europe/Paris');

        $date = DatetimeHelper::getFromTimestamp(0);

        $this->assertInstanceOf(DatetimeHelper::class, $date);
        $this->assertSame('1970-01-01 01:00:00', $date?->toDateTimeString());
    }

    #[Test]
    public function parseFromFormat_acceptsIntegerTimestamp(): void
    {
        $date = DatetimeHelper::parseFromFormat(0);

        $this->assertInstanceOf(DatetimeHelper::class, $date);
        $this->assertSame('1970-01-01 00:00:00', $date?->toDateTimeString());
    }

    #[Test]
    public function parseFromFormat_acceptsStringTimestamp(): void
    {
        $date = DatetimeHelper::parseFromFormat('0');

        $this->assertInstanceOf(DatetimeHelper::class, $date);
        $this->assertSame('1970-01-01 00:00:00', $date?->toDateTimeString());
    }

    #[Test]
    public function parseFromFormat_acceptsNegativeTimestamp(): void
    {
        $date = DatetimeHelper::parseFromFormat('-1');

        $this->assertInstanceOf(DatetimeHelper::class, $date);
        $this->assertSame('1969-12-31 23:59:59', $date?->toDateTimeString());
    }

    #[Test]
    public function parseFromFormat_parsesDateString(): void
    {
        $date = DatetimeHelper::parseFromFormat('2024-01-02 03:04:05');

        $this->assertInstanceOf(DatetimeHelper::class, $date);
        $this->assertSame('2024-01-02 03:04:05', $date?->toDateTimeString());
    }

    #[Test]
    public function parseFromFormat_returnsNullForInvalidDate(): void
    {
        $this->assertNull(DatetimeHelper::parseFromFormat('not-a-valid-date-value'));
    }

    #[Test]
    public function getFromFormat_usesConfiguredTimezone(): void
    {
        DatetimeHelper::changeTimeZone('Europe/Paris');

        $date = DatetimeHelper::getFromFormat('2024-01-02 12:00:00');

        $this->assertInstanceOf(DatetimeHelper::class, $date);
        $this->assertSame('Europe/Paris', $date?->getTimezone()->getName());
        $this->assertSame('2024-01-02 12:00:00', $date?->toDateTimeString());
    }

    #[Test]
    public function getFromFormat_preservesExplicitTimezone(): void
    {
        $date = DatetimeHelper::getFromFormat('2024-01-02T12:00:00+02:00');

        $this->assertInstanceOf(DatetimeHelper::class, $date);
        $this->assertSame('+02:00', $date?->getTimezone()->getName());
    }

    #[Test]
    public function getFromFormat_returnsNullForInvalidDate(): void
    {
        $this->assertNull(DatetimeHelper::getFromFormat('not-a-valid-date-value'));
    }

    #[Test]
    public function dynamicProperties_returnsExpectedValues(): void
    {
        $date = new DatetimeHelper('2024-02-29 03:04:05.123456', new DateTimeZone('UTC'));

        $this->assertSame(2024, $date->year);
        $this->assertSame(2, $date->month);
        $this->assertSame(29, $date->day);
        $this->assertSame(3, $date->hour);
        $this->assertSame(4, $date->minute);
        $this->assertSame(5, $date->second);
        $this->assertSame(123456, $date->micro);
        $this->assertSame(123456, $date->microsecond);
        $this->assertSame($date->getTimestamp(), $date->timestamp);
        $this->assertSame(4, $date->dayOfWeek);
        $this->assertSame(60, $date->dayOfYear);
        $this->assertSame(9, $date->weekOfYear);
        $this->assertSame(29, $date->daysInMonth);
        $this->assertSame('UTC', $date->timezone);
        $this->assertSame('UTC', $date->timezoneName);
        $this->assertNull($date->unknown);
    }

    #[Test]
    public function dynamicProperties_issetRecognizesSupportedProperties(): void
    {
        $date = new DatetimeHelper('2024-01-01 00:00:00', new DateTimeZone('UTC'));

        $this->assertTrue(isset($date->year));
        $this->assertTrue(isset($date->microsecond));
        $this->assertTrue(isset($date->timezone));
        $this->assertFalse(isset($date->unknown));
    }

    #[Test]
    public function serialization_restoresDateAndTimezone(): void
    {
        $date = new DatetimeHelper(
            '2024-02-29 03:04:05.123456',
            new DateTimeZone('Europe/Paris')
        );

        $restored = unserialize(serialize($date));

        $this->assertInstanceOf(DatetimeHelper::class, $restored);
        $this->assertSame(
            '2024-02-29 03:04:05.123456',
            $restored->format('Y-m-d H:i:s.u')
        );
        $this->assertSame('Europe/Paris', $restored->getTimezone()->getName());
    }

    #[Test]
    public function modify_returnsSameClassAndPreservesTimezone(): void
    {
        $date = new DatetimeHelper('2024-01-02 12:00:00', new DateTimeZone('Europe/Paris'));

        $modified = $date->modify('+1 day');

        $this->assertInstanceOf(DatetimeHelper::class, $modified);
        $this->assertSame('2024-01-03 12:00:00', $modified->toDateTimeString());
        $this->assertSame('Europe/Paris', $modified->getTimezone()->getName());
        $this->assertSame('2024-01-02 12:00:00', $date->toDateTimeString());
    }

    #[Test]
    public function modify_returnsOriginalInstanceForInvalidModifier(): void
    {
        $date = new DatetimeHelper('2024-01-02 12:00:00', new DateTimeZone('UTC'));

        $modified = $date->modify('this is not a valid date modifier');

        $this->assertSame($date, $modified);
    }

    #[Test]
    public function addHoursAndSubHours_modifyTheDate(): void
    {
        $date = new DatetimeHelper('2024-01-02 12:00:00', new DateTimeZone('UTC'));

        $this->assertSame('2024-01-02 14:00:00', $date->addHours(2)->toDateTimeString());
        $this->assertSame('2024-01-02 10:00:00', $date->subHours(2)->toDateTimeString());
    }

    #[Test]
    public function addDaysAndSubDays_modifyTheDate(): void
    {
        $date = new DatetimeHelper('2024-01-02 12:00:00', new DateTimeZone('UTC'));

        $this->assertSame('2024-01-04 12:00:00', $date->addDays(2)->toDateTimeString());
        $this->assertSame('2023-12-31 12:00:00', $date->subDays(2)->toDateTimeString());
    }

    #[Test]
    public function formatting_returnsExpectedStandardFormats(): void
    {
        $date = new DatetimeHelper('2024-01-02 03:04:05', new DateTimeZone('UTC'));

        $this->assertSame('2024-01-02T03:04:05+00:00', $date->toAtomString());
        $this->assertSame($date->toAtomString(), $date->toIso8601String());
        $this->assertSame('Tue, 02 Jan 24 03:04:05 +0000', $date->toRfc822String());
        $this->assertSame('2024-01-02T03:04:05+00:00', $date->toW3cString());
        $this->assertSame('2024-01-02', $date->toDateString());
        $this->assertSame('2024-01-02 03:04:05', $date->toDateTimeString());
    }

    #[Test]
    public function isoFormat_usesConfiguredPatternMap(): void
    {
        $date = new DatetimeHelper('2024-02-29 12:30:00', new DateTimeZone('UTC'));

        $this->assertSame('29/02/2024', $date->isoFormat('L'));
    }

    #[Test]
    public function isoFormat_usesInstanceTimezone(): void
    {
        $utcDate = new DatetimeHelper('2024-01-02 12:00:00', new DateTimeZone('UTC'));

        $parisDate = new DatetimeHelper('2024-01-02 13:00:00', new DateTimeZone('Europe/Paris'));

        $pattern = 'yyyy-MM-dd HH:mm';

        $this->assertSame('2024-01-02 12:00', $utcDate->isoFormat($pattern));
        $this->assertSame('2024-01-02 13:00', $parisDate->isoFormat($pattern));
    }

    #[Test]
    public function setLocale_usesTheSelectedLocale(): void
    {
        $date = new DatetimeHelper('2024-01-02 12:00:00', new DateTimeZone('UTC'));

        DatetimeHelper::setLocale('fr_FR');
        $french = $date->isoFormat('MMMM');

        DatetimeHelper::setLocale('en_US');
        $english = $date->isoFormat('MMMM');

        $this->assertNotSame($french, $english);
    }

    #[Test]
    public function isNowOrFuture_returnsExpectedResult(): void
    {
        $past = new DatetimeHelper('2000-01-01 00:00:00', new DateTimeZone('UTC'));

        $future = new DatetimeHelper('2099-01-01 00:00:00', new DateTimeZone('UTC'));

        $this->assertFalse($past->isNowOrFuture());
        $this->assertTrue($future->isNowOrFuture());
    }

    #[Test]
    public function secondsSinceMidnight_returnsExpectedSeconds(): void
    {
        $date = new DatetimeHelper('2024-01-02 03:04:05', new DateTimeZone('UTC'));

        $this->assertSame(11045, $date->secondsSinceMidnight());
    }

    #[Test]
    public function ago_formatsPastAndFutureDates(): void
    {
        $past = new DatetimeHelper('2024-01-02 10:00:00', new DateTimeZone('UTC'));

        $target = new DateTimeImmutable('2024-01-02 12:00:00', new DateTimeZone('UTC'));

        $this->assertSame('il y a 2 heures', $past->ago($target));

        $future = new DatetimeHelper('2024-01-02 12:00:00', new DateTimeZone('UTC'));

        $this->assertSame(
            'dans 2 heures',
            $future->ago(
                new DateTimeImmutable('2024-01-02 10:00:00', new DateTimeZone('UTC'))
            )
        );
    }

    #[Test]
    public function ago_supportsMultipleParts(): void
    {
        $date = new DatetimeHelper('2024-01-02 10:00:00', new DateTimeZone('UTC'));

        $target = new DateTimeImmutable('2024-01-02 12:15:30', new DateTimeZone('UTC'));

        $this->assertSame('il y a 2 heures 15 minutes', $date->ago($target, 2));
    }

    #[Test]
    public function ago_absoluteOmitsPastAndFuturePrefix(): void
    {
        $date = new DatetimeHelper('2024-01-02 10:00:00', new DateTimeZone('UTC'));

        $target = new DateTimeImmutable('2024-01-02 12:00:00', new DateTimeZone('UTC'));

        $this->assertSame('2 heures', $date->ago($target, 1, true));
    }

    #[Test]
    public function ago_returnsNowForDifferencesBelowFiveSeconds(): void
    {
        $date = new DatetimeHelper('2024-01-02 10:00:00', new DateTimeZone('UTC'));

        $target = new DateTimeImmutable('2024-01-02 10:00:04', new DateTimeZone('UTC'));

        $this->assertSame("à l'instant", $date->ago($target));
    }

    #[Test]
    public function ago_returnsZeroSecondsForEqualDatesWhenAbsolute(): void
    {
        $date = new DatetimeHelper('2024-01-02 10:00:00', new DateTimeZone('UTC'));

        $this->assertSame('0 secondes', $date->ago($date, 1, true));
    }

    #[Test]
    public function elapsedTime_formatsShortAndLongUnits(): void
    {
        $this->assertSame('1 min 5 s', DatetimeHelper::elapsedTime(65.5));

        $this->assertSame('1 minute 5 secondes', DatetimeHelper::elapsedTime(65.5, 2, false));
    }

    #[Test]
    public function elapsedTime_returnsConfiguredZeroText(): void
    {
        $this->assertSame('0 ms', DatetimeHelper::elapsedTime(0));

        $this->assertSame('0 milliseconde', DatetimeHelper::elapsedTime(0, 2, false));
    }

    #[Test]
    public function elapsedTime_normalizesNonPositiveParts(): void
    {
        $this->assertSame('1 min', DatetimeHelper::elapsedTime(65, 0));

        $this->assertSame('1 min', DatetimeHelper::elapsedTime(65, -1));
    }

    #[Test]
    public function elapsedTime_returnsZeroTextForNegativeDuration(): void
    {
        $this->assertSame('0 ms', DatetimeHelper::elapsedTime(-1));
    }

    #[Test]
    public function secondsFromIsoDuration_convertsFixedUnits(): void
    {
        $this->assertSame(9015, DatetimeHelper::secondsFromIsoDuration('PT2H30M15S'));

        $this->assertSame(93600, DatetimeHelper::secondsFromIsoDuration('P1DT2H'));
    }

    #[Test]
    public function secondsFromIsoDuration_rejectsCalendarUnits(): void
    {
        $this->assertSame(0, DatetimeHelper::secondsFromIsoDuration('P1M'));

        $this->assertSame(0, DatetimeHelper::secondsFromIsoDuration('P1Y'));
    }

    #[Test]
    public function secondsFromIsoDuration_returnsZeroForInvalidInput(): void
    {
        $this->assertSame(0, DatetimeHelper::secondsFromIsoDuration(''));

        $this->assertSame(0, DatetimeHelper::secondsFromIsoDuration('not-a-duration'));
    }

    #[Test]
    public function ago_usesIcuArabicPluralRules(): void
    {
        DatetimeHelper::setLocale('ar');

        $date = new DatetimeHelper('2024-01-02 12:00:00', new DateTimeZone('UTC'));

        $cases = [
            [0, '0 ثانية'],
            [1, 'ثانية واحدة'],
            [2, 'ثانيتين'],
            [3, '3 ثوانٍ'],
            [11, '11 ثانية'],
        ];

        foreach ($cases as [$seconds, $expected]) {
            $target = $date->modify("+{$seconds} seconds");

            $this->assertSame($expected, $date->ago($target, 1, true));
        }

        $this->assertSame('3 ثوانٍ', DatetimeHelper::elapsedTime(3, 1, false));

        $language = require dirname(__DIR__, 3) . '/src/Datetime/Lang/ar.php';

        $pattern = $language['agoTexts']['units']['second'];

        $this->assertSame(
            '321 ثانية',
            MessageFormatter::formatMessage(
                'ar',
                $pattern,
                ['count' => 321, 'number' => '321']
            )
        );
    }

    #[Test]
    public function elapsedTime_fallsBackToFrenchForUnknownLocale(): void
    {
        DatetimeHelper::setLocale('br');

        $this->assertSame('1 minute', DatetimeHelper::elapsedTime(60, 1, false));
    }

    #[Test]
    public function addMonths_itAddsMonthsWithoutModifyingOriginal(): void
    {
        $date = new DatetimeHelper('2026-01-15 12:34:56.123456');

        $result = $date->addMonths(2);

        self::assertSame(
            ['2026-03-15 12:34:56.123456', '2026-01-15 12:34:56.123456'],
            [$result->format('Y-m-d H:i:s.u'), $date->format('Y-m-d H:i:s.u')]
        );

        self::assertNotSame($date, $result);
    }

    #[Test]
    public function subMonths_itSubtractsMonthsAcrossYearBoundary(): void
    {
        $date = new DatetimeHelper('2026-01-15 12:34:56.123456');

        $result = $date->subMonths();

        self::assertSame('2025-12-15 12:34:56.123456', $result->format('Y-m-d H:i:s.u'));
    }

    #[Test]
    public function startOfMonth_itReturnsFirstDayAtMidnight(): void
    {
        $date = new DatetimeHelper('2026-02-18 13:14:15.123456');

        $result = $date->startOfMonth();

        self::assertSame(
            ['2026-02-01 00:00:00.000000', '2026-02-18 13:14:15.123456'],
            [$result->format('Y-m-d H:i:s.u'), $date->format('Y-m-d H:i:s.u')]
        );
    }

    #[Test]
    public function endOfMonth_itReturnsLastDayAtLastMicrosecond(): void
    {
        $date = new DatetimeHelper('2024-02-12 13:14:15.123456');

        $result = $date->endOfMonth();

        self::assertSame('2024-02-29 23:59:59.999999', $result->format('Y-m-d H:i:s.u'));
    }

    #[Test]
    public function startOfDay_itResetsTimeAndMicroseconds(): void
    {
        $date = new DatetimeHelper('2026-10-22 14:15:16.123456');

        $result = $date->startOfDay();

        self::assertSame('2026-10-22 00:00:00.000000', $result->format('Y-m-d H:i:s.u'));
    }

    #[Test]
    public function endOfDay_itSetsLastMicrosecondOfDay(): void
    {
        $date = new DatetimeHelper('2026-10-22 14:15:16.123456');

        $result = $date->endOfDay();

        self::assertSame('2026-10-22 23:59:59.999999', $result->format('Y-m-d H:i:s.u'));
    }

    #[Test]
    public function startOfYear_itReturnsFirstDayAtMidnight(): void
    {
        $date = new DatetimeHelper('2024-02-29 13:14:15.123456');

        $result = $date->startOfYear();

        self::assertSame('2024-01-01 00:00:00.000000', $result->format('Y-m-d H:i:s.u'));
    }

    #[Test]
    public function endOfYear_itReturnsLastMicrosecondOfYear(): void
    {
        $date = new DatetimeHelper('2024-02-29 13:14:15.123456');

        $result = $date->endOfYear();

        self::assertSame('2024-12-31 23:59:59.999999', $result->format('Y-m-d H:i:s.u'));
    }

    #[Test]
    public function subMonths_itCalculatesPreviousCalendarMonthFromMonthStart(): void
    {
        $date = new DatetimeHelper('2026-03-31 14:15:16.123456');

        $previousMonth = $date->startOfMonth()->subMonths();

        self::assertSame(
            ['2026-02-01 00:00:00.000000', '2026-03-31 14:15:16.123456'],
            [$previousMonth->format('Y-m-d H:i:s.u'), $date->format('Y-m-d H:i:s.u')]
        );
    }
}
