<?php

declare(strict_types=1);

namespace Rugolinifr\ArrayPathSelector\Tests\Contract;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Rugolinifr\ArrayPathSelector\Contract\CrudInterface;
use Rugolinifr\ArrayPathSelector\Factory\PathToolFactory;

class CrudDeleteTest extends TestCase
{
    private CrudInterface $crud;

    /**
     * @param array<int|string, mixed> $array
     * @param array<int|string, mixed> $expectedArray
     */
    #[DataProvider('provideDeleteData')]
    public function testDelete(
        array $array,
        string $path,
        array $expectedArray,
    ): void {
        $this->givenIHaveACrud();
        $this->whenIDeleteValueInArrayAtPath($array, $path);
        $this->thenIGetExpectedArray($expectedArray, $array);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function provideDeleteData(): array
    {
        return [
            'deletes value from root' => self::deleteValueFromRoot(),
            'deletes value from nested - dot notation' => self::deleteValueFromNestedDotNotation(),
            'deletes value from nested - bracket notation' => self::deleteValueFromNestedBracketNotation(),
            'deletes value from complex mixed notation' => self::deleteValueFromComplexMixedNotation(),
            'deletes value from an empty path' => self::deleteValueFromEmptyString(),
            'deletes a null value' => self::deleteNullValue(),
            'does nothing when path does not exist' => self::doesNothingWhenPathDoesNotExist(),
            'does nothing when nested path does not exist' => self::doesNothingWhenNestedPathDoesNotExist(),
            'does not delete property having same name in array at previous path' => self::doesNotDeleteSamePropertyAtPreviousPath(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function deleteValueFromRoot(): array
    {
        return [
            'array' => [
                'name' => 'john',
                'age' => 30,
            ],
            'path' => 'name',
            'expectedArray' => [
                'age' => 30,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function deleteValueFromNestedDotNotation(): array
    {
        return [
            'array' => [
                'user' => [
                    'email' => 'john@example.com',
                    'age' => 25,
                ],
            ],
            'path' => 'user.age',
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
    private static function deleteValueFromNestedBracketNotation(): array
    {
        return [
            'array' => [
                'cars' => [
                    ['brand' => 'Ford'],
                    ['brand' => 'Tesla'],
                ],
            ],
            'path' => 'cars[0]',
            'expectedArray' => [
                'cars' => [
                    1 => ['brand' => 'Tesla'],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function deleteValueFromComplexMixedNotation(): array
    {
        return [
            'array' => [
                'nested' => [
                    'again' => [
                        'name' => 'john',
                        'tags' => [
                            'admin',
                            'editor',
                        ],
                    ],
                ],
            ],
            'path' => 'nested.again.tags[1]',
            'expectedArray' => [
                'nested' => [
                    'again' => [
                        'name' => 'john',
                        'tags' => [
                            0 => 'admin',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function deleteValueFromEmptyString(): array
    {
        return [
            'array' => [
                '' => 'valueForEmptyString',
                'other' => 'keepMe',
            ],
            'path' => '',
            'expectedArray' => [
                'other' => 'keepMe',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function deleteNullValue(): array
    {
        return [
            'array' => [
                'other' => 'keepMe',
                'isNull' => null,
            ],
            'path' => 'isNull',
            'expectedArray' => [
                'other' => 'keepMe',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function doesNothingWhenPathDoesNotExist(): array
    {
        return [
            'array' => [
                'name' => 'john',
            ],
            'path' => 'age',
            'expectedArray' => [
                'name' => 'john',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function doesNothingWhenNestedPathDoesNotExist(): array
    {
        return [
            'array' => [
                'user' => [
                    'name' => 'john',
                ],
            ],
            'path' => 'user.address.zip',
            'expectedArray' => [
                'user' => [
                    'name' => 'john',
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function doesNotDeleteSamePropertyAtPreviousPath(): array
    {
        return [
            'array' => [
                'aaa' => [
                    'bbb' => 'alice',
                    'ccc' => 'bob'
                ],
            ],
            'path' => 'aaa.ddd.bbb', // /!\ "bbb" exist in "aaa"
            'expectedArray' => [
                'aaa' => [
                    'bbb' => 'alice',
                    'ccc' => 'bob'
                ],
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
    private function whenIDeleteValueInArrayAtPath(array &$array, string $path): void
    {
        $this->crud->delete($array, $path);
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
            'The crud tool did not delete the value at the expected path correctly.'
        );
    }
}
