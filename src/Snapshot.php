<?php

namespace Guppy;

class Snapshot
{
    /**
     * @var Entity[]
     */
    protected array $entities = [];
    protected Hasher $hasher;

    /**
     * Snapshot constructor.
     * @param Hasher $hasher
     */
    public function __construct(Hasher $hasher)
    {
        $this->hasher = $hasher;
    }

    /**
     * @param Entity $entity
     * @return Snapshot
     */
    public function add(Entity $entity): Snapshot
    {
        $this->entities[$entity->getKey()] = $entity;
        return $this;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        ksort($this->entities);
        return $this->hasher->hash(json_encode(array_keys($this->entities)));
    }

    public function getData()
    {
        $data = [];
        foreach ($this->entities as $entity) {
            $data[$entity->getKey()] = $entity->getHash();
        }
        ksort($data);
        return $data;
    }
}