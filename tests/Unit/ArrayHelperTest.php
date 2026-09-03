<?php

declare(strict_types=1);

namespace Moudarir\Helpers\Tests\Unit;

use Moudarir\Helpers\ArrayHelper;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ArrayHelperTest extends TestCase
{

    #[Test]
    public function diff_returnsNoDifferenceWhenArraysAreIdentical(): void
    {
        self::assertSame(
            [
                'diff' => false,
                'add' => [],
                'remove' => [],
            ],
            ArrayHelper::diff(
                [1, 2, 3],
                [1, 2, 3]
            )
        );
    }

    #[Test]
    public function diff_detectsAddedValues(): void
    {
        self::assertSame(
            [
                'diff' => true,
                'add' => [
                    3 => 4,
                    4 => 5,
                ],
                'remove' => [],
            ],
            ArrayHelper::diff(
                [1, 2, 3],
                [1, 2, 3, 4, 5]
            )
        );
    }

    #[Test]
    public function diff_detectsRemovedValues(): void
    {
        self::assertSame(
            [
                'diff' => true,
                'add' => [],
                'remove' => [
                    0 => 1,
                    2 => 3,
                ],
            ],
            ArrayHelper::diff(
                [1, 2, 3],
                [2]
            )
        );
    }

    #[Test]
    public function diff_detectsAddedAndRemovedValues(): void
    {
        self::assertSame(
            [
                'diff' => true,
                'add' => [
                    1 => 4,
                    2 => 5,
                ],
                'remove' => [
                    0 => 1,
                    2 => 3,
                ],
            ],
            ArrayHelper::diff(
                [1, 2, 3],
                [2, 4, 5]
            )
        );
    }

    #[Test]
    public function diff_convertsNewValuesToIdsWhenAsIdsIsEnabled(): void
    {
        self::assertSame(
            [
                'diff' => false,
                'add' => [],
                'remove' => [],
            ],
            ArrayHelper::diff(
                [1, 2, 3],
                ['1', '2', '3'],
                true
            )
        );
    }

    #[Test]
    public function diff_detectsChangesAfterConvertingNewValuesToIds(): void
    {
        self::assertSame(
            [
                'diff' => true,
                'add' => [
                    2 => 4,
                ],
                'remove' => [
                    0 => 1,
                ],
            ],
            ArrayHelper::diff(
                [1, 2, 3],
                ['2', '3', '4'],
                true
            )
        );
    }

    #[Test]
    public function diff_doesNotConvertNewValuesWhenAsIdsIsDisabled(): void
    {
        self::assertSame(
            [
                'diff' => false,
                'add' => [],
                'remove' => [],
            ],
            ArrayHelper::diff([1, 2, 3], ['1', '2', '3'])
        );
    }

    #[Test]
    public function ids_itExtractsIdsFromArrays(): void
    {
        $rows = [
            ['id' => 1, 'name' => 'National'],
            ['id' => 2, 'name' => 'Afrique'],
            ['id' => 3, 'name' => 'Monde'],
        ];

        self::assertSame([1, 2, 3], ArrayHelper::ids($rows));
    }

    #[Test]
    public function ids_itExtractsIdsFromObjects(): void
    {
        $rows = [
            (object) ['id' => 1, 'name' => 'National'],
            (object) ['id' => 2, 'name' => 'Afrique'],
            (object) ['id' => 3, 'name' => 'Monde'],
        ];

        self::assertSame([1, 2, 3], ArrayHelper::ids($rows));
    }

    #[Test]
    public function ids_itExtractsIdsFromMixedArraysAndObjects(): void
    {
        $rows = [
            ['id' => 1],
            (object) ['id' => 2],
            ['id' => 3],
            (object) ['id' => 4],
        ];

        self::assertSame([1, 2, 3, 4], ArrayHelper::ids($rows));
    }

    #[Test]
    public function ids_itUsesACustomKey(): void
    {
        $rows = [
            ['category_id' => 10],
            ['category_id' => 20],
            (object) ['category_id' => 30],
        ];

        self::assertSame(
            [10, 20, 30],
            ArrayHelper::ids($rows, 'category_id')
        );
    }

    #[Test]
    public function ids_itIgnoresMissingKeys(): void
    {
        $rows = [
            ['id' => 1],
            ['name' => 'Afrique'],
            ['id' => 3],
            (object) ['name' => 'Monde'],
        ];

        self::assertSame([1, 3], ArrayHelper::ids($rows));
    }

    #[Test]
    public function ids_itIgnoresNullValues(): void
    {
        $rows = [
            ['id' => 1],
            ['id' => null],
            (object) ['id' => 3],
            (object) ['id' => null],
        ];

        self::assertSame([1, 3], ArrayHelper::ids($rows));
    }

    #[Test]
    public function ids_itPreservesNonNullFalsyValues(): void
    {
        $rows = [
            ['id' => 0],
            ['id' => ''],
            ['id' => false],
        ];

        self::assertSame([0, '', false], ArrayHelper::ids($rows));
    }

    #[Test]
    public function ids_itReturnsAnEmptyArrayForEmptyInput(): void
    {
        self::assertSame([], ArrayHelper::ids([]));
    }

    #[Test]
    public function ids_itReturnsAReindexedList(): void
    {
        $rows = [
            10 => ['id' => 100],
            20 => ['name' => 'Afrique'],
            30 => ['id' => 300],
        ];

        $result = ArrayHelper::ids($rows);

        self::assertSame([100, 300], $result);
        self::assertSame([0, 1], array_keys($result));
    }

    #[Test]
    public function toInt_itConvertsPositiveIntegers(): void
    {
        self::assertSame(
            [123, 456],
            ArrayHelper::toInt([123, 456])
        );
    }

    #[Test]
    public function toInt_itConvertsPositiveIntegerStrings(): void
    {
        self::assertSame(
            [123, 456],
            ArrayHelper::toInt(['123', '456'])
        );
    }

    #[Test]
    public function toInt_itAcceptsZeroWhenOnlyPositiveIsDisabled(): void
    {
        self::assertSame(
            [0, 123],
            ArrayHelper::toInt([0, 123], false)
        );
    }

    #[Test]
    public function toInt_itRejectsZeroWhenOnlyPositiveIsEnabled(): void
    {
        self::assertSame(
            [123],
            ArrayHelper::toInt([0, 123])
        );
    }

    #[Test]
    public function toInt_itAcceptsNegativeIntegersWhenOnlyPositiveIsDisabled(): void
    {
        self::assertSame(
            [-123, 123],
            ArrayHelper::toInt([-123, 123], false)
        );
    }

    #[Test]
    public function toInt_itRejectsNegativeIntegersWhenOnlyPositiveIsEnabled(): void
    {
        self::assertSame(
            [123],
            ArrayHelper::toInt([-123, 123])
        );
    }

    #[Test]
    public function toInt_itAcceptsNegativeIntegerStringsWhenOnlyPositiveIsDisabled(): void
    {
        self::assertSame(
            [-123, 123],
            ArrayHelper::toInt(['-123', '123'], false)
        );
    }

    #[Test]
    public function toInt_itRejectsAnEmptyArrayForInvalidIntegerStrings(): void
    {
        self::assertSame(
            [],
            ArrayHelper::toInt([
                '123sde',
                '12.5',
                '1e3',
                '00',
                '01',
                '0123',
                '00123',
                '+123',
                ' 123',
                '123 ',
                ' 123 ',
                ' ',
                '',
            ], false)
        );
    }

    #[Test]
    public function toInt_itIgnoresNullValues(): void
    {
        self::assertSame(
            [123],
            ArrayHelper::toInt([null, 123], false)
        );
    }

    #[Test]
    public function toInt_itReturnsAnEmptyArrayForNullInput(): void
    {
        self::assertSame(
            [],
            ArrayHelper::toInt(null)
        );
    }

    #[Test]
    public function toInt_itAcceptsScalarInput(): void
    {
        self::assertSame(
            [123],
            ArrayHelper::toInt('123')
        );

        self::assertSame(
            [123],
            ArrayHelper::toInt(123)
        );
    }

    #[Test]
    public function toInt_itChecksDuplicateValues(): void
    {
        self::assertSame(
            [
                [123, 123, 456, 456],
                [123, 456],
            ],
            [
                ArrayHelper::toInt([123, '123', 456, '456'], false),
                ArrayHelper::toInt([123, '123', 456, '456'], false, true)
            ]
        );
    }

    #[Test]
    public function toInt_itReturnsAReindexedList(): void
    {
        self::assertSame(
            [123, 456],
            ArrayHelper::toInt([
                10 => 123,
                20 => 'invalid',
                30 => 456,
            ])
        );

        self::assertSame(
            [0, 1],
            array_keys(
                ArrayHelper::toInt([
                    10 => 123,
                    20 => 456,
                ], false)
            )
        );
    }

    #[Test]
    public function toString_convertsIntegersToStrings(): void
    {
        self::assertSame(
            ['123', '456'],
            ArrayHelper::toString([123, 456])
        );
    }

    #[Test]
    public function toString_acceptsStringValues(): void
    {
        self::assertSame(
            ['foo', 'bar'],
            ArrayHelper::toString(['foo', 'bar'])
        );
    }

    #[Test]
    public function toString_acceptsScalarInput(): void
    {
        self::assertSame(
            ['123'],
            ArrayHelper::toString(123)
        );

        self::assertSame(
            ['123'],
            ArrayHelper::toString('123')
        );
    }

    #[Test]
    public function toString_returnsAnEmptyArrayForNullInput(): void
    {
        self::assertSame(
            [],
            ArrayHelper::toString(null)
        );
    }

    #[Test]
    public function toString_rejectsEmptyValuesByDefault(): void
    {
        self::assertSame(
            ['foo', 'bar'],
            ArrayHelper::toString(['', 'foo', '', 'bar'])
        );
    }

    #[Test]
    public function toString_preservesEmptyValuesWhenRejectEmptyIsDisabled(): void
    {
        self::assertSame(
            ['', 'foo', '', 'bar'],
            ArrayHelper::toString(['', 'foo', '', 'bar'], false)
        );
    }

    #[Test]
    public function toString_rejectsZeroWhenRejectEmptyIsEnabled(): void
    {
        self::assertSame(
            ['123'],
            ArrayHelper::toString([0, 123])
        );
    }

    #[Test]
    public function toString_preservesZeroWhenRejectEmptyIsDisabled(): void
    {
        self::assertSame(
            ['0', '123'],
            ArrayHelper::toString([0, 123], false)
        );
    }

    #[Test]
    public function toString_checksDuplicateValues(): void
    {
        self::assertSame(
            [
                ['123', '123', '456', '456'],
                ['123', '456'],
            ],
            [
                ArrayHelper::toString([123, '123', 456, '456'], false),
                ArrayHelper::toString([123, '123', 456, '456'], false, true),
            ]
        );
    }

    #[Test]
    public function extractImgSrc_extractsImageSources(): void
    {
        $content = <<<'HTML'
        <img src="image-1.jpg">
        <img src="image-2.png">
        <img src="image-3.webp">
        HTML;

        self::assertSame(
            [
                'image-1.jpg',
                'image-2.png',
                'image-3.webp',
            ],
            ArrayHelper::extractImgSrc($content)
        );
    }

    #[Test]
    public function extractImgSrc_supportsSingleAndDoubleQuotes(): void
    {
        $content = <<<'HTML'
        <img src="image-1.jpg">
        <img src='image-2.png'>
        HTML;

        self::assertSame(
            [
                'image-1.jpg',
                'image-2.png',
            ],
            ArrayHelper::extractImgSrc($content)
        );
    }

    #[Test]
    public function extractImgSrc_isCaseInsensitive(): void
    {
        $content = <<<'HTML'
        <IMG SRC="image-1.jpg">
        <Img Src="image-2.png">
        <img src="image-3.webp">
        HTML;

        self::assertSame(
            [
                'image-1.jpg',
                'image-2.png',
                'image-3.webp',
            ],
            ArrayHelper::extractImgSrc($content)
        );
    }

    #[Test]
    public function extractImgSrc_extractsSourcesFromImagesWithOtherAttributes(): void
    {
        $content = <<<'HTML'
        <img class="thumbnail" src="image-1.jpg" alt="Image 1">
        <img src="image-2.png" width="100" height="100">
        <img loading="lazy" src='image-3.webp' class="image">
        HTML;

        self::assertSame(
            [
                'image-1.jpg',
                'image-2.png',
                'image-3.webp',
            ],
            ArrayHelper::extractImgSrc($content)
        );
    }

    #[Test]
    public function extractImgSrc_returnsAnEmptyArrayWhenNoImageIsFound(): void
    {
        self::assertSame(
            [],
            ArrayHelper::extractImgSrc('<p>No image here</p>')
        );
    }

    #[Test]
    public function extractImgSrc_returnsAnEmptyArrayForEmptyContent(): void
    {
        self::assertSame(
            [],
            ArrayHelper::extractImgSrc('')
        );
    }
}
