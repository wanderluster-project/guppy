<?php

namespace Guppy;

use Symfony\Component\Lock\LockFactory;
use Exception;

class Writer
{
    protected string $baseDir;
    protected string $repoUuid;
    protected string $repoDir;
    protected Compressor $compressor;
    protected LockFactory $lockFactory;


    public function __construct(string $repoUuid, string $baseDir, Compressor $compressor, LockFactory $lockFactory)
    {
        $this->repoUuid = $repoUuid;
        $this->baseDir =$baseDir;
        $this->compressor = $compressor;
        $this->lockFactory = $lockFactory;
        $this->init();
    }

    /**
     * Initialize the repo directory.
     */
    public function init()
    {
        $repoDir = $this->baseDir . '/' . $this->repoUuid;
        if (!file_exists($repoDir)) {
            mkdir($repoDir);
            mkdir($repoDir . '/entities');
            mkdir($repoDir . '/snapshots');
        }
        $this->repoDir = realpath($repoDir);
    }

    /**
     * @param Entity $entity
     * @return $this
     */
    public function writeEntity(Entity $entity)
    {
        $repoPaths = $entity->getRepoPaths();
        $dir = $this->repoDir . '/entities/' . $repoPaths['dir'];
        if (!file_exists($dir)) {
            mkdir($dir);
        }
        $filePath = $dir . '/' . $repoPaths['file'];
        if (!file_exists($filePath)) {
            file_put_contents($filePath, $this->compressor->compress($entity->getData()));
        }
        return $this;
    }

    /**
     * @param string $hash
     * @param string $jsonData
     * @return $this
     */
    public function writeSnapshot(Snapshot $snapshot)
    {
        $hash = $snapshot->getHash();
        $jsonData = json_encode($snapshot->getData());
        $lock = $this->lockFactory->createLock($this->repoUuid, 90);
        if (!$lock->acquire()) {
            throw new Exception(sprintf('Unable to obtain lock for repository -  %s', $this->repoUuid));
        }

        try {
            $filePath = $this->repoDir . '/snapshots/' . $hash;
            $currentPath = $this->repoDir . '/current';
            if (!file_exists($filePath)) {
                file_put_contents($filePath, $jsonData);
            }
            if (file_exists($currentPath)) {
                unlink($this->repoDir . '/current');
            }

            symlink($filePath, $this->repoDir . '/current');
        } finally {
            $lock->release();
        }

        return $this;
    }
}