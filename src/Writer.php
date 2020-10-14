<?php

namespace Guppy;

use Exception;
use Symfony\Component\Lock\LockInterface;

class Writer
{
    protected string $repoDir;
    protected Compressor $compressor;
    protected LockInterface $lock;
    protected ShellExecutor $shellExecutor;

    public function __construct( string $repoDir, Compressor $compressor, LockInterface $lock, ShellExecutor $shellExecutor)
    {
        $this->repoDir = $repoDir;
        $this->compressor = $compressor;
        $this->lock = $lock;
        $this->shellExecutor = $shellExecutor;
    }

    /**
     * Initialize the repo directory.
     */
    public function initRepository()
    {
        mkdir($this->repoDir);
        mkdir($this->repoDir . '/entities');
        mkdir($this->repoDir . '/snapshots');
    }

    /**
     * @param Entity $entity
     * @return $this
     */
    public function writeEntity(Entity $entity)
    {
        $filePath = $this->repoDir . '/entities/' . $entity->getHash();
        file_put_contents($filePath, $this->compressor->compress($entity->getData()));
        return $this;
    }

    /**
     * @param Snapshot $snapshot
     * @return $this
     * @throws Exception
     */
    public function writeSnapshot(Snapshot $snapshot)
    {
        $version = $snapshot->getVersion();
        $jsonData = json_encode($snapshot->getData());
        if (!$this->lock->acquire()) {
            throw new Exception(sprintf('Unable to obtain lock for repository'));
        }

        try {
            $filePath = $this->repoDir . '/snapshots/' . $version;
            $currentPath = $this->repoDir . '/current';
            file_put_contents($filePath, $jsonData);
            $this->shellExecutor->exec('ln -sfn %s %s', [$filePath, $currentPath]);
        } finally {
            $this->lock->release();
        }

        return $this;
    }
}