<?php

namespace Radcliffe\Tests\Larama\Unit\Console;

use PHPUnit\Framework\TestCase;
use Radcliffe\Larama\Console\Larama;

/**
 * Tests the larama application for some basic stuff. Not very useful.
 *
 * @group larama
 */
class LaramaTest extends TestCase
{

    public function testGetEnvironment()
    {
        $app = new Larama();
        $this->assertEquals(null, $app->getEnvironment());
    }

    public function testGetAliases()
    {
        $app = new Larama();
        $this->assertTrue(is_array($app->getAliases()));
    }
}
