<?php

declare(strict_types=1);

namespace Rugolinifr\ArrayPathSelector\Implementation;

use Rugolinifr\ArrayPathSelector\Contract\CrudInterface;
use RuntimeException;

class Crud implements CrudInterface
{

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
        $splitPath = $this->createSplitPath($path);
        $targetArray = &$array;
        $lastProperty = $this->pop($splitPath);
        foreach ($splitPath as $subPath) {
            if (!is_array($targetArray[$subPath] ?? null)) {
                break;
            }
            $targetArray = &$targetArray[$subPath];
        }
        if (key_exists($lastProperty, $targetArray)) {
            unset($targetArray[$lastProperty]);
        }
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
}
