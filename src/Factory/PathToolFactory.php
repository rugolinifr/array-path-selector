<?php

declare(strict_types=1);

namespace Rugolinifr\ArrayPathSelector\Factory;

use Rugolinifr\ArrayPathSelector\Contract\PathToolInterface;
use Rugolinifr\ArrayPathSelector\Implementation\PathTool;

class PathToolFactory
{
    public function createPathTool(): PathToolInterface
    {
        return new PathTool();
    }
}
