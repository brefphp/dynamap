DynamoDB object mapper. Like Doctrine or Eloquent, but for DynamoDB.

[![Build Status](https://img.shields.io/travis/com/bref/dynamap/master.svg?style=flat-square)](https://travis-ci.com/bref/dynamap)
[![Latest Version](https://img.shields.io/github/release/bref/dynamap.svg?style=flat-square)](https://packagist.org/packages/bref/dynamap)
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
$myObject = $dynamap->get('table', 'key');
$objects = $dynamap->getAll('table');
```

## Contributing

To run tests locally:

- start DynamoDB local with `docker-compose up` or `docker-compose start`
- run `phpunit`
