<?php

namespace Guppy;

class Compressor
{
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function compress(string $data): string
    {
        switch ($this->config->compressionAlgorithm) {
            case 'gz':
                return gzencode($data, 9);
                break;
            default:
                return $data;
        }
    }
}