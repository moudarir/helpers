<?php

declare(strict_types=1);

return [
    'isoFormatMap' => [
        'L' => 'MM/dd/yyyy',
        'LL' => 'MMMM d, yyyy',
        'LLL' => "MMMM d, yyyy 'at' h:mm a",
        'LLLL' => "EEEE, MMMM d, yyyy 'at' h:mm a",
        'l' => 'M/d/yyyy',
        'll' => 'MMM d, yyyy',
        'lll' => 'MMM d, yyyy, h:mm a',
        'llll' => 'EEE, MMM d, yyyy, h:mm a',
    ],

    'unitsShort' => [
        86_400_000 => '{count, plural, other {# d}}',
        3_600_000 => '{count, plural, other {# h}}',
        60_000 => '{count, plural, other {# min}}',
        1_000 => '{count, plural, other {# s}}',
        1 => '{count, plural, other {# ms}}',
    ],

    'unitsLong' => [
        86_400_000 => '{count, plural, =0 {0 days} one {# day} other {# days}}',
        3_600_000 => '{count, plural, =0 {0 hours} one {# hour} other {# hours}}',
        60_000 => '{count, plural, =0 {0 minutes} one {# minute} ' . 'other {# minutes}}',
        1_000 => '{count, plural, =0 {0 seconds} one {# second} ' . 'other {# seconds}}',
        1 => '{count, plural, =0 {0 milliseconds} ' . 'one {# millisecond} other {# milliseconds}}',
    ],

    'zeroText' => [
        'short' => '{count, plural, =0 {0 ms} other {# ms}}',
        'long' => '{count, plural, =0 {0 milliseconds} ' . 'one {# millisecond} other {# milliseconds}}',
    ],

    'agoTexts' => [
        'past' => '%s ago',
        'future' => 'in %s',
        'now' => 'just now',
        'units' => [
            'year' => '{count, plural, =0 {0 years} one {# year} other {# years}}',
            'month' => '{count, plural, =0 {0 months} one {# month} ' . 'other {# months}}',
            'week' => '{count, plural, =0 {0 weeks} one {# week} other {# weeks}}',
            'day' => '{count, plural, =0 {0 days} one {# day} other {# days}}',
            'hour' => '{count, plural, =0 {0 hours} one {# hour} other {# hours}}',
            'minute' => '{count, plural, =0 {0 minutes} one {# minute} ' . 'other {# minutes}}',
            'second' => '{count, plural, =0 {0 seconds} one {# second} ' . 'other {# seconds}}',
        ],
    ],
];
