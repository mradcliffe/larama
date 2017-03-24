<?php

namespace Radcliffe\Tests\Larama\Unit\Command;

use PHPUnit\Framework\TestCase;
use Radcliffe\Larama\Command\AppStatusCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;

/**
 * Tests the status command.
 *
 * @group larama
 */
class AppStatusCommandTest extends TestCase
{
    /**
     * Asserts status command when no laravel instance.
     */
    public function testStatus()
    {
        $definition = new InputDefinition();
        $definition->addArgument(new InputArgument('command'));

        $appProphecy = $this->prophesize('\Radcliffe\Larama\Console\Larama');
        $helperProphecy = $this->prophesize('\Symfony\Component\Console\Helper\HelperSet');
        $appProphecy->getHelperSet()->willReturn($helperProphecy->reveal());
        $appProphecy->getName()->willReturn('larama (Laravel Manager)');
        $appProphecy->getVersion()->willReturn('0.1');
        $appProphecy->getDefinition()->willReturn($definition);

        $command = new AppStatusCommand('status');
        $command->setApplication($appProphecy->reveal());
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => 'status'));

        $this->assertRegExp('/Console application \: larama/', $commandTester->getDisplay());
    }
}