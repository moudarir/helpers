<?php

declare(strict_types=1);

namespace Moudarir\Helpers\Datetime;

class DatetimeConfig
{

    public const string TIMEZONE = "UTC";

    public const string LOCALE = "fr_FR";

    public const array ISO_FORMAT_MAP = [
        'L' => 'dd/MM/yyyy',
        'LL' => 'd MMMM yyyy',
        'LLL' => 'd MMMM yyyy HH:mm',
        'LLLL' => 'EEEE d MMMM yyyy HH:mm',
        'l' => 'd/M/yyyy',
        'll' => 'd MMM yyyy',
        'lll' => 'd MMM yyyy HH:mm',
        'llll' => 'EEE d MMM yyyy HH:mm',
    ];

    public const array SHORT_UNITS = [
        86_400_000 => '{count, plural, other {# j}}',
        3_600_000 => '{count, plural, other {# h}}',
        60_000 => '{count, plural, other {# min}}',
        1_000 => '{count, plural, other {# s}}',
        1 => '{count, plural, other {# ms}}',
    ];

    public const array LONG_UNITS = [
        86_400_000 => '{count, plural, one {# jour} other {# jours}}',
        3_600_000 => '{count, plural, one {# heure} other {# heures}}',
        60_000 => '{count, plural, one {# minute} other {# minutes}}',
        1_000 => '{count, plural, one {# seconde} other {# secondes}}',
        1 => '{count, plural, =0 {0 milliseconde} ' . 'one {# milliseconde} other {# millisecondes}}',
    ];

    public const array ZERO_TEXTS = [
        'short' => '{count, plural, =0 {0 ms} other {# ms}}',
        'long' => '{count, plural, =0 {0 milliseconde} ' . 'one {# milliseconde} other {# millisecondes}}',
    ];

    public const array AGO_TEXTS = [
        'past' => 'il y a %s',
        'future' => 'dans %s',
        'now' => "à l'instant",
        'units' => [
            'year' => '{count, plural, one {# an} other {# ans}}',
            'month' => '{count, plural, other {# mois}}',
            'week' => '{count, plural, one {# semaine} ' . 'other {# semaines}}',
            'day' => '{count, plural, one {# jour} other {# jours}}',
            'hour' => '{count, plural, one {# heure} other {# heures}}',
            'minute' => '{count, plural, one {# minute} other {# minutes}}',
            'second' => '{count, plural, =0 {# secondes} ' . 'one {# seconde} other {# secondes}}',
        ],
    ];
}
