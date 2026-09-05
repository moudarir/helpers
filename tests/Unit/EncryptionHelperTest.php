<?php

declare(strict_types=1);

namespace Moudarir\Helpers\Tests\Unit;

use Moudarir\Helpers\EncryptionHelper;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class EncryptionHelperTest extends TestCase
{

    #[Test]
    public function binaryBytes_returnsRequestedNumberOfBytes(): void
    {
        $bytes = EncryptionHelper::binaryBytes(16);

        self::assertIsString($bytes);
        self::assertSame(16, strlen($bytes));
    }

    #[Test]
    public function binaryBytes_returnsNullForZeroLength(): void
    {
        self::assertNull(EncryptionHelper::binaryBytes(0));
    }

    #[Test]
    public function binaryBytes_returnsNullForNegativeLength(): void
    {
        self::assertNull(EncryptionHelper::binaryBytes(-1));
    }

    #[Test]
    public function generateToken_returnsTokenWithRequestedLength(): void
    {
        $token = EncryptionHelper::generateToken(16);

        self::assertSame(16, strlen($token));
    }

    #[Test]
    public function generateToken_returnsRequestedNumberOfParts(): void
    {
        $token = EncryptionHelper::generateToken(8, 'alnum', 3);

        self::assertCount(3, explode('-', $token));
    }

    #[Test]
    public function generateToken_generatesPartsWithRequestedLength(): void
    {
        $token = EncryptionHelper::generateToken(8, 'alnum', 3);

        foreach (explode('-', $token) as $part) {
            self::assertSame(8, strlen($part));
        }
    }

    #[Test]
    public function generateToken_usesAlphaCharacters(): void
    {
        $token = EncryptionHelper::generateToken(100, 'alpha');

        self::assertMatchesRegularExpression('/^[a-zA-Z]+$/', $token);
    }

    #[Test]
    public function generateToken_usesAlphanumericCharacters(): void
    {
        $token = EncryptionHelper::generateToken(100, 'alnum');

        self::assertMatchesRegularExpression('/^[a-zA-Z0-9]+$/', $token);
    }

    #[Test]
    public function generateToken_usesUppercaseCharactersAndNumbers(): void
    {
        $token = EncryptionHelper::generateToken(100, 'upper');

        self::assertMatchesRegularExpression('/^[A-Z0-9]+$/', $token);
    }

    #[Test]
    public function generateToken_usesNumericCharacters(): void
    {
        $token = EncryptionHelper::generateToken(100, 'numeric');

        self::assertMatchesRegularExpression('/^[0-9]+$/', $token);
    }

    #[Test]
    public function generateToken_usesNonZeroNumericCharacters(): void
    {
        $token = EncryptionHelper::generateToken(100, 'nozero');

        self::assertMatchesRegularExpression('/^[1-9]+$/', $token);
    }

    #[Test]
    public function generateToken_usesDefaultCharacterPoolForUnknownType(): void
    {
        $token = EncryptionHelper::generateToken(100, 'unknown');

        self::assertMatchesRegularExpression(
            '/^[123456789abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ@!#$*_-]+$/',
            $token
        );
    }

    #[Test]
    public function generateToken_returnsEmptyStringForZeroParts(): void
    {
        self::assertSame('', EncryptionHelper::generateToken(9, 'alnum', 0));
    }

    #[Test]
    public function generateToken_returnsEmptyStringWhenLengthIsZero(): void
    {
        self::assertSame('', EncryptionHelper::generateToken(0, 'alnum', 2));
    }

    #[Test]
    public function generateToken_returnsEmptyStringForNegativeLength(): void
    {
        self::assertSame('', EncryptionHelper::generateToken(-1));
    }

    #[Test]
    public function generateToken_returnsEmptyStringForNegativeParts(): void
    {
        self::assertSame('', EncryptionHelper::generateToken(9, 'alnum', -1));
    }

    #[Test]
    public function encrypt_returnsBase64String(): void
    {
        $encrypted = EncryptionHelper::encrypt('Hello World', 'secret');

        self::assertIsString($encrypted);
        self::assertNotSame('', $encrypted);
        self::assertNotFalse(base64_decode($encrypted, true));
    }

    #[Test]
    public function encrypt_canEncryptEmptyData(): void
    {
        $encrypted = EncryptionHelper::encrypt('', 'secret');

        self::assertIsString($encrypted);
        self::assertNotSame('', $encrypted);
    }

    #[Test]
    public function encrypt_canEncryptLongData(): void
    {
        $data = str_repeat('A', 10000);

        $encrypted = EncryptionHelper::encrypt($data, 'secret');

        self::assertIsString($encrypted);
        self::assertNotSame('', $encrypted);
    }

    #[Test]
    public function encrypt_canBeDecrypted(): void
    {
        $data = 'Hello World';
        $key = 'secret';

        $encrypted = EncryptionHelper::encrypt($data, $key);
        $decrypted = EncryptionHelper::decrypt($encrypted, $key);

        self::assertSame($data, $decrypted);
    }

    #[Test]
    public function encrypt_cannotBeDecryptedWithWrongKey(): void
    {
        $encrypted = EncryptionHelper::encrypt('Hello World', 'secret');

        self::assertFalse(EncryptionHelper::decrypt($encrypted, 'wrong-secret'));
    }

    #[Test]
    public function decrypt_returnsOriginalData(): void
    {
        $data = 'Hello World';
        $key = 'secret';
        $encrypted = EncryptionHelper::encrypt($data, $key);

        self::assertSame($data, EncryptionHelper::decrypt($encrypted, $key));
    }

    #[Test]
    public function decrypt_canDecryptEmptyData(): void
    {
        $key = 'secret';
        $encrypted = EncryptionHelper::encrypt('', $key);

        self::assertSame('', EncryptionHelper::decrypt($encrypted, $key));
    }

    #[Test]
    public function decrypt_returnsFalseForInvalidBase64(): void
    {
        self::assertFalse(EncryptionHelper::decrypt('not-base64!', 'secret'));
    }

    #[Test]
    public function decrypt_returnsFalseForInsufficientData(): void
    {
        $data = base64_encode('short');

        self::assertFalse(EncryptionHelper::decrypt($data, 'secret'));
    }

    #[Test]
    public function decrypt_returnsFalseWithWrongKey(): void
    {
        $encrypted = EncryptionHelper::encrypt('Hello World', 'secret');

        self::assertFalse(EncryptionHelper::decrypt($encrypted, 'wrong-key'));
    }

    #[Test]
    public function decrypt_returnsFalseForModifiedCiphertext(): void
    {
        $encrypted = EncryptionHelper::encrypt('Hello World', 'secret');
        $decoded = base64_decode($encrypted, true);

        self::assertNotFalse($decoded);

        $decoded = substr($decoded, 0, -1) . chr(ord($decoded[-1]) ^ 1);
        $modified = base64_encode($decoded);

        self::assertFalse(EncryptionHelper::decrypt($modified, 'secret'));
    }

    #[Test]
    public function decrypt_preservesBinaryData(): void
    {
        $data = random_bytes(256);
        $key = 'secret';
        $encrypted = EncryptionHelper::encrypt($data, $key);

        self::assertSame($data, EncryptionHelper::decrypt($encrypted, $key));
    }
}
