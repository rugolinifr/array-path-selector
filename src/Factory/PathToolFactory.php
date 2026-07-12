<?php

declare(strict_types=1);

namespace Rugolinifr\ArrayPathSelector\Factory;


use Rugolinifr\ArrayPathSelector\Contract\CrudInterface;
use Rugolinifr\ArrayPathSelector\Contract\FlattenerInterface;
use Rugolinifr\ArrayPathSelector\Implementation\Crud;
use Rugolinifr\ArrayPathSelector\Implementation\Flattener;

class PathToolFactory
{
    public function createFlattener(): FlattenerInterface
    {
        return new Flattener();
    }

    public function createCrud(): CrudInterface
    {
        return new Crud();
    }
}
