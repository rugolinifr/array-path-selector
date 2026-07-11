<?php

declare(strict_types=1);

namespace Rugolinifr\ArrayPathSelector\Factory;

use Rugolinifr\ArrayPathSelector\Contract\FlattenerInterface;
use Rugolinifr\ArrayPathSelector\Implementation\Flattener;

class PathToolFactory
{
    public function createFlattener(): FlattenerInterface
    {
        return new Flattener();
    }
}
