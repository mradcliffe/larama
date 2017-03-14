<?php

namespace Radcliffe\Tests\Larama\Unit\Config;

use PHPUnit\Framework\TestCase;
use Radcliffe\Larama\Config\Environment;
use Radcliffe\Larama\Config\SiteAlias;

/**
 * Tests the environment model.
 *
 * @group larama
 */
class EnvironmentTest extends TestCase
{
    /**
     * {@inheritdoc}
     */
    public function testInit()
    {
        $env = new Environment();
        $this->assertInstanceOf('\Radcliffe\Larama\Config\Environment', $env);
    }

    /**
     * {@inheritdoc}
     */
    public function testInitWithAlias()
    {
        $env = new Environment(new SiteAlias('test', []));
        $this->assertInstanceOf('\Radcliffe\Larama\Config\Environment', $env);
    }

    public function testGetBaseDir()
    {
        $env = new Environment($this->getAlias('test', ['approot' => 'not-exists']));
        $this->assertEquals('not-exists', $env->getBaseDir());
    }

    public function testCantLoad()
    {
        $env = new Environment($this->getAlias('test', ['approot' => 'not-exists']));
        $this->assertFalse($env->canLoad());
    }

    public function testContainerNotLoaded()
    {
        $env = new Environment($this->getAlias('test', ['approot' => 'not-exists']));
        $this->assertFalse($env->isLoaded());
    }

    protected function getAlias($name = 'test', $values = [])
    {
        return new SiteAlias($name, $values);
    }
}
