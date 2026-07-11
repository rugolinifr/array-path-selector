<?php

declare(strict_types=1);

namespace Rugolinifr\ArrayPathSelector\Contract;

interface PathToolInterface
{
    /**
     * Flattens the given array.
     *
     * The returned array is a 1-D array whose values are mapped to their property paths.
     *
     * @param array<int|string, mixed> $array
     * @return array<string, mixed>
     */
    public function flattenAsKeyValue(array $array): array;
}
