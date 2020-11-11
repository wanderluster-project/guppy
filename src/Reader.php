<?php

namespace Guppy;

use Exception;
use JsonException;

class Reader
{
    protected string $repoDir;
    protected FileSystem $fs;
    protected bool $compressSnapshots;

    public function __construct(string $repoDir, FileSystem $fs, bool $compressSnapshots)
    {
        $this->repoDir = $repoDir;
        $this->fs = $fs;
        $this->compressSnapshots = $compressSnapshots;
    }

    public function getCurrentSnapshot()
    {
        $version = $this->fs->readFile($this->repoDir . '/current');
        $currentPath = $this->repoDir . '/snapshots/' . $version;

        if (!$this->fs->fileExists($currentPath)) {
            throw new Exception(sprintf('Unable to read current snapshot'));
        }

        try {
            if ($this->compressSnapshots) {
                $jsonData = json_decode($this->fs->readFile('compress.zlib://' . $currentPath), true, 512, JSON_THROW_ON_ERROR);
            } else {
                $jsonData = json_decode($this->fs->readFile($currentPath), true, 512, JSON_THROW_ON_ERROR);
            }
            if (!is_array($jsonData) || !array_key_exists('_ver', $jsonData) || !array_key_exists('_data', $jsonData)) {
                throw new Exception('Unable to load current snapshot');
            }
        } catch (JsonException $e) {
            throw new Exception('Unable to load current snapshot');
        }

        $entities = [];
        foreach ($jsonData['_data'] as $key => $hash) {
            $entities[] = new Entity($key, $hash, '');
        }

        return new Snapshot($jsonData['_ver'], $entities);
    }
}