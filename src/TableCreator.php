<?php
namespace SimpleORM;

use Doctrine\DBAL\Schema\Table;

/**
 * Interface TableCreator
 * @package SimpleORM
 */
Interface TableCreator
{
    /**
     * @param Table $table
     * @return Table
     */
    public static function defineTable(Table $table);
}