<?php

declare(strict_types=1);

namespace Rugolinifr\ArrayPathSelector\Tests\Contract;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Rugolinifr\ArrayPathSelector\Contract\CrudInterface;
use Rugolinifr\ArrayPathSelector\Contract\ValueNotFoundException;
use Rugolinifr\ArrayPathSelector\Factory\PathToolFactory;
use Throwable;

class CrudGetTest extends TestCase
{
    private CrudInterface $crud;
    private mixed $result = null;
    private ?Throwable $exception = null;

    /**
     * @param array<int|string, mixed> $array
     */
    #[DataProvider('provideGetData')]
    public function testGet(
        array $array,
        string $path,
        mixed $expectedValue,
    ): void {
        $this->givenIHaveACrud();
        $this->whenIGetValueInArrayAtPath($array, $path);
        $this->thenIGetExpectedValue($expectedValue);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function provideGetData(): array
    {
        return [
            'gets value from root' => self::getValueFromRoot(),
            'gets value from nested - dot notation' => self::getValueFromNestedDotNotation(),
            'gets value from nested - bracket notation' => self::getValueFromNestedBracketNotation(),
            'gets value from complex mixed notation' => self::getValueFromComplexMixedNotation(),
            'gets value from an empty path' => self::getValueFromEmptyString(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function getValueFromRoot(): array
    {
        return [
            'array' => [
                'name' => 'john',
                'age' => 30,
            ],
            'path' => 'name',
            'expectedValue' => 'john',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function getValueFromNestedDotNotation(): array
    {
        return [
            'array' => [
                'user' => [
                    'email' => 'john@example.com',
                    'age' => 25,
                ],
            ],
            'path' => 'user.age',
            'expectedValue' => 25,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function getValueFromNestedBracketNotation(): array
    {
        return [
            'array' => [
                'cars' => [
                    ['brand' => 'Ford'],
                    ['brand' => 'Tesla'],
                ],
            ],
            'path' => 'cars[1].brand',
            'expectedValue' => 'Tesla',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function getValueFromComplexMixedNotation(): array
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
            'path' => 'nested.again.tags[0]',
            'expectedValue' => 'admin',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function getValueFromEmptyString(): array
    {
        return [
            'array' => [
                '' => 'valueForEmptyString',
                'other' => 'keepMe',
            ],
            'path' => '',
            'expectedValue' => 'valueForEmptyString',
        ];
    }

    /**
     * @param array<int|string, mixed> $array
     */
    #[DataProvider('provideNotFoundValue')]
    public function testGetNotFound(array $array, string $path): void
    {
        $this->givenIHaveACrud();
        $this->whenIGetValueInArrayAtPath($array, $path);
        $this->thenIGetExpectedException();
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function provideNotFoundValue(): array
    {
        return [
            'not found value in empty array' => [
                'array' => [],
                'path' => 'name',
            ],
            'not found value in filled array' => [
                'array' => [
                    'name' => 'alan',
                    'country' => [
                        'continent' => 'Europe',
                    ]
                ],
                'path' => 'country[0]'
            ],
            'not found value even if property exist in previous path 1/2' => [
                'array' => [
                    'name' => 'alan',
                    'country' => [
                        'continent' => 'Europe',
                    ]
                ],
                'path' => 'country[0].continent',  // /!\ "continent" exists in "country"
            ],
            'not found value even if property exist in previous path 2/2' => [
                'array' => [
                    'name' => 'alan',
                    'country' => [
                        'continent' => 'Europe',
                    ]
                ],
                'path' => 'country.data.continent', // /!\ "continent" exists in "country"
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
    private function whenIGetValueInArrayAtPath(array $array, string $path): void
    {
        try {
            $this->result = $this->crud->get($array, $path);
        } catch (ValueNotFoundException $e) {
            $this->exception = $e;
        }
    }

    private function thenIGetExpectedValue(mixed $expectedValue): void
    {
        self::assertNull(
            $this->exception,
            'The crud tool did not return but throw an exception: ' . $this->exception?->getMessage(),
        );
        $this->assertSame(
            $expectedValue,
            $this->result,
            'The crud tool did not get the expected value at the given path.'
        );
    }

    private function thenIGetExpectedException(): void
    {
        self::assertNull(
            $this->result,
            'The crud tool did not throw but returned a result.',
        );
        $this->assertInstanceOf(
            ValueNotFoundException::class,
            $this->exception,
            'The crud tool did not throw the expected exception type.'
        );
    }
}
