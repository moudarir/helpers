<?php

declare(strict_types=1);

namespace Moudarir\Helpers\Tests\Unit;

use Moudarir\Helpers\StringHelper;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class StringHelperTest extends TestCase
{

    #[Test]
    public function firstLetter_returnsEmptyStringForEmptyString(): void
    {
        self::assertSame('', StringHelper::firstLetter(''));
    }

    #[Test]
    public function firstLetter_returnsFirstLetterInUppercaseByDefault(): void
    {
        self::assertSame('L', StringHelper::firstLetter('lorem'));
    }

    #[Test]
    public function firstLetter_returnsFirstLetterInLowercaseWhenFormatIsLower(): void
    {
        self::assertSame('l', StringHelper::firstLetter('Lorem', 'lower'));
    }

    #[Test]
    public function firstLetter_returnsUppercaseForUnknownFormat(): void
    {
        self::assertSame('L', StringHelper::firstLetter('lorem', 'unknown'));
    }

    #[Test]
    public function firstLetter_handlesUtf8Characters(): void
    {
        self::assertSame('É', StringHelper::firstLetter('école'));
    }

    #[Test]
    public function firstLetter_convertsUtf8CharacterToLowercase(): void
    {
        self::assertSame('é', StringHelper::firstLetter('École', 'lower'));
    }

    #[Test]
    public function firstLetters_returnsFirstLettersSeparatedByDotsByDefault(): void
    {
        self::assertSame('L.I.D', StringHelper::firstLetters('Lorem ipsum dolor'));
    }

    #[Test]
    public function firstLetters_usesCustomSeparator(): void
    {
        self::assertSame('L-I-D', StringHelper::firstLetters('Lorem ipsum dolor', '-'));
    }

    #[Test]
    public function firstLetters_usesRequestedFormat(): void
    {
        self::assertSame(
            'l.i.d',
            StringHelper::firstLetters('Lorem ipsum dolor', '.', 'lower')
        );
    }

    #[Test]
    public function firstLetters_ignoresRepeatedWhitespace(): void
    {
        self::assertSame('L.I.D', StringHelper::firstLetters("  Lorem   ipsum\t dolor\n"));
    }

    #[Test]
    public function firstLetters_returnsEmptyStringForEmptyString(): void
    {
        self::assertSame('', StringHelper::firstLetters(''));
    }

    #[Test]
    public function firstLetters_handlesUtf8Characters(): void
    {
        self::assertSame('É.À', StringHelper::firstLetters('école àbaco'));
    }

    #[Test]
    public function firstLetters_handlesUtf8CharactersInLowercaseFormat(): void
    {
        self::assertSame(
            'é.à',
            StringHelper::firstLetters('École Àbaco', '.', 'lower')
        );
    }

    #[Test]
    public function toCamelcase_returnsEmptyStringForEmptyString(): void
    {
        self::assertSame('', StringHelper::toCamelcase(''));
    }

    #[Test]
    public function toCamelcase_convertsUnderscoreSeparatedString(): void
    {
        self::assertSame('helloWorld', StringHelper::toCamelcase('hello_world'));
    }

    #[Test]
    public function toCamelcase_usesCustomSeparator(): void
    {
        self::assertSame('helloWorld', StringHelper::toCamelcase('hello-world', '-'));
    }

    #[Test]
    public function toCamelcase_handlesMultipleWords(): void
    {
        self::assertSame('thisIsAString', StringHelper::toCamelcase('this_is_a_string'));
    }

    #[Test]
    public function toCamelcase_handlesAlreadyCamelCaseString(): void
    {
        self::assertSame('helloWorld', StringHelper::toCamelcase('HelloWorld'));
    }

    #[Test]
    public function bytesToHuman_returnsZeroBytes(): void
    {
        self::assertSame('0 B', StringHelper::bytesToHuman(0));
    }

    #[Test]
    public function bytesToHuman_convertsBinaryBytesToKibibytes(): void
    {
        self::assertSame('1 KiB', StringHelper::bytesToHuman(1024));
    }

    #[Test]
    public function bytesToHuman_convertsBinaryBytesToMebibytes(): void
    {
        self::assertSame('1 MiB', StringHelper::bytesToHuman(1024 ** 2));
    }

    #[Test]
    public function bytesToHuman_convertsBinaryBytesToGibibytes(): void
    {
        self::assertSame('1 GiB', StringHelper::bytesToHuman(1024 ** 3));
    }

    #[Test]
    public function bytesToHuman_roundsBinaryValuesToTwoDecimalPlaces(): void
    {
        self::assertSame('1.5 KiB', StringHelper::bytesToHuman(1536));
    }

    #[Test]
    public function bytesToHuman_removesUnnecessaryDecimalZeros(): void
    {
        self::assertSame('10 KiB', StringHelper::bytesToHuman(10240));
    }

    #[Test]
    public function bytesToHuman_convertsDecimalBytesToKilobytes(): void
    {
        self::assertSame('1 kB', StringHelper::bytesToHuman(1000, false));
    }

    #[Test]
    public function bytesToHuman_convertsDecimalBytesToMegabytes(): void
    {
        self::assertSame('1 MB', StringHelper::bytesToHuman(1000 ** 2, false));
    }

    #[Test]
    public function bytesToHuman_convertsDecimalBytesToGigabytes(): void
    {
        self::assertSame('1 GB', StringHelper::bytesToHuman(1000 ** 3, false));
    }

    #[Test]
    public function bytesToHuman_roundsDecimalValuesToTwoDecimalPlaces(): void
    {
        self::assertSame('1.5 kB', StringHelper::bytesToHuman(1500, false));
    }

    #[Test]
    public function bytesToHuman_handlesNegativeValues(): void
    {
        self::assertSame('-1 KiB', StringHelper::bytesToHuman(-1024));
    }

    #[Test]
    public function bytesToHuman_doesNotExceedLargestBinaryUnit(): void
    {
        self::assertSame('2 PiB', StringHelper::bytesToHuman(1024 ** 5 * 2));
    }

    #[Test]
    public function bytesToHuman_doesNotExceedLargestDecimalUnit(): void
    {
        self::assertSame('2 PB', StringHelper::bytesToHuman(1000 ** 5 * 2, false));
    }

    #[Test]
    public function excerpt_returnsEmptyStringForEmptyString(): void
    {
        self::assertSame('', StringHelper::excerpt(''));
    }

    #[Test]
    public function excerpt_returnsEmptyStringWhenMaxLengthIsZero(): void
    {
        self::assertSame('', StringHelper::excerpt('Lorem ipsum', 0));
    }

    #[Test]
    public function excerpt_returnsEmptyStringWhenMaxLengthIsNegative(): void
    {
        self::assertSame('', StringHelper::excerpt('Lorem ipsum', -1));
    }

    #[Test]
    public function excerpt_returnsOriginalStringWhenLengthDoesNotExceedMaximum(): void
    {
        self::assertSame('Lorem ipsum', StringHelper::excerpt('Lorem ipsum', 11));
    }

    #[Test]
    public function excerpt_truncatesStringAndPlacesEllipsisOnRightByDefault(): void
    {
        self::assertSame('Lorem ipsu…', StringHelper::excerpt('Lorem ipsum dolor', 11));
    }

    #[Test]
    public function excerpt_truncatesStringAndPlacesEllipsisOnLeft(): void
    {
        self::assertSame(
            '…m dolor',
            StringHelper::excerpt('Lorem ipsum dolor', 8, 'left')
        );
    }

    #[Test]
    public function excerpt_usesRightPositionForUnknownEllipsisPosition(): void
    {
        self::assertSame(
            'Lorem ipsu…',
            StringHelper::excerpt('Lorem ipsum dolor', 11, 'unknown')
        );
    }

    #[Test]
    public function excerpt_usesCustomEllipsis(): void
    {
        self::assertSame(
            'Lorem ... ',
            StringHelper::excerpt('Lorem ipsum dolor', 10, 'right', '... ')
        );
    }

    #[Test]
    public function excerpt_doesNotExceedMaximumLength(): void
    {
        $result = StringHelper::excerpt('Lorem ipsum dolor sit amet', 10);

        self::assertLessThanOrEqual(10, mb_strlen($result));
    }

    #[Test]
    public function excerpt_countsUtf8CharactersInsteadOfBytes(): void
    {
        self::assertSame('Été…', StringHelper::excerpt('Été très chaud', 4));
    }

    #[Test]
    public function excerpt_truncatesUtf8StringFromLeft(): void
    {
        self::assertSame(
            '…chaud',
            StringHelper::excerpt('Été très chaud', 6, 'left')
        );
    }

    #[Test]
    public function excerpt_stripsHtmlTags(): void
    {
        self::assertSame(
            'Lorem ipsum',
            StringHelper::excerpt('<strong>Lorem</strong> <em>ipsum</em>')
        );
    }

    #[Test]
    public function excerpt_extractsContentFromFirstParagraph(): void
    {
        self::assertSame(
            'Lorem ipsum',
            StringHelper::excerpt(
                '<div><p class="text-large">Lorem <strong>ipsum</strong></p></div>'
            )
        );
    }

    #[Test]
    public function excerpt_extractsOnlyFirstParagraphWhenMultipleParagraphsExist(): void
    {
        self::assertSame(
            'Lorem ipsum',
            StringHelper::excerpt('<p>Lorem ipsum</p><p>Dolor sit amet</p>')
        );
    }

    #[Test]
    public function excerpt_stripsHtmlBeforeCalculatingLength(): void
    {
        self::assertSame(
            'Lorem ipsu…',
            StringHelper::excerpt('<p><strong>Lorem ipsum dolor</strong></p>', 11)
        );
    }

    #[Test]
    public function excerpt_returnsOnlyEllipsisWhenMaximumLengthIsOne(): void
    {
        self::assertSame('…', StringHelper::excerpt('Lorem ipsum', 1));
    }

    #[Test]
    public function excerpt_truncatesEllipsisWhenItIsLongerThanMaximumLength(): void
    {
        self::assertSame(
            '...',
            StringHelper::excerpt('Lorem ipsum', 3, 'right', '......')
        );
    }

    #[Test]
    public function excerpt_returnsEmptyStringWhenHtmlContainsOnlyTags(): void
    {
        self::assertSame('', StringHelper::excerpt('<p><strong></strong></p>'));
    }
}
