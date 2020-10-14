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
            $this->entities[$entity->getKey()] = $entity;
        }
    }

    /**
     * @return int
     */
    public function getVersion():int{
        return $this->version;
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
}