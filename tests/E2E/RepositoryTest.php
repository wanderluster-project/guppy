<?php

namespace Guppy\Test\E2E;

use Guppy\Changeset;
use Guppy\Config;
use Guppy\Repository;
use PHPUnit\Framework\TestCase;
use Exception;
use Ramsey\Uuid\Uuid;
use Redis;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\RedisStore;
use Symfony\Component\Lock\Store\RetryTillSaveStore;

class RepositoryTest extends TestCase
{
    protected $uuid = '9e9ae208-f961-4405-ab3c-4551d97eaad7';

    public function setUp(): void
    {
        parent::setUp();
        exec('rm -Rf '.__DIR__.'/SampleRepo/*');
    }

    /**
     * @throws Exception
     */
    public function testCommit()
    {
        $redis = new Redis();
        $redis->connect('redis');
        $lockFactory =  new LockFactory(new RetryTillSaveStore(new RedisStore($redis), 100, 50));

        $config = new Config( __DIR__ . '/SampleRepo', $lockFactory);
        $changeSet = new Changeset();
        $changeSet->set('foo', file_get_contents(__DIR__ . '/sample-entity-data.json'));
        $changeSet->set('bar', '456aslfjasdlfjladsjfaasdfasdfadsfasdfasasdfasdfasdfadsfasdfasdfasdfdfsdfasdfadsfasdfasdfasdfasdfasdfasdfasdfasdfasdfasdfasfasdfasdfsdaflkasdjflkjasdlkfjaklsdjflkasdfkljasdlkfjlkasdjflkasdjlfkja');
//        $uuid = Uuid::uuid4();
        $uuid = '9b40e296-2dbf-49a4-be50-5be67c7ea6ad';
        $sut = new Repository((string)$uuid, $config);
        $sut->init();
        $sut->commit($changeSet);

        $newChangeset = new Changeset();
        $newChangeset->set('baz','woot');
        $sut->commit($newChangeset);
    }
}