<?php

namespace Guppy\Test\E2E;

use Guppy\Changeset;
use Guppy\Config;
use Guppy\Repository;
use PHPUnit\Framework\TestCase;
use Exception;
use Ramsey\Uuid\Uuid;

class RepositoryTest extends TestCase
{
    protected $uuid = '9e9ae208-f961-4405-ab3c-4551d97eaad7';

    public function setUp(): void
    {
        parent::setUp();
        exec('rm -Rf ' . __DIR__ . '/Workspace/*');
    }

    /**
     * @throws Exception
     */
    public function testCommit()
    {
        $config = new Config(__DIR__ . '/Workspace');
        $config->compressEntities = false;
        $config->compressSnapshots = false;
        $changeSet = new Changeset();
        $changeSet->set('foo', file_get_contents(__DIR__ . '/sample-entity-data.json'));
        $changeSet->set('bar', '456aslfjasdlfjladsjfaasdfasdfadsfasdfasasdfasdfasdfadsfasdfasdfasdfdfsdfasdfadsfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasfasdfasdfsdaflkasdjflkjasdlkfjaklsdjflkasdfkljasdlkfjlkasdjflkasdjlfkja');
        $uuid = Uuid::uuid4();
        $sut = new Repository((string)$uuid, $config);
        $sut->init();
        $sut->commit($changeSet);

        $newChangeset = new Changeset();
        $newChangeset->set('baz', 'woot');
        $sut->commit($newChangeset);
    }
}