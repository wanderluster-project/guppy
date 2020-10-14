<?php

namespace Guppy;

class Hasher
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * Hasher constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $string
     * @return string
     */
    public function hash(string $string)
    {
        return hash($this->config->hashAlgorithm, $string);
    }
}