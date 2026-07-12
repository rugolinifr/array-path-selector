<?php

declare(strict_types=1);

namespace Rugolinifr\ArrayPathSelector\Tests\Contract;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Rugolinifr\ArrayPathSelector\Contract\CrudInterface;
use Rugolinifr\ArrayPathSelector\Factory\PathToolFactory;
use stdClass;

class CrudPutTest extends TestCase
{
    private CrudInterface $crud;

    /**
     * @param array<int|string, mixed> $array
     * @param array<int|string, mixed> $expectedArray
     */
    #[DataProvider('providePutData')]
    public function testPut(
        array $array,
        string $path,
        mixed $value,
        array $expectedArray,
    ): void {
        $this->givenIHaveACrud();
        $this->whenIPutValueInArrayAtPath($array, $path, $value);
        $this->thenIGetExpectedArray($expectedArray, $array);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function providePutData(): array
    {
        $object = new stdClass();
        return [
            'creates value into root' => self::createValueIntoRoot(),
            'creates value into not existing nested ' => self::createValueIntoNotExistingNested(),
            'replaces value into root' => self::replaceValueIntoRoot(),
            'put value inside nested array with dot notation' => self::putNestedDotNotation(),
            'put value inside nested array with index notation' => self::putNestedIndexNotation($object),
            'put value into complex mixed notation' => self::putComplexMixedNotation(),
            'put value into an empty path' => self::putValueIntoEmptyString(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function createValueIntoRoot(): array
    {
        return [
            'array' => [],
            'path' => 'name',
            'value' => 'john',
            'expectedArray' => [
                'name' => 'john',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function createValueIntoNotExistingNested(): array
    {
        return [
            'array' => [],
            'path' => 'address.zip',
            'value' => '75001',
            'expectedArray' => [
                'address' => [
                    'zip' => '75001',
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function replaceValueIntoRoot(): array
    {
        return [
            'array' => [
                'name' => 'doe',
            ],
            'path' => 'name',
            'value' => 'john',
            'expectedArray' => [
                'name' => 'john',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function putNestedDotNotation(): array
    {
        return [
            'array' => [
                'user' => [
                    'email' => 'john@example.com',
                ],
            ],
            'path' => 'user.age',
            'value' => 25,
            'expectedArray' => [
                'user' => [
                    'email' => 'john@example.com',
                    'age' => 25,
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function putNestedIndexNotation(stdClass $object): array
    {
        return [
            'array' => [
                'cars' => [
                    ['brand' => 'Ford'],
                ],
            ],
            'path' => 'cars[0].owner',
            'value' => $object,
            'expectedArray' => [
                'cars' => [
                    [
                        'brand' => 'Ford',
                        'owner' => $object,
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function putComplexMixedNotation(): array
    {
        return [
            'array' => [
                'nested' => [
                    'again' => [
                        'name' => 'john',
                    ],
                ],
            ],
            'path' => 'nested.again.tags[0]',
            'value' => 'admin',
            'expectedArray' => [
                'nested' => [
                    'again' => [
                        'name' => 'john',
                        'tags' => [
                            'admin',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function putValueIntoEmptyString(): array
    {
        return [
            'array' => [],
            'path' => '',
            'value' => 'valueForEmptyString',
            'expectedArray' => [
                '' => 'valueForEmptyString',
            ],
        ];
    }

    private function givenIHaveACrud(): void
    {
         $this->crud = (new PathToolFactory())->createCrud();
    }

    /**
     * @param array<int|string, mixed> $array
     */
    private function whenIPutValueInArrayAtPath(array &$array, string $path, mixed $value): void
    {
        $this->crud->put($array, $path, $value);
    }

    /**
     * @param array<int|string, mixed> $expectedArray
     * @param array<int|string, mixed> $actualArray
     */
    private function thenIGetExpectedArray(array $expectedArray, array $actualArray): void
    {
        $this->assertSame(
            $expectedArray,
            $actualArray,
            'The crud tool did not put the value at the expected path.'
        );
    }
}
