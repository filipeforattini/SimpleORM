<?php
require_once(__DIR__ . '/../../vendor/autoload.php');

use Faker\Generator;
use SimpleORM\Entity;
use Ramsey\Uuid\Uuid;
use Doctrine\DBAL\Schema\Table;

class Book extends Entity
{
    /**
     * @return Table
     */
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

    /**
     * @param Generator $faker
     * @return array
     */
    public static function defineMock(Generator $faker)
    {
        return [
            'id' => Uuid::uuid4(),
            'name' => $faker->sentence(5, true),
        ];
    }
}