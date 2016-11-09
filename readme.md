# SimpleORM

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

SimpleORM is an intuitive package to work with `Entity` and `Repository` abstractions. Developed over `Doctrine\DBAL` package to ensure quality and versality of development.

## Get started

### Installing

Install using Composer:

```bash
composer require "fforattini/simpleorm"
```

Read the unit tests files for faster understanding of the package:
+ [Entity Tests](tests/EntityTests.php)
+ [Repository Tests](tests/RepositoryTests.php)

## Features

### Entity

The `Entity` should represent an table on your database.

```php
use SimpleORM\Entity;

class Book extends Entity 
{

}
```

### Repository

The `Repository` should deal with a collection of entities objects, this is where all of `SQL` is placed and search logic.

It uses and `Doctrine\DBAL\Connection` to connect to databases and execute queries;

```php
use SimpleORM\Repository;
use Doctrine\DBAL\Connection;

DriverManager::getConnection([
    'driver' => 'pdo_sqlite',
    'path' => 'database.sqlite',
]);

$repository = new Repository(Book::class, $connection);
```
