<?php

declare(strict_types=1);

namespace Moudarir\Helpers;

final class StringHelper
{

    public static function firstLetter(string $word, string $format = 'upper'): string
    {
        if ($word === '') {
            return '';
        }

        $letter = mb_substr($word, 0, 1);

        return match ($format) {
            'lower' => mb_strtolower($letter),
            default => mb_strtoupper($letter),
        };
    }

    public static function firstLetters(string $string, string $separator = '.', string $format = 'upper'): string
    {
        $words = preg_split('/\s+/', trim($string), -1, PREG_SPLIT_NO_EMPTY);

        if ($words === false) {
            return '';
        }

        $letters = [];

        foreach ($words as $word) {
            $letters[] = self::firstLetter($word, $format);
        }

        return implode($separator, $letters);
    }

    public static function toCamelcase(string $string, string $separator = '_'): string
    {
        if ($string === '') {
            return '';
        }

        return lcfirst(
            str_replace(
                ' ',
                '',
                ucwords(str_replace($separator, ' ', $string))
            )
        );
    }

    public static function bytesToHuman(int $bytes, bool $binary = true): string
    {
        if ($bytes === 0) {
            return '0 B';
        }

        $base = $binary === true ? 1024 : 1000;
        $units = $binary === true
            ? ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB']
            : ['B', 'kB', 'MB', 'GB', 'TB', 'PB'];

        $isNegative = $bytes < 0;
        $bytes = abs($bytes);
        $unit = 0;

        while ($bytes >= $base && $unit < count($units) - 1) {
            $bytes /= $base;
            $unit++;
        }

        $value = round($bytes, 2);
        $formattedValue = number_format($value, 2, '.', '');
        $formattedValue = rtrim(rtrim($formattedValue, '0'), '.');

        return ($isNegative ? '-' : '') . $formattedValue . ' ' . $units[$unit];
    }

    public static function excerpt(
        string $string,
        int    $maxLength = 160,
        string $ellipsisPosition = 'right',
        string $ellipsis = '…'
    ): string
    {
        if ($string === '') {
            return '';
        }

        if (preg_match('/<p\b[^>]*>(.*?)<\/p>/is', $string, $matches) === 1) {
            $string = $matches[1];
        }

        $string = trim(strip_tags($string));

        if ($string === '' || $maxLength <= 0) {
            return '';
        }

        if (mb_strlen($string) <= $maxLength) {
            return $string;
        }

        $ellipsisPosition = match ($ellipsisPosition) {
            'left', 'right' => $ellipsisPosition,
            default => 'right',
        };

        $ellipsisLength = mb_strlen($ellipsis);

        if ($ellipsisLength >= $maxLength) {
            return mb_substr($ellipsis, 0, $maxLength);
        }

        $contentLength = $maxLength - $ellipsisLength;

        if ($ellipsisPosition === 'left') {
            return $ellipsis . mb_substr($string, -$contentLength);
        }

        return mb_substr($string, 0, $contentLength) . $ellipsis;
    }
}
