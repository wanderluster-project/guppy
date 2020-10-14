<?php

namespace Guppy;

use Exception;
use Symfony\Component\Lock\Lock;
use Symfony\Component\Lock\LockInterface;

class Repository
{
    protected string $uuid;
    protected Config $config;
    protected Compressor $compressor;
    protected Reader $reader;
    protected Writer $writer;
    protected Hasher $hasher;
    protected LockInterface $lock;

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
        $this->lock = $config->lockFactory->createLock($uuid);
        $this->compressor = new Compressor($this->config);
        $this->reader = new Reader($config->baseDir. '/'.$uuid);
        $this->writer = new Writer($config->baseDir. '/'.$uuid, $this->compressor, $this->lock , new ShellExecutor());
        $this->hasher = new Hasher($config);
    }

    public function init():Repository{
        $this->writer->initRepository();
        return $this;
    }

    /**
     * @param ChangeSet $changeset
     */
    public function commit(ChangeSet $changeset)
    {
        $originalChangeset = new ChangeSet($this->reader->getCurrentSnapshotData());
        $changeSet = $originalChangeset->merge($changeset);

        $entities = [];
        $keys = $changeSet->keys();
        foreach ($keys as $key) {
            $data = $changeSet->get($key);
            $hash = $this->hasher->hash($data);
            $entity = new Entity($key, $hash, $data);
            $this->writer->writeEntity($entity);
            $entities[] = $entity;
        }
        $snapshot = new Snapshot(0, $entities);

        $this->writer->writeSnapshot($snapshot);
    }
}