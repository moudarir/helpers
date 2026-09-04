<?php

declare(strict_types=1);

namespace Moudarir\Helpers;

use Moudarir\Helpers\Enums\EnumProtocol;

final class IsHelper
{

    /**
     * @param list<EnumProtocol> $protocols
     */
    public static function validUrl(string $value, array $protocols = []): bool
    {
        if ($value === '') {
            return false;
        }

        $allowedProtocols = array_values(
            array_filter(
                $protocols,
                static fn (mixed $protocol): bool => $protocol instanceof EnumProtocol
            )
        );

        if ($allowedProtocols === []) {
            $allowedProtocols = EnumProtocol::cases();
        }

        $scheme = parse_url($value, PHP_URL_SCHEME);

        if (is_string($scheme) === false) {
            return false;
        }

        $scheme = strtolower($scheme);

        if (array_any($allowedProtocols, static fn ($protocol): bool => $protocol->value === $scheme)) {
            return filter_var($value, FILTER_VALIDATE_URL) !== false;
        }

        return false;
    }

    public static function validIP(?string $ip = null, string $which = ''): bool
    {
        if ($ip === null || trim($ip) === '') {
            return false;
        }

        $which = match (strtolower($which)) {
            'ipv4' => FILTER_FLAG_IPV4,
            'ipv6' => FILTER_FLAG_IPV6,
            default => 0,
        };

        return (bool)filter_var($ip, FILTER_VALIDATE_IP, $which);
    }

    public static function validEmail(string $str): bool
    {
        if (function_exists('idn_to_ascii') && preg_match('#\A([^@]+)@(.+)\z#', $str, $matches)) {
            if (($domain = idn_to_ascii($matches[2])) !== false) {
                $str = $matches[1] . '@' . $domain;
            }
        }

        return (bool)filter_var($str, FILTER_VALIDATE_EMAIL);
    }

    public static function validMac(string $mac): bool
    {
        return (bool)filter_var($mac, FILTER_VALIDATE_MAC);
    }

    public static function validBase64(string $str): bool
    {
        return (base64_encode(base64_decode($str)) === $str);
    }

    public static function natural(string $str): bool
    {
        return ctype_digit($str);
    }

    public static function naturalNoZero(string $str): bool
    {
        return ($str != 0 && ctype_digit($str));
    }

    public static function alpha(string $str): bool
    {
        return ctype_alpha($str);
    }

    public static function alphaNumeric(string $str): bool
    {
        return ctype_alnum($str);
    }

    public static function alphaNumericSpaces(string $str): bool
    {
        return (bool)preg_match('/^[A-Z0-9 ]+$/i', $str);
    }

    public static function alphaDash(string $str): bool
    {
        return (bool)preg_match('/^[a-z0-9_\-]+$/i', $str);
    }

    public static function alnumDash(string $str): bool
    {
        return (bool)preg_match('/^[a-z0-9\-]+$/i', $str);
    }

    public static function alnumUnderscore(string $str): bool
    {
        return (bool)preg_match('/^[a-z0-9_]+$/i', $str);
    }

    public static function numeric(string $str): bool
    {
        return (bool)preg_match('/^[\-+]?[0-9]*\.?[0-9]+$/', $str);
    }

    public static function integer(string $str): bool
    {
        return (bool)preg_match('/^[\-+]?[0-9]+$/', $str);
    }

    public static function decimal(string $str): bool
    {
        return (bool)preg_match('/^[\-+]?[0-9]+\.[0-9]+$/', $str);
    }

    public static function hex(string $content): bool
    {
        if (str_starts_with(strtolower($content), '0x')) {
            $content = substr($content, 2);
        }

        return ctype_xdigit($content);
    }

    public static function containsArabic(string $string): bool
    {
        return preg_match('/\p{Arabic}/u', $string) > 0;
    }

    public static function onlyArabic(string $string): bool
    {
        return preg_match('/(?!\p{Arabic})\p{L}/u', $string) !== 1;
    }
}
