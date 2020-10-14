<?php

namespace Guppy;

use Exception;
use Symfony\Component\Lock\LockFactory;

class Config
{
    public string $hashAlgorithm = 'sha256';
    public string $compressionAlgorithm = 'gz';
    public int $compressionLevel = 6;
    public string $baseDir;
    public LockFactory $lockFactory;

    /**
     * Config constructor.
     * @param string $baseDir
     * @param LockFactory $lockFactory
     */
    public function __construct(string $baseDir, LockFactory $lockFactory)
    {
        $this->baseDir = $baseDir;
        $this->lockFactory = $lockFactory;
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

        if (!in_array($this->compressionAlgorithm, ['gz', 'none'])) {
            throw new Exception(sprintf('Invalid Config - compressionAlgorithm = %s', $this->compressionAlgorithm));
        }
        if (!is_int($this->compressionLevel)) {
            throw new Exception(sprintf('Invalid Config - compressionLevel = %s', $this->compressionLevel));
        }

        if (!$this->lockFactory) {
            throw new Exception(sprintf('Invalid Config - lockFactory'));
        }
    }
}