# SimpleORM (Under development)

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

## How to use

SimpleORM has two abstractions `Entity` and `Repository`.

### Entity

The `Entity` should represent an table on your database.

```php
use SimpleORM\Entity;

class Book extends Entity 
{

}
```

#### Custom properties

Most of the `Entity` attributes are defined by `SimpleORM`, but of course you can define your own values for them:

+ `protected static $_table` will be define as the plural of the class name using the package `icanboogie/inflector`;

Example:
```php
use SimpleORM\Entity;

class Book extends Entity 
{
    public static $_table = 'my_books_table';
}
```

#### Attributes

The class `Entity` extends an `ArrayObject`! So there is an list of possibilities for you to work with your attributes:

```php
$book = new Book();
$book->name = "My book";

// OR

$book = new Book([
    'name' => "My book",
]);
```

Learn more about [`ArrayObject` here](http://php.net/manual/en/class.arrayobject.php).

#### Creating tables

You can define your table using a `Doctrine\DBAL\Schema\Table` instance through the function `Entity::defineTable(Table $table)` :

```php
use SimpleORM\Entity;
use Doctrine\DBAL\Schema\Table;

class Book extends Entity 
{
    public static function defineTable(Table $table)
    {
        $table->addColumn('id', 'string', [
            'length' => 36,
            'unique' => true,
        ]);
        $table->addColumn('name', 'string');
        $table->addUniqueIndex(["id"]);
        $table->setPrimaryKey(['id']);
        return $table;
    }
}
```

You will need to use the `Doctrine\DBAL\DriverManager` to get a `Connection` and create your table:

```php
$schema = DriverManager::getConnection([
    'driver'    => 'pdo_sqlite',
    'path'      => 'database.sqlite',
])->getSchemaManager();

$schema->createTable(Book::getTable());
```

Follow this to get to know [more about `Connection`](http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html).

### Repository

The `Repository` should deal with a collection of objects of `Entity` and implements `SplDoublyLinkedList` (you can check [more about it here](http://php.net/manual/en/class.spldoublylinkedlist.php)).

This is where all of `SQL` queries are trigged using the `Doctrine\DBAL\Connection`.

```php
use SimpleORM\Repository;
use Doctrine\DBAL\DriverManager;

DriverManager::getConnection([
    'driver' => 'pdo_sqlite',
    'path' => 'database.sqlite',
]);

$books = new Repository(Book::class, $connection);
```

#### Retriving information

To populate your `Repository` get all elements.

```php
$books->all();

foreach($books as $book) {
    // do something here
}
```

