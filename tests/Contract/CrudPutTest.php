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
        return [
            'creates value into root' => self::createValueIntoRoot(),
            'replaces value into root' => self::replaceValueIntoRoot(),

            'creates value into not existing nested - dot notation' => self::createValueIntoNotExistingNestedDotNotation(),

            'creates value into existing nested - dot notation' => self::createValueIntoExistingNestedDotNotation(),
            'replaces value into existing nested - dot notation' => self::replaceValueIntoExistingNestedDotNotation(),

            'creates value into existing nested - bracket notation' => self::createValueIntoExistingNestedBracketNotation(),
            'replaces value into existing nested - bracket notation' => self::replaceValueIntoExistingNestedBracketNotation(),

            'creates value into complex mixed notation' => self::createValueIntoComplexMixedNotation(),

            'creates value into an empty path' => self::createValueIntoEmptyString(),
            'replaces value into an empty path' => self::replaceValueIntoEmptyString(),
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
    private static function createValueIntoNotExistingNestedDotNotation(): array
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
    private static function createValueIntoExistingNestedDotNotation(): array
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
    private static function replaceValueIntoExistingNestedDotNotation(): array
    {
        return [
            'array' => [
                'user' => [
                    'email' => 'old@example.com',
                ],
            ],
            'path' => 'user.email',
            'value' => 'john@example.com',
            'expectedArray' => [
                'user' => [
                    'email' => 'john@example.com',
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function createValueIntoExistingNestedBracketNotation(): array
    {
        $object = new stdClass();
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
    private static function replaceValueIntoExistingNestedBracketNotation(): array
    {
        return [
            'array' => [
                'cars' => [
                    ['brand' => 'Ford'],
                ],
            ],
            'path' => 'cars[0].brand',
            'value' => 'Tesla',
            'expectedArray' => [
                'cars' => [
                    ['brand' => 'Tesla'],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function createValueIntoComplexMixedNotation(): array
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
    private static function createValueIntoEmptyString(): array
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

    /**
     * @return array<string, mixed>
     */
    private static function replaceValueIntoEmptyString(): array
    {
        return [
            'array' => [
                '' => 'oldValue'
            ],
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
