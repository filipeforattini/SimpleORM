<?php
namespace SimpleORM;

use ArrayObject;
use ICanBoogie\Inflector;
use Doctrine\DBAL\Schema\Schema;
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
    public static $table = null;

    /**
     * Primary key
     *
     * @var array
     */
    protected static $_pk = ['id'];

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
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        if(is_null(static::getTableName())) {
            static::$table = static::inferTable();
        }

        parent::__construct($attributes, ArrayObject::ARRAY_AS_PROPS);
    }

    public static function newTable()
    {
        return new Table(static::getTableName());
    }

    /**
     * @param Table $table
     * @param Schema|null $schema
     * @return Table
     */
    public static function createTable()
    {
        return $table;
    }

    /**
     * Returns entity's table name.
     * @return string
     */
    public static function getTableName()
    {
        if(is_null(static::$table)) {
            static::$table = static::inferTable();
        }

        return (string) static::$table;
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
    public function isChanged()
    {
        return $this->_changed;
    }

    /**
     * Infers the table name for the entity object.
     *
     * @return string
     */
    public static function inferTable()
    {
        $class = explode('\\', get_called_class());

        return Inflector::get('en')
            ->pluralize(mb_strtolower(end($class)));
    }

    /**
     * Transforms the object into SQL string.
     * @return string
     */
    public function toSql()
    {
        return "(`".implode("`,`", $this->getArrayCopy())."`)";
    }
}
