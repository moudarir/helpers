<?php

declare(strict_types=1);

namespace Moudarir\Helpers\Tests\Unit;

use ArrayIterator;
use InvalidArgumentException;
use Moudarir\Helpers\Collector;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CollectorTest extends TestCase
{

    #[Test]
    public function map_itPreservesKeysAndPassesKeysToCallback(): void
    {
        $items = ['a' => 2, 'b' => 3];

        $result = Collector::map(
            $items,
            static fn (int $value, string $key): string => "$key:$value"
        );

        $this->assertSame(['a' => 'a:2', 'b' => 'b:3'], $result);
    }

    #[Test]
    public function map_returnsEmptyArrayForEmptyInput(): void
    {
        $this->assertSame([], Collector::map([], static fn ($item) => $item));
    }

    #[Test]
    public function filter_withoutCallbackRemovesFalseyValues(): void
    {
        $items = [0 => 0, 1 => '0', 2 => '', 3 => false, 4 => null, 5 => 'x'];

        $this->assertSame([5 => 'x'], Collector::filter($items));
    }

    #[Test]
    public function filter_withCallbackPreservesKeysAndReceivesKeys(): void
    {
        $items = ['a' => 2, 'b' => 3, 'c' => 4];

        $result = Collector::filter(
            $items,
            static fn (int $value, string $key): bool =>
                $value % 2 === 0 && $key !== 'c'
        );

        $this->assertSame(['a' => 2], $result);
    }

    #[Test]
    public function reject_withCallbackKeepsItemsThatFailPredicate(): void
    {
        $items = ['a' => 1, 'b' => 2, 'c' => 3];

        $result = Collector::reject(
            $items,
            static fn (int $value): bool => $value > 1
        );

        $this->assertSame(['a' => 1], $result);
    }

    #[Test]
    public function reject_withValueRemovesMatchingValues(): void
    {
        $this->assertSame(
            [0 => 'a', 2 => 'c'],
            Collector::reject(['a', 'b', 'c'], 'b')
        );
    }

    #[Test]
    public function mapWithKeys_mergesReturnedAssociativePairs(): void
    {
        $result = Collector::mapWithKeys(
            ['a' => 1, 'b' => 2],
            static fn (int $value): array => [
                "value_$value" => $value,
                'last' => $value,
            ]
        );

        $this->assertSame(
            ['value_1' => 1, 'last' => 2, 'value_2' => 2],
            $result
        );
    }

    #[Test]
    public function partition_separatesItemsAndPreservesKeys(): void
    {
        $items = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4];

        $result = Collector::partition(
            $items,
            static fn (int $value): bool => $value % 2 === 0
        );

        $this->assertSame(['b' => 2, 'd' => 4], $result[0]);
        $this->assertSame(['a' => 1, 'c' => 3], $result[1]);
    }

    #[Test]
    public function chunk_splitsItemsAndPreservesKeysWithinChunks(): void
    {
        $items = ['a' => 1, 'b' => 2, 'c' => 3];

        $this->assertSame(
            [['a' => 1, 'b' => 2], ['c' => 3]],
            Collector::chunk($items, 2)
        );
    }

    #[Test]
    public function chunk_returnsEmptyArrayForEmptyInput(): void
    {
        $this->assertSame([], Collector::chunk([], 2));
    }

    #[Test]
    public function groupBy_groupsUsingCallback(): void
    {
        $result = Collector::groupBy(
            [1, 2, 3, 4, 5],
            static fn (int $value): string => $value % 2 === 0 ? 'even' : 'odd'
        );

        $this->assertSame(['odd' => [1, 3, 5], 'even' => [2, 4]], $result);
    }

    #[Test]
    public function groupBy_groupsUsingArrayKey(): void
    {
        $items = [
            ['name' => 'Ada', 'team' => 'red'],
            ['name' => 'Lin', 'team' => 'blue'],
            ['name' => 'Sam', 'team' => 'red'],
        ];

        $result = Collector::groupBy($items, 'team');

        $this->assertSame([$items[0], $items[2]], $result['red']);
        $this->assertSame([$items[1]], $result['blue']);
    }

    #[Test]
    public function groupBy_groupsUsingObjectProperty(): void
    {
        $red = (object) ['name' => 'Ada', 'team' => 'red'];
        $blue = (object) ['name' => 'Lin', 'team' => 'blue'];

        $this->assertSame(
            ['red' => [$red], 'blue' => [$blue]],
            Collector::groupBy([$red, $blue], 'team')
        );
    }

    #[Test]
    public function groupBy_treatsFunctionNamesAsArrayKeys(): void
    {
        $items = [
            ['trim' => 'active', 'id' => 1],
            ['trim' => 'inactive', 'id' => 2],
            ['trim' => 'active', 'id' => 3],
        ];

        $this->assertSame(
            [
                'active' => [
                    ['trim' => 'active', 'id' => 1],
                    ['trim' => 'active', 'id' => 3],
                ],
                'inactive' => [
                    ['trim' => 'inactive', 'id' => 2],
                ],
            ],
            Collector::groupBy($items, 'trim')
        );
    }

    #[Test]
    public function groupBy_acceptsClosureCallbacks(): void
    {
        $items = [
            ['id' => 1, 'active' => true],
            ['id' => 2, 'active' => false],
            ['id' => 3, 'active' => true],
        ];

        $result = Collector::groupBy(
            $items,
            static fn (array $item): string => $item['active'] ? 'active' : 'inactive'
        );

        $this->assertSame(
            [
                'active' => [
                    ['id' => 1, 'active' => true],
                    ['id' => 3, 'active' => true],
                ],
                'inactive' => [
                    ['id' => 2, 'active' => false],
                ],
            ],
            $result
        );
    }

    #[Test]
    public function keyBy_usesCallbackAndAcceptsIterable(): void
    {
        $items = (static function (): iterable {
            yield ['id' => 10, 'name' => 'Ada'];
            yield ['id' => 20, 'name' => 'Lin'];
        })();

        $result = Collector::keyBy(
            $items,
            static fn (array $item): int => $item['id']
        );

        $this->assertSame('Ada', $result[10]['name']);
        $this->assertSame('Lin', $result[20]['name']);
    }

    #[Test]
    public function keyBy_usesArrayKey(): void
    {
        $items = [
            ['id' => 'a', 'name' => 'Ada'],
            ['id' => 'b', 'name' => 'Lin'],
        ];

        $this->assertSame(
            ['a' => $items[0], 'b' => $items[1]],
            Collector::keyBy($items, 'id')
        );
    }

    #[Test]
    public function keyBy_treatsFunctionNamesAsArrayKeys(): void
    {
        $items = [
            ['trim' => 'first', 'id' => 1],
            ['trim' => 'second', 'id' => 2],
        ];

        $this->assertSame(
            [
                'first' => ['trim' => 'first', 'id' => 1],
                'second' => ['trim' => 'second', 'id' => 2],
            ],
            Collector::keyBy($items, 'trim')
        );
    }

    #[Test]
    public function sortBy_sortsValuesAscendingWithoutCallback(): void
    {
        $this->assertSame([1, 2, 3], Collector::sortBy([3, 1, 2]));
    }

    #[Test]
    public function sortBy_sortsByArrayKeyAndCanPreserveKeys(): void
    {
        $items = [
            'first' => ['name' => 'Zoe'],
            'second' => ['name' => 'Ada'],
        ];

        $result = Collector::sortBy($items, 'name', false, true);

        $this->assertSame(['second', 'first'], array_keys($result));
        $this->assertSame('Ada', $result['second']['name']);
    }

    #[Test]
    public function sortBy_sortsByMultipleCallbackCriteria(): void
    {
        $items = [
            ['last' => 'Doe', 'first' => 'John'],
            ['last' => 'Adams', 'first' => 'Sam'],
            ['last' => 'Doe', 'first' => 'Amy'],
        ];

        $result = Collector::sortBy(
            $items,
            static fn (array $item): array => [
                $item['last'],
                $item['first'],
            ]
        );

        $this->assertSame(
            ['Sam', 'Amy', 'John'],
            array_column($result, 'first')
        );
    }

    #[Test]
    public function sortByDesc_sortsValuesDescending(): void
    {
        $this->assertSame([3, 2, 1], Collector::sortByDesc([1, 3, 2]));
    }

    #[Test]
    public function sortKeys_sortsKeysAscending(): void
    {
        $items = ['c' => 3, 'a' => 1, 'b' => 2];

        $this->assertSame(
            ['a' => 1, 'b' => 2, 'c' => 3],
            Collector::sortKeys($items)
        );
    }

    #[Test]
    public function sortKeysDesc_sortsKeysDescending(): void
    {
        $items = ['a' => 1, 'c' => 3, 'b' => 2];

        $this->assertSame(
            ['c' => 3, 'b' => 2, 'a' => 1],
            Collector::sortKeysDesc($items)
        );
    }

    #[Test]
    public function unique_removesDuplicatesAndPreservesKeys(): void
    {
        $items = ['a' => 1, 'b' => 2, 'c' => 1, 'd' => 3];

        $this->assertSame(
            ['a' => 1, 'b' => 2, 'd' => 3],
            Collector::unique($items)
        );
    }

    #[Test]
    public function unique_canReindexResults(): void
    {
        $items = ['a' => 'x', 'b' => 'y', 'c' => 'x'];

        $this->assertSame(['x', 'y'], Collector::unique(
            $items,
            preserveKeys: false
        ));
    }

    #[Test]
    public function unique_removesDuplicatesUsingAnArrayKey(): void
    {
        $items = [
            ['id' => 1, 'name' => 'Ada'],
            ['id' => 2, 'name' => 'Lin'],
            ['id' => 1, 'name' => 'Other'],
        ];

        $result = Collector::unique($items, 'id');

        $this->assertSame([$items[0], $items[1]], array_values($result));
    }

    #[Test]
    public function uniqueStrict_distinguishesTypesForExtractedKeys(): void
    {
        $items = [
            ['id' => 1],
            ['id' => '1'],
            ['id' => 1],
        ];

        $this->assertSame(
            [$items[0], $items[1]],
            array_values(Collector::uniqueStrict($items, 'id'))
        );
    }

    #[Test]
    public function uniqueStrict_distinguishesIntegerAndStringValues(): void
    {
        $this->assertSame(
            [1, '1'],
            array_values(Collector::uniqueStrict([1, '1', 1]))
        );
    }

    #[Test]
    public function min_returnsNullForEmptyInput(): void
    {
        $this->assertNull(Collector::min([]));
    }

    #[Test]
    public function min_returnsValueAndMinimumExtractedValue(): void
    {
        $this->assertSame(2, Collector::min([3, 2, 5]));

        $items = [
            ['score' => 30],
            ['score' => 10],
            ['score' => 20],
        ];

        $this->assertSame(10, Collector::min($items, 'score'));
        $this->assertSame(10, Collector::min(
            $items,
            static fn (array $item): int => $item['score']
        ));
    }

    #[Test]
    public function min_treatsFunctionNamesAsArrayKeys(): void
    {
        $items = [
            ['trim' => 3],
            ['trim' => 1],
            ['trim' => 2],
        ];

        $this->assertSame(1, Collector::min($items, 'trim'));
    }

    #[Test]
    public function max_returnsNullForEmptyInput(): void
    {
        $this->assertNull(Collector::max([]));
    }

    #[Test]
    public function max_returnsValueAndMaximumExtractedValue(): void
    {
        $this->assertSame(5, Collector::max([3, 2, 5]));

        $items = [
            ['score' => 30],
            ['score' => 10],
            ['score' => 20],
        ];

        $this->assertSame(30, Collector::max($items, 'score'));
        $this->assertSame(30, Collector::max(
            $items,
            static fn (array $item): int => $item['score']
        ));
    }

    #[Test]
    public function max_treatsFunctionNamesAsArrayKeys(): void
    {
        $items = [
            ['trim' => 3],
            ['trim' => 1],
            ['trim' => 2],
        ];

        $this->assertSame(3, Collector::max($items, 'trim'));
    }

    #[Test]
    public function collapse_flattensOneLevelAndSkipsScalars(): void
    {
        $items = [
            ['a' => 1],
            'ignored',
            ['b' => 2],
            [3, 4],
        ];

        $this->assertSame(
            ['a' => 1, 'b' => 2, 0 => 3, 1 => 4],
            Collector::collapse($items)
        );
    }

    #[Test]
    public function collapse_acceptsTraversableValues(): void
    {
        $items = [
            new ArrayIterator(['a' => 1]),
            new ArrayIterator(['b' => 2]),
        ];

        $this->assertSame(
            ['a' => 1, 'b' => 2],
            Collector::collapse($items)
        );
    }

    #[Test]
    public function flatten_flattensToSpecifiedDepth(): void
    {
        $items = [1, [2, [3, [4]]]];

        $this->assertSame(
            [1, 2, [3, [4]]],
            Collector::flatten($items, 1)
        );
        $this->assertSame(
            [1, 2, 3, [4]],
            Collector::flatten($items, 2)
        );
        $this->assertSame([1, 2, 3, 4], Collector::flatten($items));
    }

    #[Test]
    public function flatten_acceptsTraversableValues(): void
    {
        $items = [
            new ArrayIterator([1, [2]]),
            [3],
        ];

        $this->assertSame([1, 2, 3], Collector::flatten($items));
    }

    #[Test]
    public function random_returnsRequestedItemsAsList(): void
    {
        $items = ['a' => 'red', 'b' => 'green', 'c' => 'blue'];
        $result = Collector::random($items, 2);

        $this->assertCount(2, $result);
        $this->assertTrue(array_is_list($result));

        foreach ($result as $value) {
            $this->assertContains($value, $items);
        }
    }

    #[Test]
    public function random_canPreserveOriginalKeys(): void
    {
        $items = ['a' => 'red', 'b' => 'green', 'c' => 'blue'];
        $result = Collector::random($items, 2, true);

        $this->assertCount(2, $result);

        foreach ($result as $key => $value) {
            $this->assertArrayHasKey($key, $items);
            $this->assertSame($items[$key], $value);
        }
    }

    #[Test]
    public function random_returnsSingleItemWhenNumberIsNull(): void
    {
        $items = ['red', 'green', 'blue'];

        $this->assertContains(Collector::random($items), $items);
    }

    #[Test]
    public function randomly_returnsSingleItemWhenNumberIsNull(): void
    {
        $items = ['red', 'green', 'blue'];

        $this->assertContains(Collector::randomly($items), $items);
    }

    #[Test]
    public function randomly_returnsRequestedCount(): void
    {
        $items = ['a' => 'red', 'b' => 'green', 'c' => 'blue'];
        $result = Collector::randomly($items, 2);

        $this->assertCount(2, $result);
        $this->assertTrue(array_is_list($result));

        foreach ($result as $value) {
            $this->assertContains($value, $items);
        }
    }

    #[Test]
    public function randomly_returnsNullForEmptyInputWithoutCount(): void
    {
        $this->assertNull(Collector::randomly([]));
    }

    #[Test]
    public function randomly_throwsWhenRequestExceedsItemCount(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Collector::randomly(['a', 'b'], 3);
    }

    #[Test]
    public function randomly_returnsEmptyArrayWhenCountIsZero(): void
    {
        $this->assertSame([], Collector::randomly(['a', 'b'], 0));
    }

    #[Test]
    public function pop_returnsAndRemovesLastItem(): void
    {
        $items = ['a' => 1, 'b' => 2];

        $this->assertSame(2, Collector::pop($items));
        $this->assertSame(['a' => 1], $items);
    }

    #[Test]
    public function pop_returnsMultipleItemsInOriginalOrder(): void
    {
        $items = [1, 2, 3, 4];

        $this->assertSame([3, 4], Collector::pop($items, 2));
        $this->assertSame([1, 2], $items);
    }

    #[Test]
    public function pop_returnsNullForEmptyInputOrNonPositiveCount(): void
    {
        $items = [];

        $this->assertNull(Collector::pop($items));
        $this->assertNull(Collector::pop($items, 0));
        $this->assertNull(Collector::pop($items, -1));
    }

    #[Test]
    public function first_returnsFirstItemOrConfiguredDefault(): void
    {
        $this->assertSame('a', Collector::first(['a', 'b']));
        $this->assertSame(
            'fallback',
            Collector::first([], default: 'fallback')
        );
    }

    #[Test]
    public function first_findsItemAndPassesItsKeyToCallback(): void
    {
        $items = ['a' => 2, 'b' => 5, 'c' => 8];

        $result = Collector::first(
            $items,
            static fn (int $value, string $key): bool =>
                $value > 4 && $key === 'b'
        );

        $this->assertSame(5, $result);
    }

    #[Test]
    public function first_evaluatesCallableDefaultWhenNoItemMatches(): void
    {
        $this->assertSame(
            'fallback',
            Collector::first(
                [1, 2],
                static fn (int $value): bool => $value > 5,
                static fn (): string => 'fallback'
            )
        );
    }

    #[Test]
    public function last_returnsLastItemOrConfiguredDefault(): void
    {
        $this->assertSame('b', Collector::last(['a', 'b']));
        $this->assertSame('fallback', Collector::last([], default: 'fallback'));
    }

    #[Test]
    public function last_findsLastItemMatchingCallback(): void
    {
        $items = ['a' => 2, 'b' => 4, 'c' => 6, 'd' => 7];

        $result = Collector::last(
            $items,
            static fn (int $value): bool => $value % 2 === 0
        );

        $this->assertSame(6, $result);
    }

    #[Test]
    public function implode_joinsScalarValuesWithSeparator(): void
    {
        $this->assertSame('a|b|c', Collector::implode(['a', 'b', 'c'], '|'));
        $this->assertSame('abc', Collector::implode(['a', 'b', 'c']));
        $this->assertSame('', Collector::implode([], '|'));
    }

    #[Test]
    public function implode_extractsValuesUsingCallbackOrArrayKey(): void
    {
        $items = [
            ['name' => 'Ada'],
            ['name' => 'Lin'],
        ];

        $this->assertSame(
            'Ada, Lin',
            Collector::implode($items, 'name', ', ')
        );
        $this->assertSame(
            'Ada | Lin',
            Collector::implode(
                $items,
                static fn (array $item): string => $item['name'],
                ' | '
            )
        );
    }

    #[Test]
    public function implode_treatsFunctionNamesAsArrayKeys(): void
    {
        $items = [
            ['trim' => 'Alice'],
            ['trim' => 'Bob'],
        ];

        $this->assertSame(
            'Alice, Bob',
            Collector::implode($items, 'trim', ', ')
        );
    }

    #[Test]
    public function join_usesSeparateFinalGlue(): void
    {
        $this->assertSame(
            'red, green and blue',
            Collector::join(['red', 'green', 'blue'], ', ', ' and ')
        );
        $this->assertSame('red and green', Collector::join(
            ['red', 'green'],
            ', ',
            ' and '
        ));
    }

    #[Test]
    public function join_handlesEmptyAndSingleItemInputs(): void
    {
        $this->assertSame('', Collector::join([]));
        $this->assertSame('only', Collector::join(['only']));
    }

    #[Test]
    public function pluck_extractsValuesFromArraysAndObjects(): void
    {
        $items = [
            ['name' => 'Ada'],
            (object) ['name' => 'Lin'],
        ];

        $this->assertSame(['Ada', 'Lin'], Collector::pluck($items, 'name'));
    }

    #[Test]
    public function pluck_canUseAnExtractedValueAsTheResultKey(): void
    {
        $items = [
            ['id' => 10, 'name' => 'Ada'],
            (object) ['id' => 20, 'name' => 'Lin'],
        ];

        $this->assertSame(
            [10 => 'Ada', 20 => 'Lin'],
            Collector::pluck($items, 'name', 'id')
        );
    }

    #[Test]
    public function every_checksTruthinessAndTreatsEmptyInputAsTrue(): void
    {
        $this->assertTrue(Collector::every([]));
        $this->assertTrue(Collector::every([1, 'yes', true]));
        $this->assertFalse(Collector::every([1, 0, true]));
    }

    #[Test]
    public function every_checksCallbackAndArrayKeyOrObjectProperty(): void
    {
        $items = ['a' => 2, 'b' => 4];

        $this->assertTrue(Collector::every(
            $items,
            static fn (int $value, string $key): bool =>
                $value % 2 === 0 && $key !== ''
        ));

        $this->assertTrue(Collector::every([
            ['active' => true],
            ['active' => 1],
        ], 'active'));

        $this->assertFalse(Collector::every([
            (object) ['active' => true],
            (object) ['active' => false],
        ], 'active'));
    }

    #[Test]
    public function every_treatsFunctionNamesAsArrayKeys(): void
    {
        $this->assertTrue(
            Collector::every(
                [
                    ['trim' => true],
                    ['trim' => 1],
                ],
                'trim'
            )
        );

        $this->assertFalse(
            Collector::every(
                [
                    ['trim' => true],
                    ['trim' => false],
                ],
                'trim'
            )
        );
    }

    #[Test]
    public function flip_swapsArrayValuesAndKeys(): void
    {
        $this->assertSame(
            ['red' => 0, 'green' => 1],
            Collector::flip(['red', 'green'])
        );
    }

    #[Test]
    public function where_filtersUsingValuesAndKeys(): void
    {
        $items = ['a' => 1, 'b' => 2, 'c' => 3];

        $this->assertSame(
            ['b' => 2, 'c' => 3],
            Collector::where(
                $items,
                static fn (int $value, string $key): bool =>
                    $value > 1 && $key !== 'a'
            )
        );
    }
}
