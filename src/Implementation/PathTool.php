<?php

declare(strict_types=1);

namespace Rugolinifr\ArrayPathSelector\Implementation;

use Closure;
use Rugolinifr\ArrayPathSelector\Contract\PathToolInterface;

class PathTool implements PathToolInterface
{

    public function flattenAsKeyValue(array $array): array
    {
        $resultFiller = function (array &$array, string $key, mixed $value): void {
            $array[$key] = $value;
        };
        return $this->flatten($array, $resultFiller); //@phpstan-ignore return.type
    }

    public function flattenAsPairs(array $array): array
    {
        $resultFiller = function (array &$array, string $key, mixed $value): void {
            $array[] = [$key, $value];
        };
        return $this->flatten($array, $resultFiller); //@phpstan-ignore return.type
    }

    /**
     * @param array<int|string, mixed> $array
     * @param Closure(array<int|string, mixed>&, string, mixed):void $resultFiller
     * @return array<int|string, mixed>
     */
    private function flatten(
        array $array,
        Closure $resultFiller,
    ): array {
        $result = [];
        $this->flattenRecursively($array, $result, '', $resultFiller);
        return $result;
    }

    /**
     * @param array<int|string, mixed> $array
     * @param array<int|string, mixed> $result
     * @param Closure(array<int|string, mixed>&, string, mixed):void $resultFiller
     */
    private function flattenRecursively(
        array $array,
        array &$result,
        string $prefixKey,
        Closure $resultFiller,
    ): void {
        foreach ($array as $key => $value) {
            $nextKey = $this->createNextKey($key, $prefixKey);
            if (!is_array($value) || empty($value)) {
                $resultFiller($result, $nextKey, $value);
            } else {
                $this->flattenRecursively($value, $result, $nextKey, $resultFiller);
            }
        }
    }

    private function createNextKey(int|string $key, string $prefixKey): string
    {
        return is_int($key) ? "{$prefixKey}[$key]" : trim("$prefixKey.$key", '.');
    }
}
