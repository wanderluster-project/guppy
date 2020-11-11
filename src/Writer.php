<?php

namespace Guppy;

use Exception;

class Writer
{
    protected string $repoDir;
    protected FileSystem $fs;
    protected bool $compressEntities;
    protected bool $compressSnapshots;

    public function __construct(string $repoDir, FileSystem $fs, bool $compressSnapshots, bool $compressEntities)
    {
        $this->repoDir = $repoDir;
        $this->fs = $fs;
        $this->compressEntities = $compressEntities;
        $this->compressSnapshots = $compressSnapshots;
    }

    /**
     * Initialize the repo directory.
     */
    public function initRepository()
    {
        if ($this->fs->fileExists($this->repoDir)) {
            throw new Exception('Repository already initialized');
        }
        $this->fs->makeDir($this->repoDir);
        $this->fs->makeDir($this->repoDir . '/entities');
        $this->fs->makeDir($this->repoDir . '/snapshots');
        $this->fs->writeToFile($this->repoDir . '/snapshots/0', json_encode(['_ver' => 0, '_data' => []]));
        $this->setCurrentVersion(0);
    }

    /**
     * @param Entity $entity
     * @return $this
     */
    public function writeEntity(Entity $entity)
    {
        $filePath = $this->repoDir . '/entities/' . $entity->getHash();
        if ($this->compressEntities) {
            $this->fs->writeToFile('compress.zlib://' . $filePath, $entity->getData());
        } else {
            $this->fs->writeToFile($filePath, $entity->getData());
        }

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
        $jsonData = json_encode(['_ver' => $version, '_data' => $snapshot->getData()], JSON_PRETTY_PRINT);

        $filePath = $this->repoDir . '/snapshots/' . $version;
        if ($this->compressSnapshots) {
            $this->fs->writeToFile('compress.zlib://' . $filePath, $jsonData);
        } else {
            $this->fs->writeToFile( $filePath, $jsonData);
        }
        $this->setCurrentVersion($version);

        return $this;
    }

    public function setCurrentVersion(int $version)
    {
        $this->fs->writeToFile($this->repoDir . '/current', $version);
    }
}