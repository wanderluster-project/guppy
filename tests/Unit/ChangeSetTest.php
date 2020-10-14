<?php

namespace Guppy\Test\Unit;

use Guppy\ChangeSet;
use PHPUnit\Framework\TestCase;

class ChangeSetTest extends TestCase
{
    public function testConstructor()
    {
        $sut = new ChangeSet(['foo1' => 'bar1', 'foo2' => 'bar2']);
        $this->assertEquals(['foo1' => 'bar1', 'foo2' => 'bar2'], $sut->asArray());
    }

    public function testSetGet()
    {
        $sut = new ChangeSet();
        $this->assertEquals([], $sut->keys());
        $sut->set('foo', 'bar');
        $this->assertEquals('bar', $sut->get('foo'));
        $sut->set('foo', 'baz');
        $this->assertEquals('baz', $sut->get('foo'));
    }

    public function testKeys()
    {
        $sut = new ChangeSet(['foo1' => 'bar1', 'foo2' => 'bar2']);
        $this->assertEquals(['foo1', 'foo2'], $sut->keys());
    }

    public function testIteratorInterface()
    {
        $sut = new ChangeSet(['foo1' => 'bar1', 'foo2' => 'bar2']);
        foreach ($sut as $key => $value) {
            $this->assertEquals($value, $sut->get($key));
        }
    }

    public function testMerge()
    {
        $sut1 = new ChangeSet(['foo1' => 'bar1', 'foo2' => 'bar2', 'foo3' => null]);
        $sut2 = new ChangeSet(['foo1' => 'bar1.1', 'foo4' => 'bar4', 'foo5' => null]);
        $ret = $sut1->merge($sut2);
        $this->assertInstanceOf(ChangeSet::class, $ret);
        $this->assertEquals('bar1.1', $sut1->get('foo1'));
        $this->assertEquals('bar2', $sut1->get('foo2'));
        $this->assertEquals(null, $sut1->get('foo3'));
        $this->assertEquals('bar4', $sut1->get('foo4'));
        $this->assertEquals(null, $sut1->get('foo5'));
    }

    public function testExists(){
        $sut = new ChangeSet();
        $this->assertFalse($sut->exists('foo'));
        $sut->set('foo','bar');
        $this->assertTrue($sut->exists('foo'));
    }
}