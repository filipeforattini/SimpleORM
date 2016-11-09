<?php

require_once('assets/Book.php');

use SimpleORM\Entity;
use SimpleORM\Repository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

class RepositoryTests extends PHPUnit_Framework_TestCase
{
    /**
     * @var Doctrine\DBAL\Connection;
     */
    protected $connection;

    public static function setUpBeforeClass()
    {
        $connection = DriverManager::getConnection([
            'driver'    => 'pdo_sqlite',
            'path'      => __DIR__.'/assets/'.getenv('sqlite_database'),
        ]);

        $table = Book::getTableName();

        $schema_manager = $connection->getSchemaManager();

        if($schema_manager->tablesExist($table)) {
            $schema_manager->dropTable($table);
        }
    }

    public function setUp()
    {
        $this->connection = DriverManager::getConnection([
            'driver'    => 'pdo_sqlite',
            'path'      => __DIR__.'/assets/'.getenv('sqlite_database'),
        ]);
    }

    /**
     * @test
     */
    public function can_create_table()
    {
        $schema_manager = $this->connection->getSchemaManager();

        $schema_manager->createTable(Book::getTable());

        static::assertTrue(
            $schema_manager->tablesExist(
                Book::getTableName()
            )
        );
    }

    /**
     * @test
     */
    public function can_create_repository()
    {
        static::assertFalse(is_null(
            new Repository(Book::class, $this->connection)
        ));
    }

    /**
     * @test
     */
    public function can_insert()
    {
        $faker = Faker\Factory::create();
        $repository = new Repository(Book::class, $this->connection);

        $repository->save(new Book([
            'id' => Ramsey\Uuid\Uuid::uuid4(),
            'name' => $faker->sentence(5, true),
        ]));

        static::assertEquals($repository->all()->count(), 1);
    }

    /**
     * @test
     */
    public function can_insert_a_list()
    {
        $faker = Faker\Factory::create();

        $repository = new Repository(Book::class, $this->connection);

        for ($i = (int) getenv('table_lines'); $i > 0; $i--) {
            $repository->push(new Book([
                'id' => Ramsey\Uuid\Uuid::uuid4(),
                'name' => $faker->sentence(5, true),
            ]));
        }

        $repository->saveAll();
    }
}