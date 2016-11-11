# SimpleORM (Under development)

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

SimpleORM is an intuitive package to work with `Entity` and `Repository` abstractions.

This package was developed using [`Doctrine\DBAL`](docs.doctrine-project.org/projects/doctrine-dbal/en/latest/) package to ensure quality and versatility of development.

## Get started

### Installing

Install using Composer:

```bash
composer require "fforattini/simpleorm"
```

Read the unit tests files for faster understanding of the package:
+ [Entity](tests/EntityTests.php)
+ [Repository](tests/RepositoryTests.php)

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
$book->id = '02adee84-a128-4e51-8170-4155ea222fae';
$book->name = 'My book';

// OR

$book = new Book([
    'id' => '02adee84-a128-4e51-8170-4155ea222fae',
    'name' => 'My book',
]);
```

Learn more about [`ArrayObject` here with the docs](http://php.net/manual/en/class.arrayobject.php).

#### Mocking data

Simply define a factory of elements using `fzaninotto/faker` (learn more about this [with the docs](https://github.com/fzaninotto/Faker)):

```php
use Faker\Generator;
use Ramsey\Uuid\Uuid;
use SimpleORM\Entity;

class Book extends Entity
{
    public static function defineFactory(Generator $faker)
    {
        return [
            'id' => Uuid::uuid4(),
            'name' => $faker->sentence(5, true),
        ];
    }
}
```

Then you can just call:

```php
$book = Book::factory();
```

Or you can just pass a `callable` as parameter and you will receive an instance of the `Generator`:

```php
use Ramsey\Uuid\Uuid;

$book = Book::factory(function($faker){
    return [
        'id' => Uuid::uuid4(),
        'name' => $faker->sentence(5, true),
    ];
});
```

#### Creating tables

You can define your table using a `Doctrine\DBAL\Schema\Table` instance through the function `Entity::defineTable(Table $table)` :

```php
use SimpleORM\Entity;
use SimpleORM\TableCreator;
use Doctrine\DBAL\Schema\Table;

class Book extends Entity implements TableCreator 
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

You will need to use the `Doctrine\DBAL\DriverManager` to get a `Connection` (learn more about this [with the docs](http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html)) and create your table:

```php
$schema = DriverManager::getConnection([
    'driver'    => 'pdo_sqlite',
    'path'      => 'database.sqlite',
])->getSchemaManager();
```

Then you can create your table using the connection you create with the following method:

```php
$schema->createTable(Book::getTable());
```

Or you can just pass a `callable` as parameter and you will receive an instance of the `Generator`:

```php
use SimpleORM\Entity;

$schema->createTable(Entity::getTable(function($table){
    $table->addColumn('id', 'string', [
        'length' => 36,
        'unique' => true,
    ]);
    $table->addColumn('name', 'string');
    $table->addUniqueIndex(["id"]);
    $table->setPrimaryKey(['id']);
    return $table;
}));
```

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

To populate your `Repository` with all of your elements.

```php
$books->all();

foreach($books as $book) {
    // do something here
}
```

