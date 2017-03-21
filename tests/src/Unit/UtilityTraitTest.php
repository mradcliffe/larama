<?php

namespace Radcliffe\Tests\Larama\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Tests the utility trait.
 *
 * @group larama
 */
class UtilityTraitTest extends TestCase
{

    protected $mock;

    protected function setUp()
    {
        $this->mock = $this->getMockForTrait('\Radcliffe\Larama\Utility');
    }

    public function testHomeDirectoryUnix()
    {
        $home = isset($_SERVER['HOMEDRIVE']) && isset($_SERVER['HOMEPATH']) ? $_SERVER['HOMEDRIVE'] . $_SERVER['HOMEPATH'] : getenv('HOME');
        $this->assertEquals($home, $this->mock->getHomeDirectory());
    }

    /**
     * Tests that the escape shell arguments function is secure.
     *
     * @param string $args
     *   The shell arguments.
     * @param string $expected
     *   The expected value.
     *
     * @dataProvider escapeShellArgsProvider
     */
    public function testEscapeShellArgs($args, $expected)
    {
        $this->assertEquals($expected, $this->mock->escapeShellArguments($args));
    }

    /**
     * Escape shell arguments data provider.
     */
    public function escapeShellArgsProvider()
    {
        return [
            ['', ''],
            ['/tmp/blah.sql', '/tmp/blah.sql'],
            ['test\'', 'test\'\\\'\''],
        ];
    }
}
