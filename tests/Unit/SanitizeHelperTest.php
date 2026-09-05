<?php

declare(strict_types=1);

namespace Moudarir\Helpers\Tests\Unit;

use Moudarir\Helpers\SanitizeHelper;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class SanitizeHelperTest extends TestCase
{

    #[Test]
    public function slugify_returnsEmptyStringForEmptyInput()
    {
        $this->assertSame('', SanitizeHelper::slugify(''));
    }

    #[Test]
    public function slugify_convertsTextToLowercaseSlug()
    {
        $this->assertSame('hello-world', SanitizeHelper::slugify('Hello World'));
    }

    #[Test]
    public function slugify_removesHtmlTags()
    {
        $this->assertSame(
            'hello-world',
            SanitizeHelper::slugify('<p>Hello <strong>World</strong></p>')
        );
    }

    #[Test]
    public function slugify_removesAccents()
    {
        $this->assertSame('creer-un-slug', SanitizeHelper::slugify('Créer un slug'));
    }

    #[Test]
    public function slugify_replacesSpacesWithHyphens()
    {
        $this->assertSame('hello-world', SanitizeHelper::slugify('Hello   World'));
    }

    #[Test]
    public function slugify_collapsesConsecutiveHyphens()
    {
        $this->assertSame('hello-world', SanitizeHelper::slugify('Hello---World'));
    }

    #[Test]
    public function slugify_removesUnsupportedCharacters()
    {
        $this->assertSame('hello-world', SanitizeHelper::slugify('Hello @ World!'));
    }

    #[Test]
    public function slugify_replacesPercentSign()
    {
        $this->assertSame('100-pourcent', SanitizeHelper::slugify('100%'));
    }

    #[Test]
    public function slugify_replacesAmpersand()
    {
        $this->assertSame('rock-et-roll', SanitizeHelper::slugify('Rock & Roll'));
    }

    #[Test]
    public function slugify_replacesSpecialSigns()
    {
        $this->assertSame('plus-de-2-exemples-3', SanitizeHelper::slugify('+ de 2 exemples ³'));
    }

    #[Test]
    public function slugify_replacesForwardSlash()
    {
        $this->assertSame('php-8-4', SanitizeHelper::slugify('PHP/8.4'));
    }

    #[Test]
    public function slugify_preservesEncodedOctets()
    {
        $this->assertSame('file%20name', SanitizeHelper::slugify('file%20name'));
    }

    #[Test]
    public function slugify_replacesAdditionalSigns()
    {
        $this->assertSame(
            'hello-at-example-com',
            SanitizeHelper::slugify('Hello @ example.com', ['@' => '-at'])
        );
    }

    #[Test]
    public function slugify_overridesDefaultPercentReplacement()
    {
        $this->assertSame(
            '100-percent',
            SanitizeHelper::slugify('100%', ['%' => '-percent'])
        );
    }

    #[Test]
    public function slugify_overridesDefaultAmpersandReplacement()
    {
        $this->assertSame(
            'rock-and-roll',
            SanitizeHelper::slugify('Rock & Roll', ['&' => 'and'])
        );
    }

    #[Test]
    public function slugify_acceptsMultipleAdditionalReplacements()
    {
        $this->assertSame(
            '100-percent-rock-and-roll-at-home',
            SanitizeHelper::slugify(
                '100% Rock & Roll @ Home',
                [
                    '%' => '-percent',
                    '&' => 'and',
                    '@' => '-at',
                ]
            )
        );
    }

    #[Test]
    public function slugify_doesNotReplaceEncodedPercentOctets()
    {
        $this->assertSame(
            'file%20name',
            SanitizeHelper::slugify('file%20name', ['%' => '-percent'])
        );
    }

    #[Test]
    public function slugify_removesLeadingAndTrailingHyphens()
    {
        $this->assertSame('hello-world', SanitizeHelper::slugify('---Hello World---'));
    }

    #[Test]
    public function urlTitle_returnsEmptyStringForEmptyInput()
    {
        $this->assertSame('', SanitizeHelper::urlTitle(''));
        $this->assertSame('', SanitizeHelper::urlTitle('   '));
    }

    #[Test]
    public function urlTitle_trimsInput()
    {
        $this->assertSame('Hello-World', SanitizeHelper::urlTitle('  Hello World  '));
    }

    #[Test]
    public function urlTitle_removesHtmlTags()
    {
        $this->assertSame(
            'Hello-World',
            SanitizeHelper::urlTitle('<p>Hello <strong>World</strong></p>')
        );
    }

    #[Test]
    public function urlTitle_removesUnsupportedCharacters()
    {
        $this->assertSame('Hello-World', SanitizeHelper::urlTitle('Hello @ World!'));
    }

    #[Test]
    public function urlTitle_convertsSpacesToHyphens()
    {
        $this->assertSame('Hello-World', SanitizeHelper::urlTitle('Hello   World'));
    }

    #[Test]
    public function urlTitle_collapsesConsecutiveHyphens()
    {
        $this->assertSame('Hello-World', SanitizeHelper::urlTitle('Hello---World'));
    }

    #[Test]
    public function urlTitle_preservesCase()
    {
        $this->assertSame('Hello-World', SanitizeHelper::urlTitle('Hello World'));
    }

    #[Test]
    public function removeAccents_returnsOriginalAsciiText()
    {
        $text = 'Hello World 123';

        $this->assertSame($text, SanitizeHelper::removeAccents($text));
    }

    #[Test]
    public function removeAccents_replacesCommonAccents()
    {
        $this->assertSame(
            'Cafe Creme Francais',
            SanitizeHelper::removeAccents('Café Crème Français')
        );
    }

    #[Test]
    public function removeAccents_handlesExtendedCharacters()
    {
        $this->assertSame(
            'AEther OEuvre ss Th y',
            SanitizeHelper::removeAccents('Æther Œuvre ß Th ý')
        );
    }

    #[Test]
    public function removeAccents_removesPoundSign()
    {
        $this->assertSame('Price 10', SanitizeHelper::removeAccents('Price £10'));
    }

    #[Test]
    public function removeAccents_convertsEuroSign()
    {
        $this->assertSame('Price E10', SanitizeHelper::removeAccents('Price €10'));
    }
}
