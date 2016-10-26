<?php
namespace SimpleORM;

use SplDoublyLinkedList;
use Doctrine\DBAL\Connection;

class Repository extends SplDoublyLinkedList
{
    protected $entity;
    protected $table;
    protected $columns;
    protected $connection;
    protected $pk;

    public function __construct($entity, Connection $connection)
    {
        $this->connection = $connection;
        $this->entity = $entity;
        $this->table = $entity::getTableName();
        $this->pk = $entity::getPk();
    }

    public function all()
    {
        $this->exchangeArray(
            $this->connection->fetchAll("SELECT * FROM {$this->table}")
        );
    }

    public function insert(Entity $entity)
    {
        $this->connection->insert(
            $this->table,
            $entity->getArrayCopy()
        );

        return $this;
    }

    public function update($attributes)
    {
        $this->connection->update(
            $this->table,
            $attributes,
            $this->pk
        );

        return $this;
    }


    public function save(Entity $entity)
    {
        if($entity->isNew()) {
            $this->insert($entity);
        }
    }

    public function saveAll()
    {
        $inserts = [];
        $updates = [];

        foreach($this as $entity) {
            if($entity->isNew()) {
                //$this->insert($entity);
                $inserts[] = $entity->toSql();
            } else {
                $updates[] = $entity->attributes();
            }
        }

        $sql_inserts = "
            INSERT INTO {$this->table}
            VALUES ".implode(', ', $inserts).";";

        $this->connection->exec($sql_inserts);
    }
}
