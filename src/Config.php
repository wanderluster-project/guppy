<?php

namespace Guppy;

use Exception;

class Config
{
    public string $hashAlgorithm = 'sha256';
    public bool $compressEntities = true;
    public bool $compressSnapshots = true;
    public string $baseDir;
    public int $maxFileSize = 10000000;

    /**
     * Config constructor.
     * @param string $baseDir
     */
    public function __construct(string $baseDir)
    {
        $this->baseDir = $baseDir;
    }

    /**
     * @throws Exception
     */
    public function validateConfigs()
    {
        if (!in_array($this->hashAlgorithm, hash_algos())) {
            throw new Exception(sprintf('Invalid Config - hashAlgorithm = %s', $this->hashAlgorithm));
        }

        if (!$this->baseDir) {
            throw new Exception(sprintf('Invalid Config - baseDir = %s', $this->baseDir));
        }

        if (!file_exists($this->baseDir)) {
            throw new Exception(sprintf('Invalid Config - baseDir does not exist = %s', $this->baseDir));
        }
    }
}