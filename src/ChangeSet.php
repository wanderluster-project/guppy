<?php

namespace Guppy;

use Exception;
use Iterator;

class ChangeSet implements Iterator
{
    protected array $data = [];

    /**
     * Changeset constructor.
     * @param array $data
     * @throws Exception
     */
    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * @param string $key
     * @param string $value
     * @return $this
     * @throws Exception
     */
    public function set(string $key, ?string $value)
    {
        if (!preg_match('/[a-zA-Z0-9-_]+/', $key)) {
            throw new Exception(sprintf('Invalid format for key - %s', $key));
        }
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * @param $key
     * @return string|null
     */
    public function get($key): ?string
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }
        return null;
    }

    /**
     * @return array
     */
    public function keys(): array
    {
        return array_keys($this->data);
    }

    /**
     * @return mixed|void
     */
    function rewind()
    {
        return reset($this->data);
    }

    /**
     * @return mixed
     */
    function current()
    {
        return current($this->data);
    }

    /**
     * @return bool|float|int|string|null
     */
    function key()
    {
        return key($this->data);
    }

    /**
     * @return mixed|void
     */
    function next()
    {
        return next($this->data);
    }

    /**
     * @return bool
     */
    function valid()
    {
        return key($this->data) !== null;
    }

    /**
     * @param ChangeSet $changeSet
     * @return $this
     * @throws Exception
     */
    public function merge(ChangeSet $changeSet): ChangeSet
    {
        foreach ($changeSet as $key => $value) {
            $this->set($key, $value);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function asArray(): array
    {
        return $this->data;
    }

    /**
     * @param $key
     * @return bool
     */
    public function exists($key): bool
    {
        return array_key_exists($key, $this->data) && !is_null($this->data[$key]);
    }
}