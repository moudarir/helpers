<?php

namespace Moudarir\Helpers\Datetime;

class DatetimeConfig
{

    public const string TIMEZONE = "UTC";

    public const string LOCALE = "fr_FR";

    public const array ISO_FORMAT_MAP = [
        'L'    => 'dd/MM/yyyy',
        'LL'   => 'd MMMM yyyy',
        'LLL'  => 'd MMMM yyyy HH:mm',
        'LLLL' => 'EEEE d MMMM yyyy HH:mm',
        'l'    => 'd/M/yyyy',
        'll'   => 'd MMM yyyy',
        'lll'  => 'd MMM yyyy HH:mm',
        'llll' => 'EEE d MMM yyyy HH:mm',
    ];

    public const array SHORT_UNITS = [
        86_400_000 => ['j', 'j'],
        3_600_000  => ['h', 'h'],
        60_000     => ['min', 'min'],
        1_000      => ['s', 's'],
        1          => ['ms', 'ms'],
    ];

    public const array LONG_UNITS = [
        86_400_000 => ['jour', 'jours'],
        3_600_000  => ['heure', 'heures'],
        60_000     => ['minute', 'minutes'],
        1_000      => ['seconde', 'secondes'],
        1          => ['milliseconde', 'millisecondes'],
    ];

    public const array ZERO_TEXTS = [
        'short' => '0 ms',
        'long'  => '0 milliseconde',
    ];

    public const array AGO_TEXTS = [
        'past'   => 'il y a %s',
        'future' => 'dans %s',
        'now'    => "à l'instant",
        'units'  => [
            'year'   => ['an', 'ans'],
            'month'  => ['mois', 'mois'],
            'week'   => ['semaine', 'semaines'],
            'day'    => ['jour', 'jours'],
            'hour'   => ['heure', 'heures'],
            'minute' => ['minute', 'minutes'],
            'second' => ['seconde', 'secondes'],
        ],
    ];
}
