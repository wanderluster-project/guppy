<?php

namespace Guppy;

class Reader
{
    protected string $repoDir;

    public function __construct(string $repoDir)
    {
        $this->repoDir = $repoDir;
    }

    public function getCurrentSnapshotData()
    {
        $currentSnapshotPath = $this->repoDir . '/current';
        if (file_exists($currentSnapshotPath)) {
            return json_decode(file_get_contents($currentSnapshotPath), true);
        } else {
            return [];
        }
    }
}