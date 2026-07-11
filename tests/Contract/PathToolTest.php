<?php

declare(strict_types=1);

namespace Rugolinifr\ArrayPathSelector\Tests\Contract;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Rugolinifr\ArrayPathSelector\Contract\PathToolInterface;
use Rugolinifr\ArrayPathSelector\Factory\PathToolFactory;
use stdClass;

class PathToolTest extends TestCase
{
    private PathToolInterface $pathTool;
    /** @var array<int|string, mixed>  */
    private array $result;

    /**
     * @param array<int|string, mixed> $array
     * @param array<string, mixed> $expectedArray
     */
    #[DataProvider('provideFlattenArrayData')]
    public function testFlattenAsKeyValue(
        array $array,
        array $expectedArray,
    ): void {
        $this->givenIHaveAPathTool();
        $this->whenIFlattenArrayAsKeyValue($array);
        $this->thenIGetExpectedFlattenArray($expectedArray);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function provideFlattenArrayData(): array
    {
        $object = new stdClass();
        return [
            'empty array becomes an empty array' => [
                'array' => [],
                'expectedArray' => [],
            ],
            'array with direct property stays the same' => [
                'array' => [
                    'string' => 'john',
                    'int' => 25,
                    'object' => $object,
                    'bool' => true,
                    'float' => 0.25,
                ],
                'expectedArray' => [
                    'string' => 'john',
                    'int' => 25,
                    'object' => $object,
                    'bool' => true,
                    'float' => 0.25,
                ],
            ],
        ];
    }

    private function givenIHaveAPathTool(): void
    {
        $this->pathTool = (new PathToolFactory())->createPathTool();
    }

    /**
     * @param array<int|string, mixed> $array
     */
    private function whenIFlattenArrayAsKeyValue(array $array): void
    {
        $this->result = $this->pathTool->flattenAsKeyValue($array);
    }

    /**
     * @param array<int|string, mixed> $expectedArray
     */
    private function thenIGetExpectedFlattenArray(array $expectedArray): void
    {
        $this->assertSame(
            $expectedArray,
            $this->result,
            "The path tool did not return the expected flatten array."
        );
    }
}
