# Array path selector

This package provides an API to flatten nested PHP arrays to map their values to a path selector,
do the opposite operation or replace any value in the array.

## Installation

Run the following command:

```shell
composer require rugolinifr/array-path-selector
```

## Flattener examples

Instantiate the API object:

```php
$flattener = (new Rugolinifr\ArrayPathSelector\Factory\PathToolFactory())->createFlattener();
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
$keyValues = $flattener->flattenAsKeyValues($array);
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
$pairs = $flattener->flattenAsPairs($array);
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

## Crud examples

Instantiate the API object:

```php
$crud = (new Rugolinifr\ArrayPathSelector\Factory\PathToolFactory())->createCrud();
```

Then use it to manipulate arrays:

```php
$array = [
    'name' => 'John Doe',
    'kids' => [],
];

// Create new arrays along the property path:
$crud->put($array, 'address.city', 'Nice');
$crud->put($array, 'address.zips[0]', '06000');

$array === [
    'name' => 'John Doe',
    'kids' => [],
    'address' => [
        'city' => 'Nice',
        'zips' => [
            '06000',
        ],
    ],
];

// Get a value:
$crud->get($array, 'name') === 'Jane Doe';

// Replace a value:
$crud->put($array, 'name', 'Jane Doe');

// Delete a value:
$crud->delete($array, 'kids');
$crud->delete($array, 'address.zips[0]');

$array === [
    'name' => 'Jane Doe',
    'address' => [
        'city' => 'Nice',
        'zips' => [],
    ],
];
```

## Known limitations

Arrays having keys using the dot character of wrapping integers with squared brackets will conflicts with this
API.

## For maintainers

The project is shipped with a development image:

```shell
docker compose up -d --build --force-recreate
```

Use it to test the project:

```shell
docker compose exec php phpstan analyze
docker compose exec php vendor/bin/phpunit tests
```
