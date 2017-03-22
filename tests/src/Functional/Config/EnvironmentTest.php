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
     * Asserts that environment loaded from known directory.
     */
    public function testLoadEnvironment()
    {
        $alias = SiteAlias::createFromDirectory('../laravel');
        $env = new Environment($alias);
        $env->loadEnvironment();
        $this->assertTrue($env->isLoaded());
    }
}
