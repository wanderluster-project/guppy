<?php

namespace Guppy;

use Exception;

class Repository
{
    protected string $uuid;
    protected Config $config;
    protected Compressor $compressor;
    protected Reader $reader;
    protected Writer $writer;
    protected Hasher $hasher;

    /**
     * Repository constructor.
     * @param string $uuid
     * @param Config $config
     * @throws Exception
     */
    public function __construct(string $uuid, Config $config)
    {
        $config->validateConfigs();

        $this->uuid = $uuid;
        $this->config = $config;
        $this->compressor = new Compressor($this->config);
        $this->reader = new Reader($config->baseDir. '/'.$uuid);
        $this->writer = new Writer($uuid, $config->baseDir, $this->compressor, $config->lockFactory);
        $this->hasher = new Hasher($config);
    }

    public function init()
    {


        return $this;
    }

    /**
     * @param ChangeSet $changeset
     */
    public function commit(ChangeSet $changeset)
    {
        $originalChangeset = new ChangeSet($this->reader->getCurrentSnapshotData());
        $changeset = $originalChangeset->merge($changeset);

        $snapshot = new Snapshot($this->hasher, $this->writer);
        $keys = $changeset->keys();
        foreach ($keys as $key) {
            $data = $changeset->get($key);
            $hash = $this->hasher->hash($data);
            $entity = new Entity($key, $hash, $data);
            $snapshot->add($entity);
        }
        $snapshot->save();
    }
}