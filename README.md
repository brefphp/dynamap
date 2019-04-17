DynamoDB object mapper. Like Doctrine or Eloquent, but for DynamoDB.

[![Build Status](https://img.shields.io/travis/com/brefphp/dynamap/master.svg?style=flat-square)](https://travis-ci.com/brefphp/dynamap)
[![Latest Version](https://img.shields.io/github/release/brefphp/dynamap.svg?style=flat-square)](https://packagist.org/packages/bref/dynamap)
[![Total Downloads](https://img.shields.io/packagist/dt/bref/dynamap.svg?style=flat-square)](https://packagist.org/packages/bref/dynamap)

**This library is currently in an experimental status and is not meant to be used in production.**

## Installation

```
composer require bref/dynamap
```

## Usage

```php
$dynamap = Dynamap::fromOptions([
    'region' => 'us-east-1',
], $mapping);

$dynamap->save($myObject);

$myObject = $dynamap->find('table', 'key');
$myObject = $dynamap->get('table', 'key'); // Same as `find()` but throws an exception if not found

$objects = $dynamap->getAll('table');
```

Supported field types:

- string
- integer
- float
- bool
- `DateTimeImmutable` (stored as string)

## Contributing

To run tests locally:

- start DynamoDB local with `docker-compose up` or `docker-compose start`
- run `phpunit`
