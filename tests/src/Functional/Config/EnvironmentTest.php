<?php

namespace Radcliffe\Tests\Larama\Functional\Config;

use PHPUnit\Framework\TestCase;
use Radcliffe\Larama\Config\Environment;
use Radcliffe\Larama\Config\SiteAlias;

/**
 * Test the Environment loading within a fully funtional laravel app.
 *
 * @group larama_functional
 */
class EnvironmentTest extends TestCase
{

    /**
     * Get a site alias to test.
     *
     * @param string $dir
     *   Directory of laravel install to test.
     *
     * @return \Radcliffe\Larama\Config\SiteAlias
     */
    protected function createAlias($dir = '../laravel')
    {
        return SiteAlias::createFromDirectory($dir);
    }

    /**
     * Asserts that environment loaded from known directory.
     */
    public function testLoadEnvironment()
    {
        $env = new Environment($this->createAlias());
        $env->loadEnvironment();
        $this->assertTrue($env->isLoaded());
    }

    /**
     * Asserts that a console kernel is returned from a known laravel installation.
     */
    public function testLoadKernel()
    {
        $env = new Environment($this->createAlias());
        $env->loadEnvironment();
        $kernel = $env->loadKernel();
        $this->assertInstanceOf('App\Console\Kernel', $kernel);
    }

    /**
     * Asserts that an exception is thrown when not loaded.
     *
     * @expectedException \RuntimeException
     */
    public function testLoadKernelException()
    {
        $env = new Environment($this->createAlias('../laravel'));
        $env->loadKernel();
    }

}
