<?php

declare(strict_types=1);

namespace Rugolinifr\ArrayPathSelector\Tests\Contract;

use PHPUnit\Framework\TestCase;
use Rugolinifr\ArrayPathSelector\Contract\PathToolInterface;
use Rugolinifr\ArrayPathSelector\Factory\PathToolFactory;

abstract class AbstractTestPathTool extends TestCase
{
    protected PathToolInterface $pathTool;
    /** @var array<int|string, mixed>  */
    protected array $result;

    /**
     * @param array<int|string, mixed> $expectedArray
     */
    protected function thenIGetExpectedFlattenArray(array $expectedArray): void
    {
        $this->assertSame(
            $expectedArray,
            $this->result,
            'The path tool did not return the expected flatten array.'
        );
    }

    protected function givenIHaveAPathTool(): void
    {
        $this->pathTool = (new PathToolFactory())->createPathTool();
    }
}
