# Portfolio

A PHP class for managing a portfolio of projects

Mark Roland <first name @ full name dot com>

    Copyright 2011-2026 Mark Roland.
    Released under the MIT license.

## Installation

```sh
composer require markroland/portfolio
```

## Usage

```php
$Portfolio = new MarkRoland\Portfolio\Catalog;
print($Portfolio>get_all_projects());
```

The "examples" folder in this package contains sample code.

## Build

### PHPUnit

```sh
./vendor/bin/phpunit --bootstrap tests/bootstrap.php tests
```

### Code Coverage

```sh
./vendor/bin/phpunit --coverage-html ./report ./tests
```

### PHP Documentation

PHP Documentation is compiled using [phpDocumentor](http://www.phpdoc.org), which is assumed
to be installed globally on the server. It uses phpdoc.dist.xml for runtime configuration.

```sh
./vendor/bin/phpdoc
```

### Code Sniff

```sh
./vendor/bin/phpcs -n --report-width=100 ./src
```
