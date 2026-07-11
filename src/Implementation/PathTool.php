<?php

declare(strict_types=1);

namespace Rugolinifr\ArrayPathSelector\Implementation;

use Rugolinifr\ArrayPathSelector\Contract\PathToolInterface;

class PathTool implements PathToolInterface
{

    public function flattenAsKeyValue(array $array): array
    {
        $result = [];
        $this->flattenRecursively($array, $result, '');
        return $result;
    }

    /**
     * @param array<int|string, mixed> $array
     * @param array<int|string, mixed> $result
     */
    private function flattenRecursively(
        array $array,
        array &$result,
        string $prefixKey
    ): void {
        foreach ($array as $key => $value) {
            $nextKey = is_int($key) ? "{$prefixKey}[$key]" : trim("$prefixKey.$key", '.');
            if (is_array($value)) {
                $this->flattenRecursively($value, $result, $nextKey);
            } else {
                $result[$nextKey] = $value;
            }
        }
    }
}
