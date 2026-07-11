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
            'empty array becomes an empty array' => self::emptyArray(),
            'array as JSON object stays the same' => self::jsonObject($object),
            'array as JSON array use squared brackets' => self::jsonArray($object),
            'array with nested JSON object uses dot notation' => self::nestedJsonObject($object),
            'array with nested JSON array uses squared brackets' => self::nestedJsonArray($object),
            'array with nested JSON object|array mixed notation' => self::nestedJsonObjectOrArray($object),
            'array with empty nested array' => self::nestedEmptyArray(),
            'array with depth 2' => self::depth2($object),
        ];
    }


    /**
     * @return array<string, mixed>
     */
    private static function emptyArray(): array
    {
        return [
            'array' => [],
            'expectedArray' => [],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function jsonObject(stdClass $object): array
    {
        return [
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
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function jsonArray(stdClass $object): array
    {
        return [
            'array' => [
                'john',
                25,
                $object,
                true,
                0.25,
            ],
            'expectedArray' => [
                '[0]' => 'john',
                '[1]' => 25,
                '[2]' => $object,
                '[3]' => true,
                '[4]' => 0.25,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function nestedJsonObject(stdClass $object): array
    {
        return [
            'array' => [
                'nested' => [
                    'string' => 'john',
                    'int' => 25,
                    'object' => $object,
                    'bool' => true,
                    'float' => 0.25,
                ],
            ],
            'expectedArray' => [
                'nested.string' => 'john',
                'nested.int' => 25,
                'nested.object' => $object,
                'nested.bool' => true,
                'nested.float' => 0.25,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function nestedJsonArray(stdClass $object): array
    {
        return [
            'array' => [
                'nested' => [
                    'john',
                    25,
                    $object,
                    true,
                    0.25,
                ],
            ],
            'expectedArray' => [
                'nested[0]' => 'john',
                'nested[1]' => 25,
                'nested[2]' => $object,
                'nested[3]' => true,
                'nested[4]' => 0.25,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function nestedJsonObjectOrArray(stdClass $object): array
    {
        return [
            'array' => [
                'nested' => [
                    'john',
                    25,
                    'object' => $object,
                    true,
                    'float' => 0.25,
                ],
            ],
            'expectedArray' => [
                'nested[0]' => 'john',
                'nested[1]' => 25,
                'nested.object' => $object,
                'nested[2]' => true,
                'nested.float' => 0.25,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function nestedEmptyArray(): array
    {
        return [
            'array' => [
                'nested' => [],
                'full' => [
                    'name' => 'john',
                ]
            ],
            'expectedArray' => [
                'nested' => [],
                'full.name' => 'john',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function depth2(stdClass $object): array
    {
        return [
            'array' => [
                'nested' => [
                    'again' => [
                        'name' => 'john',
                        'int' => 25,
                    ],
                    [
                        'object' => $object,
                        'bool' => true,
                        0.25,
                    ],
                ],
            ],
            'expectedArray' => [
                'nested.again.name' => 'john',
                'nested.again.int' => 25,
                'nested[0].object' => $object,
                'nested[0].bool' => true,
                'nested[0][0]' => 0.25,
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
