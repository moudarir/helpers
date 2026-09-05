<?php

declare(strict_types=1);

namespace Moudarir\Helpers;

final class SanitizeHelper
{

    private const array REPLACEMENT = [
        '%' => '-pourcent', '&' => 'et',
        '+ de' => 'plus-de', '+' => '-plus',
        '₂' => '2', '²' => '2', '³' => '3',
    ];

    private const array CONVERT_CHARS = [
        // Convert &nbsp, non-breaking hyphen, &ndash, and &mdash to hyphens.
        '%c2%a0', '%e2%80%91', '%e2%80%93', '%e2%80%94',
        // Convert &nbsp, non-breaking hyphen, &ndash, and &mdash HTML entities to hyphens.
        '&nbsp;', '&#8209;', '&#160;', '&ndash;', '&#8211;', '&mdash;', '&#8212;',
        // Convert non-visible characters that display with a width to hyphen.
        '%e2%80%80', '%e2%80%81', '%e2%80%82', '%e2%80%83', '%e2%80%84', '%e2%80%85', '%e2%80%86', '%e2%80%87', '%e2%80%88', '%e2%80%89', '%e2%80%8a', '%e2%80%a8', '%e2%80%a9', '%e2%80%af',
        // Convert forward slash to hyphen.
        '/'
    ];

    private const array STRIP_ENTIRELY = [
        // Soft hyphens.
        '%c2%ad',
        // &iexcl and &iquest.
        '%c2%a1', '%c2%bf',
        // Angle quotes.
        '%c2%ab', '%c2%bb', '%e2%80%b9', '%e2%80%ba',
        // Curly quotes.
        '%e2%80%98', '%e2%80%99', '%e2%80%9c', '%e2%80%9d', '%e2%80%9a', '%e2%80%9b', '%e2%80%9e', '%e2%80%9f',
        // Bullet.
        '%e2%80%a2',
        // &copy, &reg, &deg, &hellip, and &trade.
        '%c2%a9', '%c2%ae', '%c2%b0', '%e2%80%a6', '%e2%84%a2',
        // Acute accents.
        '%c2%b4', '%cb%8a', '%cc%81', '%cd%81',
        // Grave accent, macron, caron.
        '%cc%80', '%cc%84', '%cc%8c',
        // Non-visible characters that display without a width.
        '%e2%80%8b', '%e2%80%8c', '%e2%80%8d', '%e2%80%8e', '%e2%80%8f', '%e2%80%aa', '%e2%80%ab', '%e2%80%ac', '%e2%80%ad', '%e2%80%ae', '%ef%bb%bf', '%ef%bf%bc',
    ];

    public static function slugify(string $title, array $additionalReplacements = []): string
    {
        $title = strip_tags($title);
        $title = self::removeAccents($title);

        // Preserve escaped octets.
        $title = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $title);

        // Replace signs that are not part of an octet.
        $replacements = array_merge(self::REPLACEMENT, $additionalReplacements);
        $title = strtr($title, $replacements);

        // Restore octets.
        $title = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $title);

        $title = str_replace(['¤', '«', '»', '–', '…', '°', '؟', '?'], '', $title);
        $title = str_replace(['_', '’', '\'', '  '], '-', $title);

        $title = mb_strtolower($title, 'UTF-8');
        $title = self::utf8UriEncode($title, 200);

        $title = str_replace(self::CONVERT_CHARS, '-', $title);

        // Strip these characters entirely.
        $title = str_replace(self::STRIP_ENTIRELY, '', $title);

        // Convert &times to 'x'.
        $title = str_replace('%c3%97', 'x', $title);

        // Remove HTML entities.
        $title = preg_replace('/&.+?;/', '', $title);

        $title = str_replace('.', '-', $title);
        $title = preg_replace('/[^%a-z0-9 -]/', '', $title);
        $title = preg_replace('/\s+/', '-', $title);
        $title = preg_replace('|-+|', '-', $title);

        return trim($title, '-');
    }

    public static function urlTitle(string $title): string
    {
        $title = trim($title);

        if ($title === '') {
            return '';
        }

        $title = strip_tags($title);
        $trans = [
            '&.+?;' => '',
            '[^\w\d _-]' => '',
            '\s+' => '-',
            '(' . preg_quote('-', '#') . ')+' => '-'
        ];

        foreach ($trans as $key => $val) {
            $title = preg_replace('#' . $key . '#iu', $val, $title);
        }

        return trim($title, '-');
    }

    public static function removeAccents(string $text): string
    {
        if (!preg_match('/[\x80-\xff]/', $text)) {
            return $text;
        }

        if (($normalized = normalizer_normalize($text)) !== false) {
            $text = $normalized;
        }

        return strtr($text, [
            // Decompositions for Latin-1 Supplement.
            'ª' => 'a', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae',
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE',
            'Ç' => 'C', 'ç' => 'c',
            'ð' => 'd', 'Ð' => 'D',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
            'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E',
            'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'ñ' => 'n', 'Ñ' => 'N',
            'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'º' => 'o',
            'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O',
            'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
            'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U',
            'ß' => 'ss',
            'þ' => 'th', 'Þ' => 'TH',
            'ý' => 'y', 'ÿ' => 'y', 'Ý' => 'Y',
            // Decompositions for Latin Extended-A.
            'ā' => 'a', 'ă' => 'a', 'ą' => 'a',
            'Ă' => 'A', 'Ā' => 'A', 'Ą' => 'A',
            'Ć' => 'C', 'Ĉ' => 'C', 'Ċ' => 'C', 'Č' => 'C',
            'ć' => 'c', 'ĉ' => 'c', 'ċ' => 'c', 'č' => 'c',
            'Ď' => 'D', 'Đ' => 'D',
            'ď' => 'd', 'đ' => 'd',
            'Ē' => 'E', 'Ĕ' => 'E', 'Ė' => 'E', 'Ę' => 'E', 'Ě' => 'E',
            'ē' => 'e', 'ĕ' => 'e', 'ė' => 'e', 'ę' => 'e', 'ě' => 'e',
            'Ĝ' => 'G', 'Ğ' => 'G', 'Ġ' => 'G', 'Ģ' => 'G',
            'ĝ' => 'g', 'ğ' => 'g', 'ġ' => 'g', 'ģ' => 'g',
            'Ĥ' => 'H', 'Ħ' => 'H',
            'ĥ' => 'h', 'ħ' => 'h',
            'Ĩ' => 'I', 'Ī' => 'I', 'Ĭ' => 'I', 'Į' => 'I', 'İ' => 'I', 'Ĳ' => 'IJ',
            'ĩ' => 'i', 'ī' => 'i', 'ĭ' => 'i', 'į' => 'i', 'ı' => 'i', 'ĳ' => 'ij',
            'Ĵ' => 'J', 'ĵ' => 'j',
            'Ķ' => 'K', 'ķ' => 'k', 'ĸ' => 'k',
            'Ĺ' => 'L', 'Ļ' => 'L', 'Ľ' => 'L', 'Ŀ' => 'L', 'Ł' => 'L',
            'ĺ' => 'l', 'ļ' => 'l', 'ľ' => 'l', 'ŀ' => 'l', 'ł' => 'l',
            'Ń' => 'N', 'Ņ' => 'N', 'Ň' => 'N', 'Ŋ' => 'N',
            'ń' => 'n', 'ņ' => 'n', 'ň' => 'n', 'ŉ' => 'n', 'ŋ' => 'n',
            'Ō' => 'O', 'Ŏ' => 'O', 'Ő' => 'O', 'Œ' => 'OE',
            'ō' => 'o', 'ŏ' => 'o', 'ő' => 'o', 'œ' => 'oe',
            'Ŕ' => 'R', 'Ŗ' => 'R', 'Ř' => 'R',
            'ŕ' => 'r', 'ŗ' => 'r', 'ř' => 'r',
            'Ś' => 'S', 'Ŝ' => 'S', 'Ş' => 'S', 'Š' => 'S',
            'ś' => 's', 'ŝ' => 's', 'ş' => 's', 'š' => 's', 'ſ' => 's',
            'Ţ' => 'T', 'Ť' => 'T', 'Ŧ' => 'T',
            'ţ' => 't', 'ť' => 't', 'ŧ' => 't',
            'Ũ' => 'U', 'Ū' => 'U', 'Ŭ' => 'U', 'Ů' => 'U', 'Ű' => 'U', 'Ų' => 'U',
            'ũ' => 'u', 'ū' => 'u', 'ŭ' => 'u', 'ů' => 'u', 'ű' => 'u', 'ų' => 'u',
            'Ŵ' => 'W', 'ŵ' => 'w',
            'Ŷ' => 'Y',
            'ŷ' => 'y', 'Ÿ' => 'Y',
            'Ź' => 'Z', 'Ż' => 'Z', 'Ž' => 'Z',
            'ź' => 'z', 'ż' => 'z', 'ž' => 'z',
            // Decompositions for Latin Extended-B.
            'Ə' => 'E', 'ǝ' => 'e',
            'Ș' => 'S', 'ș' => 's',
            'Ț' => 'T', 'ț' => 't',
            // Euro sign.
            '€' => 'E',
            // GBP (Pound) sign.
            '£' => '',
            // Vowels with diacritic (Vietnamese). Unmarked.
            'Ơ' => 'O', 'ơ' => 'o',
            'Ư' => 'U', 'ư' => 'u',
            // Grave accent.
            'Ầ' => 'A', 'Ằ' => 'A', 'ầ' => 'a', 'ằ' => 'a',
            'Ề' => 'E', 'ề' => 'e',
            'Ồ' => 'O', 'Ờ' => 'O', 'ồ' => 'o', 'ờ' => 'o',
            'Ừ' => 'U', 'ừ' => 'u',
            'Ỳ' => 'Y', 'ỳ' => 'y',
            // Hook.
            'Ả' => 'A', 'Ẩ' => 'A', 'Ẳ' => 'A',
            'ả' => 'a', 'ẩ' => 'a', 'ẳ' => 'a',
            'Ẻ' => 'E', 'Ể' => 'E', 'ẻ' => 'e', 'ể' => 'e',
            'Ỉ' => 'I', 'ỉ' => 'i',
            'Ỏ' => 'O', 'Ổ' => 'O', 'Ở' => 'O',
            'ỏ' => 'o', 'ổ' => 'o', 'ở' => 'o',
            'Ủ' => 'U', 'Ử' => 'U', 'ủ' => 'u', 'ử' => 'u',
            'Ỷ' => 'Y', 'ỷ' => 'y',
            // Tilde.
            'Ẫ' => 'A', 'Ẵ' => 'A', 'ẫ' => 'a', 'ẵ' => 'a',
            'Ẽ' => 'E', 'Ễ' => 'E', 'ẽ' => 'e', 'ễ' => 'e',
            'Ỗ' => 'O', 'Ỡ' => 'O', 'ỗ' => 'o', 'ỡ' => 'o',
            'Ữ' => 'U', 'ữ' => 'u',
            'Ỹ' => 'Y', 'ỹ' => 'y',
            // Acute accent.
            'Ấ' => 'A', 'Ắ' => 'A', 'ấ' => 'a', 'ắ' => 'a',
            'Ế' => 'E', 'ế' => 'e',
            'Ố' => 'O', 'Ớ' => 'O', 'ố' => 'o', 'ớ' => 'o',
            'Ứ' => 'U', 'ứ' => 'u',
            // Dot below.
            'Ạ' => 'A', 'Ậ' => 'A', 'Ặ' => 'A',
            'ạ' => 'a', 'ậ' => 'a', 'ặ' => 'a',
            'Ẹ' => 'E', 'Ệ' => 'E', 'ẹ' => 'e', 'ệ' => 'e',
            'Ị' => 'I', 'ị' => 'i',
            'Ọ' => 'O', 'Ộ' => 'O', 'Ợ' => 'O',
            'ọ' => 'o', 'ộ' => 'o', 'ợ' => 'o',
            'Ụ' => 'U', 'Ự' => 'U', 'ụ' => 'u', 'ự' => 'u',
            'Ỵ' => 'Y', 'ỵ' => 'y',
            // Vowels with diacritic (Chinese, Hanyu Pinyin).
            'ɑ' => 'a',
            // Macron.
            'Ǖ' => 'U', 'ǖ' => 'u',
            // Acute accent.
            'Ǘ' => 'U', 'ǘ' => 'u',
            // Caron.
            'Ǎ' => 'A', 'ǎ' => 'a',
            'Ǐ' => 'I', 'ǐ' => 'i',
            'Ǒ' => 'O', 'ǒ' => 'o',
            'Ǔ' => 'U', 'Ǚ' => 'U',
            'ǔ' => 'u', 'ǚ' => 'u',
            // Grave accent.
            'Ǜ' => 'U', 'ǜ' => 'u',
        ]);
    }

    private static function utf8UriEncode(string $utf8String, int $length = 0): string
    {
        $unicode = '';
        $values = [];
        $numOctets = 1;
        $unicodeLength = 0;
        $stringLength = strlen($utf8String);

        for ($i = 0; $i < $stringLength; $i++) {

            $value = ord($utf8String[$i]);

            if ($value < 128) {
                if ($length && ($unicodeLength + 1) > $length) {
                    break;
                }

                $unicode .= chr($value);
                ++$unicodeLength;
            } else {
                if ([] === $values) {
                    if ($value < 224) {
                        $numOctets = 2;
                    } elseif ($value < 240) {
                        $numOctets = 3;
                    } else {
                        $numOctets = 4;
                    }
                }

                $values[] = $value;

                if ($length && ($unicodeLength + ($numOctets * 3)) > $length) {
                    break;
                }
                if (count($values) === $numOctets) {
                    for ($j = 0; $j < $numOctets; $j++) {
                        $unicode .= '%' . dechex($values[$j]);
                    }

                    $unicodeLength += $numOctets * 3;

                    $values = [];
                    $numOctets = 1;
                }
            }
        }

        return $unicode;
    }
}
