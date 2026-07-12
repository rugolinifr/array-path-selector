<?php

declare(strict_types=1);

namespace Rugolinifr\ArrayPathSelector\Implementation;

use Rugolinifr\ArrayPathSelector\Contract\CrudInterface;

class Crud implements CrudInterface
{

    public function put(array &$array, string $path, mixed $value): void
    {
        $splitPath = $this->createSplitPath($path);
        $this->putRecursively($array, $splitPath, $value);
    }

    /**
     * @param array<int|string, mixed> $array
     * @param string[] $splitPath
     */
    private function putRecursively(
        array &$array,
        array $splitPath,
        mixed $value,
    ): void {
        $length = count($splitPath);
        $targetArray = &$array;
        foreach ($splitPath as $index => $subPath) {
            if ($index === $length - 1) {
                $targetArray[$subPath] = $value;
            } elseif (key_exists($subPath, $targetArray) && is_array($targetArray[$subPath])) {
                $targetArray = &$targetArray[$subPath];
            } else {
                $targetArray[$subPath] = [];
                $targetArray = &$targetArray[$subPath];
            }
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
}
