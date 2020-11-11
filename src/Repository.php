<?php

namespace Guppy;

use Exception;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\LockInterface;
use Symfony\Component\Lock\Store\FlockStore;

class Repository
{
    protected string $uuid;
    protected Config $config;
    protected FileSystem $fs;
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
        $this->fs = new FileSystem($this->config);
        $this->reader = new Reader($config->baseDir . '/' . $uuid, $this->fs, $this->config->compressSnapshots);
        $this->writer = new Writer($config->baseDir . '/' . $uuid, $this->fs, $this->config->compressSnapshots, $this->config->compressEntities);
        $this->hasher = new Hasher($config);
    }

    public function init(): Repository
    {
        $this->writer->initRepository();
        return $this;
    }

    /**
     * @param ChangeSet $changeSet
     * @throws Exception
     */
    public function commit(ChangeSet $changeSet)
    {
        $lockFactory = new LockFactory(new FlockStore($this->config->baseDir));
        $lock = $lockFactory->createLock($this->uuid);
        try {
            if (!$lock->acquire()) {
                throw new Exception('Unable to obtain lock on repository to commit.');
            };

            $currentSnapshot = $this->reader->getCurrentSnapshot();
            $newSnapshot = $currentSnapshot->incrementVersion();
            $keys = $changeSet->keys();
            foreach ($keys as $key) {
                $data = $changeSet->get($key);
                $hash = $this->hasher->hash($data);
                $entity = new Entity($key, $hash, $data);
                $this->writer->writeEntity($entity);
                $newSnapshot->addEntity($entity);
            }

            $this->writer->writeSnapshot($newSnapshot);
        } catch (Exception $e) {
            throw $e;
            // @todo log exception
        } finally {
            $lock->release();
        }
    }
}