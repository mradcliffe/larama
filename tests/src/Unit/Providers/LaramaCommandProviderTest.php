<?php

namespace Radcliffe\Tests\Larama\Unit\Providers;

use PHPUnit\Framework\TestCase;
use Radcliffe\Larama\Providers\LaramaCommandProvider;

class LaramaCommandProviderTest extends TestCase
{
    /**
     * @var \Radcliffe\Larama\Providers\LaramaCommandProvider
     */
    protected $provider;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $container = $this->prophesize('\Illuminate\Foundation\Contracts\Container');

        $this->provider = new LaramaCommandProvider($container->reveal());
        $this->provider->register();
    }

    /**
     * Assert that commands are returned correctly.
     */
    public function testRegister()
    {
        $expected = [
            'Radcliffe\Larama\Command\AppStatusCommand',
            'Radcliffe\Larama\Command\DatabaseCLICommand',
            'Radcliffe\Larama\Command\DatabaseDropCommand',
            'Radcliffe\Larama\Command\DatabaseDumpCommand',
        ];

        $this->assertEquals($expected, $this->provider->getCommands());
    }
}
