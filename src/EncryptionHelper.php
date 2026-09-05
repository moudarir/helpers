<?php

declare(strict_types=1);

namespace Moudarir\Helpers;

use Random\RandomException;
use SodiumException;

final class EncryptionHelper
{

    public static function binaryBytes(int $length): ?string
    {
        if ($length <= 0) {
            return null;
        }

        try {
            return random_bytes($length);
        } catch (RandomException) {
            return null;
        }
    }

    /**
     * @throws RandomException
     */
    public static function generateToken(int $length = 9, string $type = 'alnum', int $parts = 1): string
    {
        if ($length <= 0 || $parts <= 0) {
            return '';
        }

        $chars = [];
        $pool = match ($type) {
            'alpha' => 'abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ',
            'alnum' => '123456789abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ',
            'upper' => '123456789ABCDEFGHJKLMNPQRSTUVWXYZ',
            'numeric' => '0123456789',
            'nozero' => '123456789',
            default => '123456789abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ@!#$*-_',
        };

        for ($i = 0; $i < $parts; ++$i) {
            $token = '';

            for ($j = 0; $j < $length; ++$j) {
                $token .= $pool[random_int(0, (strlen($pool) - 1))];
            }

            $chars[] = $token;
        }

        return implode('-', $chars);
    }

    /**
     * @throws RandomException|SodiumException
     */
    public static function encrypt(string $data, string $key): string
    {
        $nonce = self::binaryBytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $ciphertext = sodium_crypto_secretbox(
            $data,
            $nonce,
            hash('sha256', $key, true)
        );

        // Store nonce + ciphertext
        return base64_encode($nonce . $ciphertext);
    }

    /**
     * @throws SodiumException
     */
    public static function decrypt(string $data, string $key): false|string
    {
        $decoded = base64_decode($data, true);

        if ($decoded === false || strlen($decoded) < SODIUM_CRYPTO_SECRETBOX_NONCEBYTES) {
            return false;
        }

        $nonce = substr($decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $ciphertext = substr($decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $plaintext = sodium_crypto_secretbox_open(
            $ciphertext,
            $nonce,
            hash('sha256', $key, true)
        );

        return $plaintext === false ? false : $plaintext;
    }
}
