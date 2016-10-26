<?php
require_once(__DIR__ . '/../../vendor/autoload.php');

use SimpleORM\Entity;
use Doctrine\DBAL\Schema\Table;

class Book extends Entity
{
    /**
     * @return Table
     */
    public static function createTable()
    {
        $table = static::newTable();
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