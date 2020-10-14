<?php

namespace Guppy;

class Snapshot
{
    /**
     * @var Entity[]
     */
    protected array $entities = [];
    protected Hasher $hasher;
    protected Writer $writer;

    /**
     * Snapshot constructor.
     * @param Hasher $hasher
     */
    public function __construct(Hasher $hasher, Writer $writer)
    {
        $this->hasher = $hasher;
        $this->writer = $writer;
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

    public function save()
    {
        $data = [];
        foreach ($this->entities as $entity) {
            $this->writer->writeEntity($entity);
            $data[$entity->getKey()] = $entity->getHash();
        }
        ksort($data);
        $this->writer->writeSnapshot($this->getHash(), json_encode($data, JSON_PRETTY_PRINT));
    }
}