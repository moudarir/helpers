<?php

declare(strict_types=1);

namespace Moudarir\Helpers;

use JsonException;

final class JsonHelper
{

    public static function decode(string $string, bool $assoc = true): mixed
    {
        return json_validate($string) ? json_decode($string, $assoc) : null;
    }

    public static function jsonify(array $data, bool $withHeader = true): string
    {
        if ($withHeader === true) {
            header('Content-Type: application/json; charset=utf-8');
        }

        return self::encode(
            $data,
            JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT
        );
    }

    public static function encodeForLdFormat(array $schema): string
    {
        $json = self::encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        if ($json === '') {
            return '';
        }

        return <<<HTML
<script type="application/ld+json">
    $json
</script>
HTML;
    }

    /**
     * @param list<array<string, mixed>> $data
     * @throws JsonException
     */
    public static function encodeAsJsonL(array $data): string
    {
        return implode("\n", array_map(static fn (array $d) => json_encode($d, JSON_THROW_ON_ERROR), $data));
    }

    /**
     * @throws JsonException
     */
    public static function decodeFromJsonL(string $response): array
    {
        $lines = array_values(array_filter(
            preg_split('/\R/', trim($response)) ?: [],
            static fn (string $line): bool => trim($line) !== ''
        ));

        return array_map(
            static fn (string $line): array => json_decode(
                $line,
                true,
                512,
                JSON_THROW_ON_ERROR
            ),
            $lines
        );
    }

    private static function encode(array $data, int $flags): string
    {
        $json = json_encode($data, $flags);

        return $json === false ? '' : $json;
    }
}
