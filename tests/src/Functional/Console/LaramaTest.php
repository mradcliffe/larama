<?php

namespace Radcliffe\Tests\Larama\Functional\Console;

use PHPUnit\Framework\TestCase;
use Radcliffe\Larama\Config\SiteAlias;
use Radcliffe\Larama\Console\Larama;

/**
 * Tests the larama app with a laravel instance.
 *
 * @group larama_functional
 */
class LaramaTest extends TestCase
{

    /**
     * Asserts that load environment is functional.
     */
    public function testLoadEnvironment()
    {
        $alias = SiteAlias::createFromDirectory('../laravel');
        $larama = new Larama('larama', '0.1');
        $larama->loadEnvironment($alias);
    }
}
