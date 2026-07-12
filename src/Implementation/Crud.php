<?php

declare(strict_types=1);

namespace Rugolinifr\ArrayPathSelector\Implementation;

use Rugolinifr\ArrayPathSelector\Contract\CrudInterface;

class Crud implements CrudInterface
{

    public function put(array &$array, string $path, mixed $value): void
    {
        $splitPath = $this->createSplitPath($path);
        $targetArray = &$array;
        $lastProperty = array_pop($splitPath);
        foreach ($splitPath as $subPath) {
            if (!is_array($targetArray[$subPath] ?? null)) {
                $targetArray[$subPath] = [];
            }
            $targetArray = &$targetArray[$subPath];
        }
        $targetArray[$lastProperty] = $value; //@phpstan-ignore offsetAccess.invalidOffset
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
