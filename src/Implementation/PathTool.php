<?php

declare(strict_types=1);

namespace Rugolinifr\ArrayPathSelector\Implementation;

use Rugolinifr\ArrayPathSelector\Contract\PathToolInterface;

class PathTool implements PathToolInterface
{

    public function flattenAsKeyValue(array $array): array
    {
       return $array;
    }
}
