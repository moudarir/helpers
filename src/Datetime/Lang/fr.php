<?php

declare(strict_types=1);

return [
    'isoFormatMap' => [
        'L' => 'dd/MM/yyyy',
        'LL' => 'd MMMM yyyy',
        'LLL' => "d MMMM yyyy 'à' HH:mm",
        'LLLL' => "EEEE d MMMM yyyy 'à' HH:mm",
        'l' => 'd/M/yyyy',
        'll' => 'd MMM yyyy',
        'lll' => "d MMM yyyy 'à' HH:mm",
        'llll' => "EEE d MMM yyyy 'à' HH:mm",
    ],

    'unitsShort' => [
        86_400_000 => '{count, plural, other {# j}}',
        3_600_000 => '{count, plural, other {# h}}',
        60_000 => '{count, plural, other {# min}}',
        1_000 => '{count, plural, other {# s}}',
        1 => '{count, plural, other {# ms}}',
    ],

    'unitsLong' => [
        86_400_000 => '{count, plural, =0 {0 jour} one {# jour} ' . 'other {# jours}}',
        3_600_000 => '{count, plural, =0 {0 heure} one {# heure} ' . 'other {# heures}}',
        60_000 => '{count, plural, =0 {0 minute} one {# minute} ' . 'other {# minutes}}',
        1_000 => '{count, plural, =0 {0 seconde} one {# seconde} ' . 'other {# secondes}}',
        1 => '{count, plural, =0 {0 milliseconde} ' . 'one {# milliseconde} other {# millisecondes}}',
    ],

    'zeroText' => [
        'short' => '{count, plural, =0 {0 ms} other {# ms}}',
        'long' => '{count, plural, =0 {0 milliseconde} ' . 'one {# milliseconde} other {# millisecondes}}',
    ],

    'agoTexts' => [
        'past' => 'il y a %s',
        'future' => 'dans %s',
        'now' => "à l'instant",
        'units' => [
            'year' => '{count, plural, =0 {0 an} one {# an} other {# ans}}',
            'month' => '{count, plural, =0 {0 mois} other {# mois}}',
            'week' => '{count, plural, =0 {0 semaine} one {# semaine} ' . 'other {# semaines}}',
            'day' => '{count, plural, =0 {0 jour} one {# jour} ' . 'other {# jours}}',
            'hour' => '{count, plural, =0 {0 heure} one {# heure} ' . 'other {# heures}}',
            'minute' => '{count, plural, =0 {0 minute} one {# minute} ' . 'other {# minutes}}',
            'second' => '{count, plural, =0 {0 secondes} one {# seconde} ' . 'other {# secondes}}',
        ],
    ],
];
