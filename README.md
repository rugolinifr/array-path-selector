# Array path selector

This package provides an API to flatten nested PHP arrays to map their values to a path selector,
do the opposite operation or replace any value in the array.

## Installation

Run the following command:

```shell
composer require rugolinifr/array-path-selector
```

## Examples:

Instantiate the API object:

```php
$pathTool = (new Rugolinifr\ArrayPathSelector\Factory\PathToolFactory())->createPathTool();
```

Then create an array (looking like a regular JSON object in the following example):

```php
$array = [
    'name' => 'John Doe',
    'address' => [
        'city' => 'Nice',
        'zips' => [
            '06000',
            '06100',
        ],
        'street' => 'Promenade des anglais',
    ],
    'birth' => new DateTimeImmutable('1980-01-01 12:00:00'),
    'kids' => [],
];
```

Then flatten an array according to the "key/value" model:

```php
// flatten an array following the "key/value" model:
$keyValues = $pathTool->flattenAsKeyValues($array);
$keyValues === [
    'name' => 'John Doe',
    'address.city' => 'Nice',
    'address.zips[0]' => '06000',
    'address.zips[1]' => '06100',
    'address.street' => 'Promenade des anglais',
    'birth' => new DateTimeImmutable('1980-01-01 12:00:00'),
    'kids' => [], //empty arrays have their own entry, otherwise they would disappear from the flattened array
];
```

Or following the "pairing" model:

```php
$pairs = $pathTool->flattenAsPairs($array);
$keyValues === [
    0 => ['name', 'John Doe'],
    1 => ['address.city', 'Nice'],
    2 => ['address.zips[0]', '06000'],
    3 => ['address.zips[1]', '06100'],
    4 => ['address.street', 'Promenade des anglais'],
    5 => ['birth', new DateTimeImmutable('1980-01-01 12:00:00')],
    6 => ['kids', []], //empty arrays have their own entry, otherwise they would disappear from the flattened array
];
```

The "key/value" model is the more natural approach,
perfect to test whether a path existence or find a value,
whereas the "pairing" model suits better when comparing the subparts of two (or more) arrays is wanted,
as sequential numeric keys are easier to follow than a recursive comparison algorithm.
