<?php

declare(strict_types=1);

namespace Rugolinifr\ArrayPathSelector\Contract;

interface CrudInterface
{
    /**
     * Creates or replaces the given value into the given array.
     *
     * Creates new arrays along the property path when they're missing,
     * or add new entries to them when they already exist.
     *
     * The given array must not contain any key having the dot character or integers wrapped in squared brackets.
     *
     * @param array<int|string, mixed> $array the array to put a value into.
     * @param string $path the path of the value to create or replace, eg "name" or "address.zip" or "cars[0].brand".
     * The empty string will map itself to the given value at the root of the given array.
     * @param mixed $value any value.
     *
     */
    public function put(array &$array, string $path, mixed $value): void;

    /**
     * Deletes the given value at the given path from the given array.
     *
     * Does nothing when the path does not exist.
     *
     * @param array<int|string, mixed> $array the array to put a value into.
     * @param string $path the path of the value to remove, eg 'name' or 'address.zip' or 'cars[0].brand'.
     *  The empty string remove the value at the root of the given array having the empty string as a key.
     */
    public function delete(array &$array, string $path): void;
}
