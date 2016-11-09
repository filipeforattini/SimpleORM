<?php
namespace SimpleORM;

use ArrayObject;
use Faker\Factory;
use Faker\Generator;
use ICanBoogie\Inflector;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Table;

/**
 * Class Entity
 * @package SimpleORM
 *
 * Class variables will have a underline to allow to use
 * any combination of strings for columns name.
 */
abstract class Entity extends ArrayObject
{
    /**
     * Entity's table name.
     *
     * @var string
     */
    protected static $_table = null;

    /**
     * Primary key
     *
     * @var array
     */
    protected static $_pk = ['id'];

    /**
     * Columns
     *
     * @var array
     */
    protected static $_columns = [];

    /**
     * Saves if the object was created without co-relation from the
     * database.
     *
     * @var bool
     */
    protected $_new = true;

    /**
     * Flags if the object has some attributes that have had their
     * values changed.
     *
     * @var bool
     */
    protected $_changed = false;

    /**
     * Entity constructor.
     *
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        if(is_null(static::getTableName())) {
            static::$_table = static::inferTable();
        }

        parent::__construct($attributes, ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * @param Table $table
     * @param Schema|null $schema
     * @return Table
     */
    public static function createTable(callable $callable = null)
    {
        $table = new Table(static::getTableName());

        if(is_null($callable)) {
            return static::defineTable($table);
        }

        return call_user_func_array($callable, [$table]);
    }

    /**
     * @param Table $table
     * @return Table
     */
    public static function defineTable(Table $table)
    {
        return $table;
    }

    /**
     * @param callable|null $callable
     * @return static
     */
    public static function factory(callable $callable = null)
    {
        if(is_null($callable)) {
            return new static(static::defineFactory(Factory::create()));
        }

        return new static(call_user_func_array($callable, [Factory::create()]));
    }

    /**
     * @param Generator $faker
     * @return array
     */
    public static function defineFactory(Generator $faker)
    {
        return [];
    }

    /**
     * Infers the table name for the entity object.
     *
     * @return string
     */
    public static function inferTable()
    {
        $class = explode('\\', get_called_class());

        return Inflector::get('en')->pluralize(mb_strtolower(end($class)));
    }

    /**
     * Returns entity's table name.
     * @return string
     */
    public static function getTableName()
    {
        if(is_null(static::$_table)) {
            static::$_table = static::inferTable();
        }

        return (string) static::$_table;
    }

    /**
     * @param Connection $connection
     */
    public static function setupColumns(Connection $connection)
    {
        static::$_columns = [];

        $columns = $connection
            ->getSchemaManager()
            ->listTableColumns(static::getTableName());

        foreach($columns as $index => $column) {
            static::$_columns[] = $index;
        }
    }

    /**
     * Returns an array of columns.
     *
     * @return array
     */
    public static function getColumns()
    {
        return static::$_columns;
    }

    /**
     * @return string
     */
    public static function columnsToSql()
    {
        return "(`".implode("`,`", static::getColumns())."`)";
    }

    /**
     * Get array of primary keys.
     *
     * @return array
     */
    public static function getPk()
    {
        return static::$_pk;
    }

    /**
     * Set array of primary keys.
     *
     * @param array $pk
     */
    public static function setPk($pk)
    {
        static::$_pk = $pk;
    }

    /**
     * See _new attribute.
     *
     * @return bool
     */
    public function isNew()
    {
        return $this->_new;
    }

    /**
     * See _changed attribute.
     *
     * @return bool
     */
    public function hasChanged()
    {
        return $this->_changed;
    }

    /**
     * Transforms the object into SQL string.
     * @return string
     */
    public function toSql()
    {
        return "('".implode("','", $this->getArrayCopy())."')";
    }
}
