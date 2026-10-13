<?php

declare(strict_types=1);

return [
    'isoFormatMap' => [
        'L' => 'dd/MM/yyyy',
        'LL' => 'd MMMM yyyy',
        'LLL' => 'd MMMM yyyy HH:mm',
        'LLLL' => 'EEEE، d MMMM yyyy HH:mm',
        'l' => 'd/M/yyyy',
        'll' => 'd MMM yyyy',
        'lll' => 'd MMM yyyy HH:mm',
        'llll' => 'EEE، d MMM yyyy HH:mm',
    ],

    'unitsShort' => [
        86_400_000 => '{count, plural, other {{number} d}}',
        3_600_000 => '{count, plural, other {{number} h}}',
        60_000 => '{count, plural, other {{number} min}}',
        1_000 => '{count, plural, other {{number} s}}',
        1 => '{count, plural, other {{number} ms}}',
    ],

    'unitsLong' => [
        86_400_000 => '{count, plural, zero {0 يوم} one {يوم واحد} '
            . 'two {يومان} few {{number} أيام} '
            . 'many {{number} يومًا} other {{number} يوم}}',
        3_600_000 => '{count, plural, zero {0 ساعة} one {ساعة واحدة} '
            . 'two {ساعتان} few {{number} ساعات} '
            . 'many {{number} ساعة} other {{number} ساعة}}',
        60_000 => '{count, plural, zero {0 دقيقة} one {دقيقة واحدة} '
            . 'two {دقيقتان} few {{number} دقائق} '
            . 'many {{number} دقيقة} other {{number} دقيقة}}',
        1_000 => '{count, plural, zero {0 ثانية} one {ثانية واحدة} '
            . 'two {ثانيتان} few {{number} ثوانٍ} '
            . 'many {{number} ثانية} other {{number} ثانية}}',
        1 => '{count, plural, zero {0 ميلي ثانية} '
            . 'one {ميلي ثانية واحدة} two {ميلي ثانيتان} '
            . 'few {{number} ميلي ثوانٍ} '
            . 'many {{number} ميلي ثانية} '
            . 'other {{number} ميلي ثانية}}',
    ],

    'zeroText' => [
        'short' => '{count, plural, zero {0 ms} other {{number} ms}}',
        'long' => '{count, plural, zero {0 ميلي ثانية} '
            . 'one {ميلي ثانية واحدة} two {ميلي ثانيتان} '
            . 'few {{number} ميلي ثوانٍ} '
            . 'many {{number} ميلي ثانية} '
            . 'other {{number} ميلي ثانية}}',
    ],

    'agoTexts' => [
        'past' => 'منذ %s',
        'future' => 'بعد %s',
        'now' => 'الآن',
        'units' => [
            'year' => '{count, plural, zero {0 سنة} one {سنة واحدة} '
                . 'two {سنتين} few {{number} سنوات} '
                . 'many {{number} سنة} other {{number} سنة}}',
            'month' => '{count, plural, zero {0 شهر} one {شهر واحد} '
                . 'two {شهرين} few {{number} أشهر} '
                . 'many {{number} شهرًا} other {{number} شهر}}',
            'week' => '{count, plural, zero {0 أسبوع} one {أسبوع واحد} '
                . 'two {أسبوعين} few {{number} أسابيع} '
                . 'many {{number} أسبوعًا} other {{number} أسبوع}}',
            'day' => '{count, plural, zero {0 يوم} one {يوم واحد} '
                . 'two {يومين} few {{number} أيام} '
                . 'many {{number} يومًا} other {{number} يوم}}',
            'hour' => '{count, plural, zero {0 ساعة} one {ساعة واحدة} '
                . 'two {ساعتين} few {{number} ساعات} '
                . 'many {{number} ساعة} other {{number} ساعة}}',
            'minute' => '{count, plural, zero {0 دقيقة} one {دقيقة واحدة} '
                . 'two {دقيقتين} few {{number} دقائق} '
                . 'many {{number} دقيقة} other {{number} دقيقة}}',
            'second' => '{count, plural, zero {0 ثانية} one {ثانية واحدة} '
                . 'two {ثانيتين} few {{number} ثوانٍ} '
                . 'many {{number} ثانية} other {{number} ثانية}}',
        ],
    ],
];
