<?php

declare(strict_types=1);

namespace Moudarir\Helpers\Tests\Unit;

use Moudarir\Helpers\Enums\EnumProtocol;
use Moudarir\Helpers\IsHelper;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class IsHelperTest extends TestCase
{

    #[Test]
    public function validUrl_acceptsHttpAndHttps(): void
    {
        self::assertTrue(IsHelper::validUrl('http://example.com'));
        self::assertTrue(IsHelper::validUrl('https://example.com'));
    }

    #[Test]
    public function validUrl_acceptsAllEnumProtocols(): void
    {
        self::assertTrue(IsHelper::validUrl('ftp://example.com'));
        self::assertTrue(IsHelper::validUrl('ftps://example.com'));
        self::assertTrue(IsHelper::validUrl('ws://example.com'));
        self::assertTrue(IsHelper::validUrl('wss://example.com'));
    }

    #[Test]
    public function validUrl_rejectsUnsupportedProtocol(): void
    {
        self::assertFalse(IsHelper::validUrl('ssh://example.com'));
        self::assertFalse(IsHelper::validUrl('mailto:user@example.com'));
    }

    #[Test]
    public function validUrl_restrictsAllowedProtocols(): void
    {
        self::assertTrue(IsHelper::validUrl(
            'https://example.com',
            [EnumProtocol::HTTP, EnumProtocol::HTTPS]
        ));

        self::assertFalse(IsHelper::validUrl(
            'ftp://example.com',
            [EnumProtocol::HTTP, EnumProtocol::HTTPS]
        ));
    }

    #[Test]
    public function validUrl_ignoresInvalidProtocols(): void
    {
        self::assertTrue(IsHelper::validUrl(
            'https://example.com',
            ['ssh', EnumProtocol::HTTPS]
        ));

        self::assertFalse(IsHelper::validUrl(
            'http://example.com',
            ['ssh', EnumProtocol::HTTPS]
        ));
    }

    #[Test]
    public function validUrl_rejectsProtocolNotAllowed(): void
    {
        self::assertFalse(IsHelper::validUrl('http://example.com', [EnumProtocol::HTTPS]));
    }

    #[Test]
    public function validUrl_usesAllProtocolsWhenNoValidProtocolIsProvided(): void
    {
        self::assertTrue(IsHelper::validUrl('https://example.com', ['ssh', 'custom']));
    }

    #[Test]
    public function validUrl_acceptsUrlWithPath(): void
    {
        self::assertTrue(IsHelper::validUrl('https://example.com/path/to/resource'));
    }

    #[Test]
    public function validUrl_acceptsUrlWithQuery(): void
    {
        self::assertTrue(IsHelper::validUrl('https://example.com?foo=bar&baz=123'));
    }

    #[Test]
    public function validUrl_acceptsUrlWithFragment(): void
    {
        self::assertTrue(IsHelper::validUrl('https://example.com#section'));
    }

    #[Test]
    public function validUrl_acceptsUrlWithAuthentication(): void
    {
        self::assertTrue(IsHelper::validUrl('https://user:password@example.com'));
    }

    #[Test]
    public function validUrl_acceptsUrlWithPort(): void
    {
        self::assertTrue(IsHelper::validUrl('https://example.com:8080'));
    }

    #[Test]
    public function validUrl_acceptsIpv4Address(): void
    {
        self::assertTrue(IsHelper::validUrl('https://127.0.0.1'));
    }

    #[Test]
    public function validUrl_acceptsIpv6Address(): void
    {
        self::assertTrue(IsHelper::validUrl('https://[::1]'));
    }

    #[Test]
    public function validUrl_rejectsUrlWithoutProtocol(): void
    {
        self::assertFalse(IsHelper::validUrl('example.com'));
    }

    #[Test]
    public function validUrl_rejectsEmptyValue(): void
    {
        self::assertFalse(IsHelper::validUrl(''));
    }

    #[Test]
    public function validUrl_rejectsInvalidUrl(): void
    {
        self::assertFalse(IsHelper::validUrl('not a url'));

        self::assertFalse(IsHelper::validUrl('https://'));
    }

    #[Test]
    public function validIP_acceptsIpv4(): void
    {
        self::assertTrue(IsHelper::validIP('192.168.1.1'));
    }

    #[Test]
    public function validIP_acceptsIpv6(): void
    {
        self::assertTrue(IsHelper::validIP('2001:db8::1'));
    }

    #[Test]
    public function validIP_acceptsIpv4WhenRequested(): void
    {
        self::assertTrue(IsHelper::validIP('192.168.1.1', 'ipv4'));
    }

    #[Test]
    public function validIP_acceptsIpv6WhenRequested(): void
    {
        self::assertTrue(IsHelper::validIP('2001:db8::1', 'ipv6'));
    }

    #[Test]
    public function validIP_rejectsIpv6WhenIpv4IsRequested(): void
    {
        self::assertFalse(IsHelper::validIP('2001:db8::1', 'ipv4'));
    }

    #[Test]
    public function validIP_rejectsIpv4WhenIpv6IsRequested(): void
    {
        self::assertFalse(IsHelper::validIP('192.168.1.1', 'ipv6'));
    }

    #[Test]
    public function validIP_rejectsInvalidIp(): void
    {
        self::assertFalse(IsHelper::validIP('999.999.999.999'));

        self::assertFalse(IsHelper::validIP('not-an-ip'));
    }

    #[Test]
    public function validIP_rejectsEmptyValue(): void
    {
        self::assertFalse(IsHelper::validIP());
        self::assertFalse(IsHelper::validIP(''));
        self::assertFalse(IsHelper::validIP('   '));
    }

    #[Test]
    public function validIP_rejectsInvalidIpv4(): void
    {
        self::assertFalse(IsHelper::validIP('192.168.1.256', 'ipv4'));
    }

    #[Test]
    public function validIP_rejectsInvalidIpv6(): void
    {
        self::assertFalse(IsHelper::validIP('2001:::1', 'ipv6'));
    }

    #[Test]
    public function validIP_acceptsUnknownTypeAsBothIpVersions(): void
    {
        self::assertTrue(IsHelper::validIP('192.168.1.1', 'unknown'));

        self::assertTrue(IsHelper::validIP('2001:db8::1', 'unknown'));
    }

    #[Test]
    public function validEmail_acceptsValidEmail(): void
    {
        self::assertTrue(IsHelper::validEmail('user@example.com'));
    }

    #[Test]
    public function validEmail_acceptsEmailWithSubdomain(): void
    {
        self::assertTrue(IsHelper::validEmail('user@mail.example.com'));
    }

    #[Test]
    public function validEmail_acceptsEmailWithPlusAddressing(): void
    {
        self::assertTrue(IsHelper::validEmail('user+tag@example.com'));
    }

    #[Test]
    public function validEmail_acceptsEmailWithHyphenInDomain(): void
    {
        self::assertTrue(IsHelper::validEmail('user@my-domain.com'));
    }

    #[Test]
    public function validEmail_acceptsInternationalizedDomain(): void
    {
        if (function_exists('idn_to_ascii') === false) {
            self::markTestSkipped('The intl extension is not available.');
        }

        self::assertTrue(IsHelper::validEmail('user@exemple.рф'));
    }

    #[Test]
    public function validEmail_rejectsInvalidEmail(): void
    {
        self::assertFalse(IsHelper::validEmail('not-an-email'));

        self::assertFalse(IsHelper::validEmail('user@'));

        self::assertFalse(IsHelper::validEmail('@example.com'));

        self::assertFalse(IsHelper::validEmail('user@example'));
    }

    #[Test]
    public function validEmail_rejectsEmailWithSpaces(): void
    {
        self::assertFalse(IsHelper::validEmail('user name@example.com'));

        self::assertFalse(IsHelper::validEmail('user@example .com'));
    }

    #[Test]
    public function validEmail_rejectsMalformedDomain(): void
    {
        self::assertFalse(IsHelper::validEmail('user@-example.com'));

        self::assertFalse(IsHelper::validEmail('user@example-.com'));

        self::assertFalse(IsHelper::validEmail('user@example..com'));
    }

    #[Test]
    public function validMac_acceptsValidMacAddress(): void
    {
        self::assertTrue(IsHelper::validMac('00:11:22:33:44:55'));
    }

    #[Test]
    public function validMac_acceptsMacAddressWithHyphens(): void
    {
        self::assertTrue(IsHelper::validMac('00-11-22-33-44-55'));
    }

    #[Test]
    public function validMac_acceptsMacAddressWithDots(): void
    {
        self::assertTrue(IsHelper::validMac('0011.2233.4455'));
    }

    #[Test]
    public function validMac_rejectsInvalidMacAddress(): void
    {
        self::assertFalse(IsHelper::validMac('00:11:22:33:44'));

        self::assertFalse(IsHelper::validMac('00:11:22:33:44:GG'));

        self::assertFalse(IsHelper::validMac('not-a-mac'));
    }

    #[Test]
    public function validMac_rejectsEmptyValue(): void
    {
        self::assertFalse(IsHelper::validMac(''));
    }

    #[Test]
    public function validBase64_acceptsValidBase64(): void
    {
        self::assertTrue(IsHelper::validBase64('SGVsbG8gV29ybGQ='));
    }

    #[Test]
    public function validBase64_acceptsEmptyValue(): void
    {
        self::assertTrue(IsHelper::validBase64(''));
    }

    #[Test]
    public function validBase64_rejectsInvalidBase64(): void
    {
        self::assertFalse(IsHelper::validBase64('not-base64'));

        self::assertFalse(IsHelper::validBase64('SGVsbG8'));
    }

    #[Test]
    public function validBase64_rejectsInvalidCharacters(): void
    {
        self::assertFalse(IsHelper::validBase64('SGVsbG8gV29ybGQ!'));
    }

    #[Test]
    public function natural_acceptsPositiveInteger(): void
    {
        self::assertTrue(IsHelper::natural('123'));
    }

    #[Test]
    public function natural_acceptsZero(): void
    {
        self::assertTrue(IsHelper::natural('0'));
    }

    #[Test]
    public function natural_acceptsLeadingZeros(): void
    {
        self::assertTrue(IsHelper::natural('00123'));
    }

    #[Test]
    public function natural_rejectsNegativeNumber(): void
    {
        self::assertFalse(IsHelper::natural('-123'));
    }

    #[Test]
    public function natural_rejectsPositiveSign(): void
    {
        self::assertFalse(IsHelper::natural('+123'));
    }

    #[Test]
    public function natural_rejectsDecimal(): void
    {
        self::assertFalse(IsHelper::natural('12.3'));
    }

    #[Test]
    public function natural_rejectsSpaces(): void
    {
        self::assertFalse(IsHelper::natural('123 '));
        self::assertFalse(IsHelper::natural(' 123'));
    }

    #[Test]
    public function natural_rejectsEmptyValue(): void
    {
        self::assertFalse(IsHelper::natural(''));
    }

    #[Test]
    public function naturalNoZero_acceptsPositiveInteger(): void
    {
        self::assertTrue(IsHelper::naturalNoZero('123'));
    }

    #[Test]
    public function naturalNoZero_rejectsZero(): void
    {
        self::assertFalse(IsHelper::naturalNoZero('0'));
        self::assertFalse(IsHelper::naturalNoZero('00'));
        self::assertFalse(IsHelper::naturalNoZero('000'));
    }

    #[Test]
    public function naturalNoZero_acceptsPositiveIntegerWithLeadingZeros(): void
    {
        self::assertTrue(IsHelper::naturalNoZero('00123'));
    }

    #[Test]
    public function naturalNoZero_rejectsNegativeNumber(): void
    {
        self::assertFalse(IsHelper::naturalNoZero('-123'));
    }

    #[Test]
    public function naturalNoZero_rejectsPositiveSign(): void
    {
        self::assertFalse(IsHelper::naturalNoZero('+123'));
    }

    #[Test]
    public function naturalNoZero_rejectsDecimal(): void
    {
        self::assertFalse(IsHelper::naturalNoZero('12.3'));
    }

    #[Test]
    public function naturalNoZero_rejectsEmptyValue(): void
    {
        self::assertFalse(IsHelper::naturalNoZero(''));
    }

    #[Test]
    public function alpha_acceptsLetters(): void
    {
        self::assertTrue(IsHelper::alpha('abc'));
        self::assertTrue(IsHelper::alpha('ABC'));
    }

    #[Test]
    public function alpha_rejectsNumbers(): void
    {
        self::assertFalse(IsHelper::alpha('abc123'));
    }

    #[Test]
    public function alpha_rejectsSpaces(): void
    {
        self::assertFalse(IsHelper::alpha('abc def'));
    }

    #[Test]
    public function alpha_rejectsSpecialCharacters(): void
    {
        self::assertFalse(IsHelper::alpha('abc-def'));
        self::assertFalse(IsHelper::alpha('abc_def'));
    }

    #[Test]
    public function alpha_rejectsEmptyValue(): void
    {
        self::assertFalse(IsHelper::alpha(''));
    }

    #[Test]
    public function alpha_rejectsUnicodeLetters(): void
    {
        self::assertFalse(IsHelper::alpha('éàç'));
    }

    #[Test]
    public function alphaNumeric_acceptsLetters(): void
    {
        self::assertTrue(IsHelper::alphaNumeric('abc'));
        self::assertTrue(IsHelper::alphaNumeric('ABC'));
    }

    #[Test]
    public function alphaNumeric_acceptsNumbers(): void
    {
        self::assertTrue(IsHelper::alphaNumeric('123'));
    }

    #[Test]
    public function alphaNumeric_acceptsLettersAndNumbers(): void
    {
        self::assertTrue(IsHelper::alphaNumeric('abc123'));
    }

    #[Test]
    public function alphaNumeric_rejectsSpaces(): void
    {
        self::assertFalse(IsHelper::alphaNumeric('abc 123'));
    }

    #[Test]
    public function alphaNumeric_rejectsSpecialCharacters(): void
    {
        self::assertFalse(IsHelper::alphaNumeric('abc-123'));
        self::assertFalse(IsHelper::alphaNumeric('abc_123'));
    }

    #[Test]
    public function alphaNumeric_rejectsEmptyValue(): void
    {
        self::assertFalse(IsHelper::alphaNumeric(''));
    }

    #[Test]
    public function alphaNumericSpaces_acceptsLettersNumbersAndSpaces(): void
    {
        self::assertTrue(IsHelper::alphaNumericSpaces('abc'));
        self::assertTrue(IsHelper::alphaNumericSpaces('ABC123'));
        self::assertTrue(IsHelper::alphaNumericSpaces('abc 123'));
    }

    #[Test]
    public function alphaNumericSpaces_rejectsOtherCharacters(): void
    {
        self::assertFalse(IsHelper::alphaNumericSpaces('abc-123'));
        self::assertFalse(IsHelper::alphaNumericSpaces('abc_123'));
        self::assertFalse(IsHelper::alphaNumericSpaces('abc.123'));
    }

    #[Test]
    public function alphaNumericSpaces_rejectsEmptyValue(): void
    {
        self::assertFalse(IsHelper::alphaNumericSpaces(''));
    }

    #[Test]
    public function alphaDash_acceptsLettersNumbersUnderscoresAndDashes(): void
    {
        self::assertTrue(IsHelper::alphaDash('abc'));
        self::assertTrue(IsHelper::alphaDash('abc123'));
        self::assertTrue(IsHelper::alphaDash('abc_def'));
        self::assertTrue(IsHelper::alphaDash('abc-def'));
        self::assertTrue(IsHelper::alphaDash('abc_def-123'));
    }

    #[Test]
    public function alphaDash_rejectsSpaces(): void
    {
        self::assertFalse(IsHelper::alphaDash('abc def'));
    }

    #[Test]
    public function alphaDash_rejectsOtherCharacters(): void
    {
        self::assertFalse(IsHelper::alphaDash('abc.def'));
        self::assertFalse(IsHelper::alphaDash('abc@def'));
    }

    #[Test]
    public function alphaDash_rejectsEmptyValue(): void
    {
        self::assertFalse(IsHelper::alphaDash(''));
    }

    #[Test]
    public function alnumDash_acceptsLettersNumbersAndDashes(): void
    {
        self::assertTrue(IsHelper::alnumDash('abc'));
        self::assertTrue(IsHelper::alnumDash('abc123'));
        self::assertTrue(IsHelper::alnumDash('abc-123'));
    }

    #[Test]
    public function alnumDash_rejectsUnderscore(): void
    {
        self::assertFalse(IsHelper::alnumDash('abc_123'));
    }

    #[Test]
    public function alnumDash_rejectsSpaces(): void
    {
        self::assertFalse(IsHelper::alnumDash('abc 123'));
    }

    #[Test]
    public function alnumDash_rejectsEmptyValue(): void
    {
        self::assertFalse(IsHelper::alnumDash(''));
    }

    #[Test]
    public function alnumUnderscore_acceptsLettersNumbersAndUnderscores(): void
    {
        self::assertTrue(IsHelper::alnumUnderscore('abc'));
        self::assertTrue(IsHelper::alnumUnderscore('abc123'));
        self::assertTrue(IsHelper::alnumUnderscore('abc_123'));
    }

    #[Test]
    public function alnumUnderscore_rejectsDash(): void
    {
        self::assertFalse(IsHelper::alnumUnderscore('abc-123'));
    }

    #[Test]
    public function alnumUnderscore_rejectsSpaces(): void
    {
        self::assertFalse(IsHelper::alnumUnderscore('abc 123'));
    }

    #[Test]
    public function alnumUnderscore_rejectsEmptyValue(): void
    {
        self::assertFalse(IsHelper::alnumUnderscore(''));
    }

    #[Test]
    public function numeric_acceptsInteger(): void
    {
        self::assertTrue(IsHelper::numeric('123'));
        self::assertTrue(IsHelper::numeric('-123'));
        self::assertTrue(IsHelper::numeric('+123'));
    }

    #[Test]
    public function numeric_acceptsDecimal(): void
    {
        self::assertTrue(IsHelper::numeric('123.45'));
        self::assertTrue(IsHelper::numeric('-123.45'));
        self::assertTrue(IsHelper::numeric('+123.45'));
    }

    #[Test]
    public function numeric_acceptsDecimalWithoutLeadingZero(): void
    {
        self::assertTrue(IsHelper::numeric('.45'));
        self::assertTrue(IsHelper::numeric('-.45'));
    }

    #[Test]
    public function numeric_rejectsTrailingDecimalPoint(): void
    {
        self::assertFalse(IsHelper::numeric('123.'));
    }

    #[Test]
    public function numeric_rejectsScientificNotation(): void
    {
        self::assertFalse(IsHelper::numeric('1e10'));
    }

    #[Test]
    public function numeric_rejectsSpaces(): void
    {
        self::assertFalse(IsHelper::numeric('123 '));
        self::assertFalse(IsHelper::numeric(' 123'));
    }

    #[Test]
    public function numeric_rejectsEmptyValue(): void
    {
        self::assertFalse(IsHelper::numeric(''));
    }

    #[Test]
    public function integer_acceptsPositiveInteger(): void
    {
        self::assertTrue(IsHelper::integer('123'));
    }

    #[Test]
    public function integer_acceptsNegativeInteger(): void
    {
        self::assertTrue(IsHelper::integer('-123'));
    }

    #[Test]
    public function integer_acceptsPositiveSign(): void
    {
        self::assertTrue(IsHelper::integer('+123'));
    }

    #[Test]
    public function integer_acceptsZero(): void
    {
        self::assertTrue(IsHelper::integer('0'));
    }

    #[Test]
    public function integer_acceptsLeadingZeros(): void
    {
        self::assertTrue(IsHelper::integer('00123'));
    }

    #[Test]
    public function integer_rejectsDecimal(): void
    {
        self::assertFalse(IsHelper::integer('123.45'));
    }

    #[Test]
    public function integer_rejectsScientificNotation(): void
    {
        self::assertFalse(IsHelper::integer('1e10'));
    }

    #[Test]
    public function integer_rejectsSpaces(): void
    {
        self::assertFalse(IsHelper::integer('123 '));
    }

    #[Test]
    public function integer_rejectsEmptyValue(): void
    {
        self::assertFalse(IsHelper::integer(''));
    }

    #[Test]
    public function decimal_acceptsDecimal(): void
    {
        self::assertTrue(IsHelper::decimal('123.45'));
        self::assertTrue(IsHelper::decimal('-123.45'));
        self::assertTrue(IsHelper::decimal('+123.45'));
    }

    #[Test]
    public function decimal_acceptsLeadingZeros(): void
    {
        self::assertTrue(IsHelper::decimal('00123.45'));
    }

    #[Test]
    public function decimal_rejectsDecimalWithoutIntegerPart(): void
    {
        self::assertFalse(IsHelper::decimal('.45'));
    }

    #[Test]
    public function decimal_rejectsDecimalWithoutFractionalPart(): void
    {
        self::assertFalse(IsHelper::decimal('123.'));
    }

    #[Test]
    public function decimal_rejectsInteger(): void
    {
        self::assertFalse(IsHelper::decimal('123'));
    }

    #[Test]
    public function decimal_rejectsScientificNotation(): void
    {
        self::assertFalse(IsHelper::decimal('1e10'));
    }

    #[Test]
    public function decimal_rejectsSpaces(): void
    {
        self::assertFalse(IsHelper::decimal('123.45 '));
    }

    #[Test]
    public function decimal_rejectsEmptyValue(): void
    {
        self::assertFalse(IsHelper::decimal(''));
    }

    #[Test]
    public function hex_acceptsHexadecimalValue(): void
    {
        self::assertTrue(IsHelper::hex('ABCDEF'));
        self::assertTrue(IsHelper::hex('abcdef'));
        self::assertTrue(IsHelper::hex('123456'));
    }

    #[Test]
    public function hex_acceptsHexadecimalValueWithPrefix(): void
    {
        self::assertTrue(IsHelper::hex('0xABCDEF'));
        self::assertTrue(IsHelper::hex('0Xabcdef'));
    }

    #[Test]
    public function hex_rejectsNonHexadecimalCharacters(): void
    {
        self::assertFalse(IsHelper::hex('GHI'));
        self::assertFalse(IsHelper::hex('123Z'));
    }

    #[Test]
    public function hex_rejectsInvalidPrefix(): void
    {
        self::assertFalse(IsHelper::hex('0yABCDEF'));
    }

    #[Test]
    public function hex_rejectsEmptyValue(): void
    {
        self::assertFalse(IsHelper::hex(''));
    }

    #[Test]
    public function hex_rejectsPrefixWithoutValue(): void
    {
        self::assertFalse(IsHelper::hex('0x'));
    }

    #[Test]
    public function arabicString_acceptsArabicText(): void
    {
        self::assertTrue(IsHelper::containsArabic('مرحبا'));
    }

    #[Test]
    public function arabicString_acceptsMixedTextContainingArabic(): void
    {
        self::assertTrue(IsHelper::containsArabic('Hello مرحبا'));
    }

    #[Test]
    public function arabicString_acceptsArabicCharactersWithinString(): void
    {
        self::assertTrue(IsHelper::containsArabic('abcمdef'));
    }

    #[Test]
    public function arabicString_rejectsLatinText(): void
    {
        self::assertFalse(IsHelper::containsArabic('Hello world'));
    }

    #[Test]
    public function arabicString_rejectsNumbers(): void
    {
        self::assertFalse(IsHelper::containsArabic('123456'));
    }

    #[Test]
    public function arabicString_rejectsPunctuationOnly(): void
    {
        self::assertFalse(IsHelper::containsArabic('!?,.;:'));
    }

    #[Test]
    public function arabicString_acceptsArabicTextWithNumbersAndPunctuation(): void
    {
        self::assertTrue(
            IsHelper::containsArabic('مرحبا 123!')
        );
    }

    #[Test]
    public function arabicString_rejectsEmptyValue(): void
    {
        self::assertFalse(IsHelper::containsArabic(''));
    }

    #[Test]
    public function onlyArabic_acceptsEmptyString(): void
    {
        self::assertTrue(IsHelper::onlyArabic(''));
    }

    #[Test]
    public function onlyArabic_acceptsArabicText(): void
    {
        self::assertTrue(IsHelper::onlyArabic('مرحبا بالعالم'));
    }

    #[Test]
    public function onlyArabic_acceptsArabicDiacritics(): void
    {
        self::assertTrue(IsHelper::onlyArabic('مَرْحَبًا بِالْعَالَمِ'));
    }

    #[Test]
    public function onlyArabic_acceptsArabicLettersWithLatinDigits(): void
    {
        self::assertTrue(IsHelper::onlyArabic('مرحبا 123'));
    }

    #[Test]
    public function onlyArabic_acceptsArabicLettersWithArabicDigits(): void
    {
        self::assertTrue(IsHelper::onlyArabic('مرحبا ١٢٣'));
    }

    #[Test]
    public function onlyArabic_acceptsDecimalNumbers(): void
    {
        self::assertTrue(IsHelper::onlyArabic('1.3'));

        self::assertTrue(IsHelper::onlyArabic('١٫٣'));
    }

    #[Test]
    public function onlyArabic_acceptsPunctuation(): void
    {
        self::assertTrue(IsHelper::onlyArabic('مرحبا، كيف حالك؟'));

        self::assertTrue(IsHelper::onlyArabic('مرحبا؛ أهلاً وسهلاً.'));
    }

    #[Test]
    public function onlyArabic_acceptsWhitespace(): void
    {
        self::assertTrue(IsHelper::onlyArabic("مرحبا\nبالعالم\t!"));
    }

    #[Test]
    public function onlyArabic_acceptsSymbols(): void
    {
        self::assertTrue(IsHelper::onlyArabic('مرحبا ❤️ 100%'));
    }

    #[Test]
    public function onlyArabic_acceptsTextWithoutLetters(): void
    {
        self::assertTrue(IsHelper::onlyArabic('12345 !?,.'));
    }

    #[Test]
    public function onlyArabic_rejectsLatinLetters(): void
    {
        self::assertFalse(IsHelper::onlyArabic('مرحبا Hello'));
    }

    #[Test]
    public function onlyArabic_rejectsCyrillicLetters(): void
    {
        self::assertFalse(IsHelper::onlyArabic('مرحبا Привет'));
    }

    #[Test]
    public function onlyArabic_rejectsGreekLetters(): void
    {
        self::assertFalse(IsHelper::onlyArabic('مرحبا Γειά'));
    }

    #[Test]
    public function onlyArabic_rejectsHebrewLetters(): void
    {
        self::assertFalse(IsHelper::onlyArabic('مرحبا שלום'));
    }

    #[Test]
    public function onlyArabic_rejectsMixedArabicAndOtherLanguage(): void
    {
        self::assertFalse(IsHelper::onlyArabic('Hello مرحبا 123'));
    }
}
