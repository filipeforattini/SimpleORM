<?php
namespace SimpleORM;

use SplDoublyLinkedList;
use Doctrine\DBAL\Connection;

class Repository extends SplDoublyLinkedList
{
    /**
     * @var string
     */
    protected $entity;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var array
     */
    protected $columns;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var array
     */
    protected $pk;

    /**
     * Repository constructor.
     *
     * @param $entity
     * @param Connection $connection
     */
    public function __construct($entity, Connection $connection)
    {
        $entity::setupColumns($connection);

        $this->connection = $connection;
        $this->entity = $entity;
        $this->table = $entity::getTableName();
        $this->pk = $entity::getPk();
        $this->columns = $entity::getColumns();
    }

    /**
     * @return string
     */
    public function columnsToSql()
    {
        return "(`".implode("`,`", $this->columns)."`) ";
    }

    /**
     * @return Repository
     */
    public function all()
    {
        $list = $this->connection->fetchAll("SELECT * FROM {$this->table}");

        $entityClass = $this->entity;

        foreach ($list as $item) {
            $this->push(new $entityClass($item));
        }

        return $this;
    }

    /**
     * @param Entity $entity
     * @return Repository
     */
    public function insert(Entity $entity)
    {
        $this->connection->insert(
            $this->table,
            $entity->getArrayCopy()
        );

        return $this;
    }

    /**
     * @param array $attributes
     * @return Repository
     */
    public function update($attributes = [])
    {
        $this->connection->update(
            $this->table,
            $attributes,
            $this->pk
        );

        return $this;
    }

    /**
     * @param Entity $entity
     * @return Repository
     */
    public function save(Entity $entity)
    {
        if($entity->isNew()) {
            $this->insert($entity);
        }

        return $this;
    }

    /**
     * @param bool $oneByOne
     * @return Repository
     */
    public function saveAll($oneByOne = false)
    {
        list($inserts, $updates) = array([], []);

        foreach($this as $entity) {
            if($entity->isNew()) {
                //$this->insert($entity);
                $inserts[] = $entity->toSql();
            } else {
                $updates[] = $entity->attributes();
            }
        }

        $sql_inserts = "INSERT INTO {$this->table} {$this->columnsToSql()}
            VALUES ".implode(', ', $inserts).";";

        $this->connection->exec($sql_inserts);

        return $this;
    }
}
