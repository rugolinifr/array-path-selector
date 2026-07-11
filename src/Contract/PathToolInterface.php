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
    public function flattenAsKeyValues(array $array): array;

    /**
     * Flattens the given array.
     *
     * The returned array is a 1-D array whose keys are sequential integers mapped to an array of two elements,
     * the first being a property path and the second the value.
     *
     * @param array<int|string, mixed> $array
     * @return array<int, array<int, array{0: string, 1: mixed}>>
     */
    public function flattenAsPairs(array $array): array;
}
