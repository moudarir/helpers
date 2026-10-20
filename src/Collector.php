<?php

declare(strict_types=1);

namespace Moudarir\Helpers;

use InvalidArgumentException;
use Random\Randomizer;
use Stringable;
use Traversable;

/**
 * @template TKey of array-key
 * @template TValue
 * @template TMapValue
 * @template TFirstDefault
 * @template TLastDefault
 */
final class Collector
{

    /**
     * Run a map over each of the items.
     *
     * @param list<TKey, TValue> $items
     * @param callable(TValue, TKey): TMapValue $callback
     * @return list<TKey, TMapValue>|array
     */
    public static function map(array $items, callable $callback): array
    {
        if ($items === []) {
            return [];
        }

        $result = [];

        foreach ($items as $key => $value) {
            $result[$key] = $callback($value, $key);
        }

        return $result;
    }

    /**
     * Run an associative map over each of the items.
     *
     * The callback should return an associative array with a single key/value pair.
     *
     * @template TMapWithKeysKey of array-key
     * @template TMapWithKeysValue
     *
     * @param list<TKey, TValue> $items
     * @param callable(TValue, TKey): array<TMapWithKeysKey, TMapWithKeysValue> $callback
     * @return list<TMapWithKeysKey, TMapWithKeysValue>|array
     */
    public static function mapWithKeys(array $items, callable $callback): array
    {
        if (empty($items)) {
            return [];
        }

        $result = [];

        foreach ($items as $key => $value) {
            $assoc = $callback($value, $key);

            foreach ($assoc as $mapKey => $mapValue) {
                $result[$mapKey] = $mapValue;
            }
        }

        return $result;
    }

    /**
     * Run a filter over each of the items.
     *
     * @param list<TKey, TValue> $items
     * @param (callable(TValue, TKey): bool)|null $callback
     * @return list<TKey, TMapValue>|array
     */
    public static function filter(array $items, ?callable $callback = null): array
    {
        if (empty($items)) {
            return [];
        }

        if ($callback !== null) {
            return self::where($items, $callback);
        }

        return array_filter($items);
    }

    /**
     * Create a collection of all elements that do not pass a given truth test.
     *
     * @param list<TKey, TValue> $items
     * @param (callable(TValue, TKey): bool)|bool|TValue $callback
     * @return list<TKey, TMapValue>|array
     */
    public static function reject(array $items, $callback = true): array
    {
        if (empty($items)) {
            return [];
        }

        $useAsCallable = self::useAsCallable($callback);

        return self::filter(
            $items,
            function ($value, $key) use ($callback, $useAsCallable) {
                return $useAsCallable
                    ? ! $callback($value, $key)
                    : $value != $callback;
            }
        );
    }

    /**
     * Splits an array into two based on a predicate.
     *
     * @param list<TKey, TValue> $items
     * @param callable(TValue, TKey): bool $predicate
     * @return array{list<TKey, TValue>, list<TKey, TValue>}
     */
    public static function partition(array $items, callable $predicate): array
    {
        $matches = [];
        $noMatches = [];

        foreach ($items as $key => $value) {
            if ($predicate($value, $key)) {
                $matches[$key] = $value;
            } else {
                $noMatches[$key] = $value;
            }
        }

        return [$matches, $noMatches];
    }

    /**
     * @param array<TKey, TValue> $items
     * @param int $length
     * @return list<int, array<TKey, TValue>>
     */
    public static function chunk(array $items, int $length): array
    {
        if ($length <= 0) {
            return $items;
        }

        return array_chunk($items, $length, true);
    }

    /**
     * Group items by an array key, object property, or callback.
     * String values are treated as keys or property names.
     *
     * @param array<TKey, TValue> $items
     * @param (callable(TValue, TKey): TKey)|string $groupBy
     * @return array<TKey, list<TValue>>
     */
    public static function groupBy(array $items, callable|string $groupBy): array
    {
        $result = [];
        $isCallable = self::useAsCallable($groupBy);

        foreach ($items as $key => $item) {
            $groupKey = match (true) {
                $isCallable => $groupBy($item, $key),
                is_array($item) => $item[$groupBy] ?? null,
                is_object($item) => $item->{$groupBy} ?? null,
                default => null,
            };

            $result[$groupKey ?? ''][] = $item;
        }

        return $result;
    }

    /**
     * Re-index items by an array key, object property, or callback.
     * String values are treated as keys or property names.
     *
     * @param iterable<TKey, TValue> $items
     * @param (callable(TValue, TKey): array-key)|string $keyBy
     * @return array<TKey, TValue>
     */
    public static function keyBy(iterable $items, callable|string $keyBy): array
    {
        $result = [];
        $isCallable = self::useAsCallable($keyBy);

        foreach ($items as $key => $item) {
            $newKey = match (true) {
                $isCallable => $keyBy($item, $key),
                is_array($item) => $item[$keyBy] ?? null,
                is_object($item) => $item->{$keyBy} ?? null,
                default => null,
            };

            $result[$newKey ?? ''] = $item;
        }

        return $result;
    }

    /**
     * Sort items by a key, property, or callback.
     * Supports multi-criteria sorting when the callback returns an array.
     *
     * @param array<TKey, TValue> $items
     * @param (callable(TValue): mixed)|string|null $callback
     * @param bool $descending
     * @param bool $preserveKeys
     * @return array<TKey, TValue>
     */
    public static function sortBy(
        array $items,
        callable|string|null $callback = null,
        bool $descending = false,
        bool $preserveKeys = false
    ): array
    {
        if ($items === []) {
            return [];
        }

        if ($callback === null) {
            if ($preserveKeys === true) {
                $descending === true ? arsort($items) : asort($items);
            } else {
                $descending === true ? rsort($items) : sort($items);
            }

            return $items;
        }

        $valueExtractor = self::useAsCallable($callback)
            ? $callback
            : static function (mixed $item) use ($callback): mixed {
                if (is_array($item)) {
                    return $item[$callback] ?? null;
                } elseif (is_object($item) && property_exists($item, $callback)) {
                    return $item->{$callback};
                }

                return $item;
            };

        $sortFunc = $preserveKeys ? 'uasort' : 'usort';

        $sortFunc($items, static function (mixed $a, mixed $b) use ($valueExtractor, $descending): int {
            $valueA = $valueExtractor($a);
            $valueB = $valueExtractor($b);

            if (is_array($valueA) && is_array($valueB)) {
                foreach ($valueA as $key => $valA) {
                    $valB = $valueB[$key] ?? null;
                    $comparison = $valA <=> $valB;

                    if ($comparison !== 0) {
                        return $descending ? -$comparison : $comparison;
                    }
                }

                return 0;
            }

            $comparison = $valueA <=> $valueB;

            return $descending ? -$comparison : $comparison;
        });

        return $items;
    }

    /**
     * Sort items by descending.
     *
     * @param array<TKey, TValue> $items
     * @param (callable(TValue): mixed)|string|null $callback
     * @param bool $preserveKeys
     * @return array<TKey, TValue>
     */
    public static function sortByDesc(
        array $items,
        callable|string|null $callback = null,
        bool $preserveKeys = false
    ): array
    {
        return self::sortBy($items, $callback, true, $preserveKeys);
    }

    /**
     * Sort the collection keys.
     *
     * @param list<TKey, TValue> $items
     * @param int $flags
     * @param bool $descending
     * @return list<TKey, TValue>
     */
    public static function sortKeys(array $items, int $flags = SORT_REGULAR, bool $descending = false): array
    {
        if (empty($items)) {
            return [];
        }

        $descending ? krsort($items, $flags) : ksort($items, $flags);

        return $items;
    }

    /**
     * Sort the collection keys in descending order.
     *
     * @param list<TKey, TValue> $items
     * @param  int  $flags
     * @return list<TKey, TValue>
     */
    public static function sortKeysDesc(array $items, int $flags = SORT_REGULAR): array
    {
        return self::sortKeys($items, $flags, true);
    }

    /**
     * Filter unique items by key, object property, or callback.
     *
     * @param array<TKey, TValue> $items
     * @param (callable(TValue, TKey): mixed)|string|null $key
     * @param bool $strict
     * @param bool $preserveKeys
     * @return array<TKey, TValue>
     */
    public static function unique(
        array $items,
        callable|string|null $key = null,
        bool $strict = false,
        bool $preserveKeys = true
    ): array
    {
        if ($key === null) {
            if ($strict === true) {
                $result = [];

                foreach ($items as $itemKey => $item) {
                    if (in_array($item, $result, true) === false) {
                        $result[$itemKey] = $item;
                    }
                }
            } else {
                $result = array_unique($items, SORT_REGULAR);
            }

            return $preserveKeys === true ? $result : array_values($result);
        }

        $seen = [];
        $result = [];
        $isCallable = self::useAsCallable($key);

        foreach ($items as $itemKey => $item) {
            $id = match (true) {
                $isCallable => $key($item, $itemKey),
                is_array($item) => $item[$key] ?? null,
                is_object($item) => $item->{$key} ?? null,
                default => $item,
            };

            if (in_array($id, $seen, $strict) === false) {
                $seen[] = $id;

                if ($preserveKeys === true) {
                    $result[$itemKey] = $item;
                } else {
                    $result[] = $item;
                }
            }
        }

        return $result;
    }

    /**
     * Return unique items using strict comparisons.
     *
     * @param array<TKey, TValue> $items
     * @param (callable(TValue, TKey): mixed)|string|null $key
     * @param bool $preserveKeys
     * @return array<TKey, TValue>
     */
    public static function uniqueStrict(
        array $items,
        callable|string|null $key = null,
        bool $preserveKeys = true
    ): array
    {
        return self::unique($items, $key, true, $preserveKeys);
    }

    /**
     * Return the minimum value or the minimum extracted value.
     * String values are treated as keys or property names.
     *
     * @param array<TKey, TValue> $items
     * @param (callable(TValue): mixed)|string|null $key
     * @return mixed
     */
    public static function min(array $items, callable|string|null $key = null): mixed
    {
        if ($items === []) {
            return null;
        }

        if ($key === null) {
            return min($items);
        }

        $min = null;
        $isCallable = self::useAsCallable($key);

        foreach ($items as $item) {
            $value = match (true) {
                $isCallable => $key($item),
                is_array($item) => $item[$key] ?? null,
                is_object($item) => $item->{$key} ?? null,
                default => $item,
            };

            if ($value === null) {
                continue;
            }

            if ($min === null || $value < $min) {
                $min = $value;
            }
        }

        return $min;
    }

    /**
     * Return the maximum value or the maximum extracted value.
     * String values are treated as keys or property names.
     *
     * @param array<TKey, TValue> $items
     * @param (callable(TValue): mixed)|string|null $key
     * @return mixed
     */
    public static function max(array $items, callable|string|null $key = null): mixed
    {
        if ($items === []) {
            return null;
        }

        if ($key === null) {
            return max($items);
        }

        $max = null;
        $isCallable = self::useAsCallable($key);

        foreach ($items as $item) {
            $value = match (true) {
                $isCallable => $key($item),
                is_array($item) => $item[$key] ?? null,
                is_object($item) => $item->{$key} ?? null,
                default => $item,
            };

            if ($value === null) {
                continue;
            }

            if ($max === null || $value > $max) {
                $max = $value;
            }
        }

        return $max;
    }

    /**
     * Flattens an iterable of single-level subarrays/iterables.
     *
     * @param iterable $items
     * @return list<int, mixed>
     */
    public static function collapse(iterable $items): array
    {
        $results = [];

        foreach ($items as $values) {
            if ($values instanceof Traversable) {
                $values = iterator_to_array($values);
            } elseif (!is_array($values)) {
                continue;
            }

            $results[] = $values;
        }

        return array_merge([], ...$results);
    }

    /**
     * Flattens a multidimensional array or iterable by one level or to the specified depth.
     *
     * @param iterable $items
     * @param int|float $depth
     * @return list<int, mixed>
     */
    public static function flatten(iterable $items, int|float $depth = INF): array
    {
        $result = [];
        $depth = max(1, $depth);

        foreach ($items as $item) {
            // Alternative native à $item instanceof Collection ? $item->all() : $item
            if ($item instanceof Traversable) {
                $item = iterator_to_array($item);
            }

            if (!is_array($item)) {
                $result[] = $item;
            } else {
                $values = $depth === 1
                    ? array_values($item)
                    : self::flatten($item, $depth - 1);

                foreach ($values as $value) {
                    $result[] = $value;
                }
            }
        }

        return $result;
    }

    /**
     * Get one or a specified number of random items.
     *
     * @param array<TKey, TValue> $items
     * @param int|null $number
     * @param bool $preserveKeys
     * @return TValue|array<TKey, TValue>|null
     */
    public static function random(array $items, ?int $number = null, bool $preserveKeys = false): mixed
    {
        return self::randomly($items, $number, $preserveKeys);
    }

    /**
     * Get and remove the last N items from the array.
     *
     * @param array<TKey, TValue> $items
     * @param int $numToGet
     * @return TValue|list<TValue>|null
     */
    public static function pop(array &$items, int $numToGet = 1): mixed
    {
        if ($numToGet < 1) {
            return null;
        }

        if (($count = count($items)) === 0) {
            return null;
        }

        if ($numToGet === 1) {
            return array_pop($items);
        }

        $start = max(0, $count - $numToGet);
        $results = array_slice($items, $start);
        $items = array_slice($items, 0, $start, true);

        return array_values($results);
    }

    /**
     * Retourne le premier élément du tableau (ou le premier qui satisfait le prédicat).
     *
     * @param list<TKey, TValue> $items
     * @param (callable(TValue, TKey): bool)|null $callback
     * @param TFirstDefault|TValue|(callable(): TFirstDefault)|null $default
     * @return TValue|TFirstDefault
     */
    public static function first(array $items, ?callable $callback = null, mixed $default = null): mixed
    {
        if ($callback === null) {
            if ($items === []) {
                return is_callable($default) ? $default() : $default;
            }

            return reset($items); // ou $items[array_key_first($items)]
        }

        foreach ($items as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }

        return is_callable($default) ? $default() : $default;
    }

    /**
     * Return the last element in an array passing a given truth test.
     *
     * @param list<TKey, TValue> $items
     * @param (callable(TValue, TKey): bool)|null $callback
     * @param TLastDefault|(callable(): TLastDefault) $default
     * @return TValue|TLastDefault
     */
    public static function last(array $items, ?callable $callback = null, mixed $default = null): mixed
    {
        if ($callback === null) {
            if (empty($items)) {
                return is_callable($default) ? $default() : $default;
            }

            return end($items); // ou $items[array_key_last($items)]
        }

        return self::first(array_reverse($items, true), $callback, $default);
    }

    /**
     * Join items using a separator, key, property, or callback.
     *
     * When $glue is null and $value is not callable, $value is the
     * separator passed to the native implode() function.
     *
     * @param array<TKey, TValue> $items
     * @param (callable(TValue): string)|string|null $value
     * @param string|null $glue
     * @return string
     */
    public static function implode(array $items, callable|string|null $value = null, ?string $glue = null): string
    {
        if ($items === []) {
            return '';
        }

        $isCallable = self::useAsCallable($value);

        if ($glue === null && $isCallable === false) {
            return implode($value ?? '', $items);
        }

        $separator = $glue ?? '';
        $values = [];

        foreach ($items as $item) {
            $values[] = match (true) {
                $isCallable => $value($item),
                is_string($value) && is_array($item) => $item[$value] ?? '',
                is_string($value) && is_object($item) => $item->{$value} ?? '',
                default => (string) $item,
            };
        }

        return implode($separator, $values);
    }

    /**
     * Join all items from the collection using a string.
     * The final items can use a separate glue string.
     *
     * @param list<TKey, TValue> $items
     * @param string $glue
     * @param string $finalGlue
     * @return string
     */
    public static function join(array $items, string $glue = ', ', string $finalGlue = ''): string
    {
        if (empty($items)) {
            return '';
        }

        $count = count($items);

        if ($count === 1) {
            return (string) reset($items);
        }

        if ($finalGlue === '') {
            return implode($glue, $items);
        }

        $lastItem = array_pop($items);

        return implode($glue, $items) . $finalGlue . $lastItem;
    }

    /**
     * Extract values from an iterable of arrays or objects.
     *
     * Missing values are returned as null.
     * When $value is null, each original item is returned.
     * Items with missing or invalid result keys are skipped when $key is provided.
     *
     * @param iterable<TKey, TValue> $items
     * @param string|int|null $value
     * @param string|null $key
     * @return array<array-key, mixed>
     */
    public static function pluck(
        iterable $items,
        string|int|null $value,
        ?string $key = null
    ): array
    {
        $results = [];

        foreach ($items as $item) {
            $itemValue = match (true) {
                $value === null => $item,
                is_array($item) => $item[$value] ?? null,
                is_object($item) => $item->{$value} ?? null,
                default => null,
            };

            // If the key is "null", we will just append the value to the array and keep
            // looping. Otherwise, we will key the array using the value of the key we
            // received from the developer. Then we'll return the final array form.
            if ($key === null) {
                $results[] = $itemValue;

                continue;
            }

            $itemKey = match (true) {
                is_array($item) => $item[$key] ?? null,
                is_object($item) => $item->{$key} ?? null,
                default => null,
            };

            if ($itemKey instanceof Stringable) {
                $itemKey = (string) $itemKey;
            }

            if (is_int($itemKey) === false && is_string($itemKey) === false) {
                continue;
            }

            $results[$itemKey] = $itemValue;
        }

        return $results;
    }

    /**
     * Checks whether every item satisfies a condition.
     * String values are treated as keys or property names.
     *
     * @param array<TKey, TValue> $items
     * @param (callable(TValue, TKey): bool)|string|null $callback
     * @return bool
     */
    public static function every(array $items, callable|string|null $callback = null): bool
    {
        if ($items === []) {
            return true;
        }

        if ($callback === null) {
            return array_all($items, static fn (mixed $value): bool => (bool) $value);
        }

        if (self::useAsCallable($callback)) {
            return array_all(
                $items,
                static fn (mixed $value, int|string $key): bool => (bool) $callback($value, $key)
            );
        }

        return array_all($items, static function (mixed $item) use ($callback): bool {
            if (is_array($item)) {
                return (bool) ($item[$callback] ?? false);
            }

            if (is_object($item)) {
                return (bool) ($item->{$callback} ?? false);
            }

            return false;
        });
    }

    /**
     * Flip the items in the collection.
     *
     * @param array<TKey, TValue> $items
     * @return array<TValue, TKey>
     */
    public static function flip(array $items): array
    {
        return array_flip($items);
    }

    /**
     * Filter the array using the given callback.
     *
     * @param array $items
     * @param callable $callback
     * @return array
     */
    public static function where(array $items, callable $callback): array
    {
        return array_filter($items, $callback, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * Get one or a specified number of random values.
     *
     * @param array<TKey, TValue> $array
     * @param int|null $number
     * @param bool $preserveKeys
     * @return TValue|array<TKey, TValue>|null
     *
     * @throws InvalidArgumentException
     */
    public static function randomly(array $array, ?int $number = null, bool $preserveKeys = false): mixed
    {
        if ($array === [] && $number === null) {
            return null;
        }

        if ($number !== null && $number <= 0) {
            return [];
        }

        $requested = $number ?? 1;
        $count = count($array);

        if ($requested > $count) {
            throw new InvalidArgumentException(
                "You requested $requested items, but there are only $count items available."
            );
        }

        $keys = new Randomizer()->pickArrayKeys($array, $requested);

        if ($number === null) {
            return $array[$keys[0]];
        }

        $results = [];

        if ($preserveKeys === true) {
            foreach ($keys as $key) {
                $results[$key] = $array[$key];
            }
        } else {
            foreach ($keys as $key) {
                $results[] = $array[$key];
            }
        }

        return $results;
    }

    /**
     * Determine if the given value is callable, but not a string.
     *
     * @param mixed $value
     * @return bool
     */
    private static function useAsCallable(mixed $value): bool
    {
        return !is_string($value) && is_callable($value);
    }
}
