<?php

namespace Guppy;

use Exception;

class FileSystem
{
    protected Config $config;
    static protected int $readCntr = 0;
    static protected int $writeCntr = 0;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function readFile($path): string
    {
        $fp = $this->readFileToStream($path);

        self::$readCntr++;
        $contents = stream_get_contents($fp, $this->config->maxFileSize);
        if ($contents === false) {
            throw new Exception(sprintf('Unable to read contents of file %s', $path));
        }

        if (!fclose($fp)) {
            throw new Exception(sprintf('Unable to close file %s', $path));
        }

        return $contents;
    }

    public function readFileToStream($path)
    {
        $fp = fopen($path, 'r');

        if (!is_resource($fp)) {
            throw new Exception(sprintf('Unable to open file %s', $path));
        }

        return $fp;
    }

    public function writeStreamToFile($path, $stream)
    {
        $fp = fopen($path, 'w');

        if (!is_resource($fp)) {
            throw new Exception(sprintf('Unable to open file %s', $path));
        }

        self::$readCntr++;
        self::$writeCntr++;
        if (!stream_copy_to_stream($stream, $fp)) {
            throw new Exception(sprintf('Unable to write contents to file %s', $path));
        }

        if (!fclose($fp)) {
            throw new Exception(sprintf('Unable to close file %s', $path));
        }
    }

    public function writeToFile($path, $contents)
    {
        self::$writeCntr++;
        if (!file_put_contents($path, $contents)) {
            throw new Exception(sprintf('Unable to write contents to file %s', $path));
        }
    }

    public function makeDir($path)
    {
        self::$writeCntr++;
        if (!mkdir($path)) {
            throw new Exception(sprintf('Unable to make directory %s', $path));
        }
    }

    public function fileExists($path): bool
    {
        self::$readCntr++;
        return file_exists($path);
    }

   static public function getStats():array
    {
        return [
            'read' => self::$readCntr,
            'write' => self::$writeCntr,
        ];
    }
}