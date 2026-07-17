<?php

declare(strict_types=1);

namespace Rugolinifr\ArrayPathSelector\Implementation;

use Rugolinifr\ArrayPathSelector\Contract\FlattenerInterface;

class Flattener implements FlattenerInterface
{

    public function flattenAsKeyValues(array $array): array
    {
        return $this->flatten($array);
    }

    public function flattenAsPairs(array $array): array
    {
        $result = [];
        $flattened = $this->flatten($array);
        foreach ($flattened as $key => $value) {
            $result[] = [$key, $value];
        }
        return $result; //@phpstan-ignore return.type
    }

    /**
     * @param array<int|string, mixed> $array
     * @return array<string, mixed>
     */
    private function flatten(
        array $array,
    ): array {
        $result = [];
        $this->flattenRecursively($array, $result, '');
        return $result;
    }

    /**
     * @param array<int|string, mixed> $array
     * @param array<string, mixed> $result
     */
    private function flattenRecursively(
        array $array,
        array &$result,
        string $prefixKey,
    ): void {
        foreach ($array as $key => $value) {
            $nextKey = $this->createNextKey($key, $prefixKey);
            if (!is_array($value) || empty($value)) {
                $result[$nextKey] = $value;
            } else {
                $this->flattenRecursively($value, $result, $nextKey);
            }
        }
    }

    private function createNextKey(int|string $key, string $prefixKey): string
    {
        return is_int($key) ? "{$prefixKey}[$key]" : trim("$prefixKey.$key", '.');
    }
}
