<?php

namespace Guppy;

class Entity
{
    protected string $key;
    protected string $hash;
    protected string $data;

    /**
     * Entity constructor.
     * @param string $key
     * @param string $hash
     * @param string $data
     */
    public function __construct(string $key, string $hash, string $data)
    {
        $this->key = $key;
        $this->hash = $hash;
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }
}