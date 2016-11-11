<?php

use Doctrine\DBAL\DriverManager;
use Ramsey\Uuid\Uuid;
use SimpleORM\Entity;

require_once('assets/Book.php');

class EntityTests extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Doctrine\DBAL\Driver\Connection
     */
    protected $connection;

    /**
     * @var \Doctrine\DBAL\Schema\AbstractSchemaManager
     */
    protected $schema;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->connection = DriverManager::getConnection([
            'driver'    => 'pdo_sqlite',
            'path'      => __DIR__.'/assets/'.getenv('sqlite_database'),
        ]);

        $this->schema = $this->connection->getSchemaManager();

        $table = Book::getTableName();

        if($this->schema->tablesExist($table)) {
            $this->schema->dropTable($table);
        }
    }

    /**
     * @test
     */
    public function can_create_an_instance()
    {
        static::assertTrue(new Book() instanceof Entity);
    }

    /**
     * @test
     */
    public function can_create_a_mocked_entity_by_definition()
    {
        static::assertArrayHasKey('id', Book::factory());
    }

    /**
     * @test
     */
    public function can_create_a_mocked_entity_by_function()
    {
        static::assertArrayHasKey('id', Book::factory(function($faker){
            return [
                'id' => Uuid::uuid4(),
                'title' => $faker->sentence(5, true),
            ];
        }));
    }

    /**
     * @test
     */
    public function created_entity_is_new()
    {
        static::assertTrue((new Book)->isNew());
    }

    /**
     * @test
     */
    public function create_table_with_definition()
    {
        $this->schema->createTable(Book::getTable());

        static::assertTrue($this->schema->tablesExist(Book::getTableName()));
    }

    /**
     * @test
     */
    public function create_table_with_callable()
    {
        $this->schema->createTable(Book::getTable(function($table){
            $table->addColumn('id', 'string', [
                'length' => 36,
                'unique' => true,
            ]);
            $table->addColumn('name', 'string');
            $table->addUniqueIndex(["id"]);
            $table->setPrimaryKey(['id']);
            return $table;
        }));

        static::assertTrue($this->schema->tablesExist(Book::getTableName()));
    }
}