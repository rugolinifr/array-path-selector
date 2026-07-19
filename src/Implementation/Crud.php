<?php

declare(strict_types=1);

namespace Rugolinifr\ArrayPathSelector\Implementation;

use Closure;
use Rugolinifr\ArrayPathSelector\Contract\ValueNotFoundException;
use Rugolinifr\ArrayPathSelector\Contract\CrudInterface;
use RuntimeException;
use stdClass;

class Crud implements CrudInterface
{
    public function get(array $array, string $path): mixed
    {
        $container = new stdClass();
        $action = function (array $lastArray, string $lastProperty) use ($container): void {
            $container->result = $lastArray[$lastProperty];
        };
        $this->searchAndStopWhenPathNotExist($array, $path, $action);
        if (!property_exists($container, 'result')) {
            throw new ValueNotFoundException("There is no value at path \"$path\".");
        }
        return $container->result;
    }

    public function put(array &$array, string $path, mixed $value): void
    {
        $splitPath = $this->createSplitPath($path);
        $targetArray = &$array;
        $lastProperty = $this->pop($splitPath);
        foreach ($splitPath as $subPath) {
            if (!is_array($targetArray[$subPath] ?? null)) {
                $targetArray[$subPath] = [];
            }
            $targetArray = &$targetArray[$subPath];
        }
        $targetArray[$lastProperty] = $value;
    }

    public function delete(array &$array, string $path): void
    {
        $action = function (array &$lastArray, string $lastPath): void {
            unset($lastArray[$lastPath]);
        };
        $this->searchAndStopWhenPathNotExist($array, $path, $action);
    }

    /**
     * @return array<int, string>
     */
    private function createSplitPath(string $path): array
    {
        $dotPath = preg_replace('/\[([0-9])+\]/', '.\1', $path);
        return explode('.', $dotPath); //@phpstan-ignore argument.type
    }

    /**
     * @param string[] $splitPath
     */
    private function pop(array &$splitPath): string
    {
        $lastProperty = array_pop($splitPath);
        if (null === $lastProperty) {
            throw new RuntimeException("The split path array is empty. Something is broken.");
        }
        return $lastProperty;
    }

    /**
     * @param array<int|string, mixed> $array
     * @param Closure(array&, string): void $action
     */
    private function searchAndStopWhenPathNotExist(
        array &$array,
        string $path,
        Closure $action,
    ): void {
        $splitPath = $this->createSplitPath($path);
        $targetArray = &$array;
        $lastProperty = $this->pop($splitPath);
        $found = true;
        foreach ($splitPath as $subPath) {
            if (!is_array($targetArray[$subPath] ?? null)) {
                $found = false;
                break;
            }
            $targetArray = &$targetArray[$subPath];
        }
        if ($found && key_exists($lastProperty, $targetArray)) {
            $action($targetArray, $lastProperty);
        }
    }
}
