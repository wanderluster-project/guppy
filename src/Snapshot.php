<?php

namespace Guppy;

class Snapshot
{
    /**
     * @var int
     */
    protected int $version;

    /**
     * @var Entity[]
     */
    protected array $entities = [];

    /**
     * Snapshot constructor.
     * @param int $version
     * @param array $entities
     */
    public function __construct(int $version, array $entities)
    {
        $this->version = $version;
        foreach ($entities as $entity) {
            $this->addEntity($entity);
        }
    }

    /**
     * @return int
     */
    public function getVersion():int{
        return $this->version;
    }

    /**
     * @param Entity $entity
     */
    public function addEntity(Entity $entity){
        $this->entities[$entity->getKey()] = $entity;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        $data = [];
        foreach ($this->entities as $entity) {
            $data[$entity->getKey()] = $entity->getHash();
        }
        ksort($data);
        return $data;
    }

    public function incrementVersion(){
        return new Snapshot($this->getVersion()+1, $this->entities);
    }
}