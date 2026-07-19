<?php

declare(strict_types=1);

namespace Rugolinifr\ArrayPathSelector\Contract;


interface CrudInterface
{

    /**
     * Returns the value found in the given array at the given path.
     *
     * The given array must not contain any key having the dot character or integers wrapped in squared brackets.
     *
     * @param array<int|string, mixed> $array the array to get a value from.
     * @param string $path the path of the value to get, eg 'name' or 'address.zip' or 'cars[0].brand'.
     * @return mixed any value.
     *
     * @throws ValueNotFoundException when the given path leads to a missing value.
     */
    public function get(array $array, string $path): mixed;

    /**
     * Creates or replaces the given value into the given array.
     *
     * `put()` forces its way along the property path. While traversing:
     * - if an intermediate key is missing, or if its value is not an array,
     *   it is created/overwritten as an empty array to continue building the path.
     * - if it encounters an existing array, it simply traverses it.
     *
     * The given array must not contain any key having the dot character or integers wrapped in squared brackets.
     *
     * @param array<int|string, mixed> $array the array to put a value into.
     * @param string $path the path of the value to create or replace, eg "name" or "address.zip" or "cars[0].brand".
     * The empty string will map itself to the given value at the root of the given array.
     * @param mixed $value any value.
     */
    public function put(array &$array, string $path, mixed $value): void;

    /**
     * Deletes the given value at the given path from the given array.
     *
     * Does nothing when the path does not exist.
     *
     * The given array must not contain any key having the dot character or integers wrapped in squared brackets.
     *
     * @param array<int|string, mixed> $array the array to put a value into.
     * @param string $path the path of the value to remove, eg 'name' or 'address.zip' or 'cars[0].brand'.
     *  The empty string remove the value at the root of the given array having the empty string as a key.
     */
    public function delete(array &$array, string $path): void;
}
