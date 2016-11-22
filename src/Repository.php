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
     * Creates an Entity.
     *
     * @param array $attributes
     * @return Entity
     */
    public function createEntity($attributes = [])
    {
        $entityClass = $this->entity;

        return new $entityClass($attributes);
    }

    /**
     * @return Repository
     */
    public function all()
    {
        $list = $this->connection->fetchAll("SELECT * FROM {$this->table}");

        foreach ($list as $item) {
            $this->push($this->createEntity($item));
        }

        return $this;
    }

    /**
     * Finds an Entity by given primary key.
     *
     * @param  array $pk
     * @return Entity
     */
    public function find($pk)
    {
        if(! is_array($pk)) {
            $pk = [$pk];
        }

        $query = [];
        foreach($this->pk as $key) {
            $query[] = "{$key} = ?";
        }

        $query = implode(" AND ", $query);

        return $this->createEntity(
            $this->connection->fetchAssoc(
                "SELECT * FROM {$this->table} WHERE {$query};",
                $pk
            )
        );
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
