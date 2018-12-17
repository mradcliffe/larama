<?php

namespace Radcliffe\Tests\Larama\Unit\Command;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

/**
 * Tests the database options trait.
 *
 * @group larama
 */
class DatabaseOptionsTraitTest extends TestCase
{
    /**
     * Asserts that the connection is returned.
     *
     * @param string $expected
     *   The expected value.
     * @param string $option
     *   Option to use for the database connection.
     *
     * @dataProvider connectionProvider
     */
    public function testGetDatabaseConnection($expected, $option)
    {
        $mock = $this->getMockForTrait('\Radcliffe\Larama\Command\DatabaseOptionsTrait');

        $inputProphecy = $this->prophesize('\Symfony\Component\Console\Input\InputInterface');
        $inputProphecy->getOption('connection')->willReturn($option);
        $input = $inputProphecy->reveal();

        $this->assertEquals($expected, $mock->getDatabaseConnection($input, 'mysql'));
    }

    /**
     * Tests the database options.
     *
     * @param array $expected
     *   The expected values.
     * @param array $options
     *   The options array to test.
     *
     * @dataProvider optionsProvider
     */
    public function testGetDatabaseOptions($expected, $options)
    {
        $mock = $this->getMockForTrait('\Radcliffe\Larama\Command\DatabaseOptionsTrait');
        $defaults = [
            'driver' => 'mysql',
            'database' => 'homestead',
            'username' => 'homestead',
            'password' => 'secret',
            'host' => '127.0.0.1',
            'port' => '3306',
        ];

        $inputProphecy = $this->prophesize('\Symfony\Component\Console\Input\InputInterface');
        $inputProphecy->getOption(Argument::type('string'))->will(function ($args) use ($options) {
            $option = $args[0];
            return isset($options[$option]) ? $options[$option] : null;
        });
        $input = $inputProphecy->reveal();

        $this->assertEquals($expected, $mock->getDatabaseOptions($input, $defaults));
    }

    /**
     * Get parameters for tests.
     *
     * @return array
     *   An array of parameters.
     */
    public function connectionProvider()
    {
        return [
            ['dummy', 'dummy'],
            ['mysql', null],
        ];
    }

    /**
     * Get parameters for the options test.
     *
     * @return array
     *   An array of parameters.
     */
    public function optionsProvider()
    {
        return [
            [
                [
                    'driver' => 'mysql',
                    'database' => 'homestead',
                    'username' => 'homestead',
                    'host' => '127.0.0.1',
                    'port' => '3306',
                    'password' => 'secret',
                ],
                [],
            ],
            [
                [
                    'driver' => 'mysql',
                    'database' => 'dummy',
                    'username' => 'homestead',
                    'host' => '127.0.0.1',
                    'port' => '3306',
                    'password' => 'secret',
                ],
                ['database' => 'dummy'],
            ],
            [
                [
                    'driver' => 'mysql',
                    'database' => 'homestead',
                    'username' => 'dummy',
                    'host' => '127.0.0.1',
                    'port' => '3306',
                    'password' => 'secret',
                ],
                ['username' => 'dummy'],
            ],
            [
                [
                    'driver' => 'mysql',
                    'database' => 'homestead',
                    'username' => 'homestead',
                    'host' => 'localhost',
                    'port' => '3306',
                    'password' => 'secret',
                ],
                ['host' => 'localhost'],
            ],
            [
                [
                    'driver' => 'mysql',
                    'database' => 'homestead',
                    'username' => 'homestead',
                    'host' => '127.0.0.1',
                    'port' => '5000',
                    'password' => 'secret',
                ],
                ['port' => '5000'],
            ],
            [
                [
                    'driver' => 'mysql',
                    'database' => 'homestead',
                    'username' => 'homestead',
                    'host' => '127.0.0.1',
                    'port' => '3306',
                    'password' => 'dummy',
                ],
                ['password' => 'dummy'],
            ],
        ];
    }
}
