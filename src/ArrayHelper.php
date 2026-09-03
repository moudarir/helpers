<?php

declare(strict_types=1);

namespace Moudarir\Helpers;

final class ArrayHelper
{

    /**
     * @return array{diff: bool, add: array, remove: array}
     */
    public static function diff(array $default, array $new, bool $asIds = false): array
    {
        if ($asIds === true) {
            $new = array_map(fn ($id) => (int)$id, $new);
        }

        $remove = array_diff($default, $new);
        $add = array_diff($new, $default);

        return [
            'diff' => !empty($remove) || !empty($add),
            'add' => $add,
            'remove' => $remove
        ];
    }

    /**
     * @param list<array|object> $rows
     * @param string $key
     * @return list<mixed>
     */
    public static function ids(array $rows, string $key = 'id'): array
    {
        $ids = [];

        foreach ($rows as $item) {
            if (!is_array($item) && !is_object($item)) {
                continue;
            }

            $value = is_array($item)
                ? ($item[$key] ?? null)
                : ($item->$key ?? null);

            if ($value !== null) {
                $ids[] = $value;
            }
        }

        return $ids;
    }

    /**
     * @param array|int|string|null $data
     * @param bool $onlyPositive
     * @param bool $unique
     * @return list<int>
     */
    public static function toInt(array|int|string|null $data, bool $onlyPositive = true, bool $unique = false): array
    {
        if ($data === null) {
            return [];
        }

        $data = is_array($data) ? $data : [$data];
        $result = [];

        foreach ($data as $value) {
            if (is_int($value)) {
                $intValue = $value;
            } elseif (
                is_string($value)
                && preg_match('/^-?(?:0|[1-9]\d*)$/D', $value) === 1
            ) {
                $intValue = (int)$value;
            } else {
                continue;
            }

            if ($onlyPositive === true && $intValue <= 0) {
                continue;
            }

            $result[] = $intValue;
        }

        if ($unique === true) {
            $result = array_values(array_unique($result));
        }

        return $result;
    }

    /**
     * @param array|int|string|null $data
     * @param bool $rejectEmpty
     * @param bool $unique
     * @return list<string>
     */
    public static function toString(array|int|string|null $data, bool $rejectEmpty = true, bool $unique = false): array
    {
        if ($data === null) {
            return [];
        }

        $data = is_array($data) ? $data : [$data];
        $result = [];

        foreach ($data as $value) {
            $value = (string)$value;

            if ($rejectEmpty === true && empty($value)) {
                continue;
            }

            $result[] = $value;
        }


        if ($unique === true) {
            $result = array_values(array_unique($result));
        }

        return $result;
    }

    public static function extractImgSrc(string $content): array
    {
        if (preg_match_all('~<img[^>]+src=["\']([^"\']+)["\']~i', $content, $matches) === false) {
            return [];
        }

        return $matches[1] ?? [];
    }
}
