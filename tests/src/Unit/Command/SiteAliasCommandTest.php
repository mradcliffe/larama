<?php

namespace Radcliffe\Tests\Larama\Unit\Command;

use PHPUnit\Framework\TestCase;
use Radcliffe\Larama\Command\SiteAliasCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;

/**
 * Tests site alias command.
 *
 * @group larama
 */
class SiteAliasCommandTest extends TestCase
{
    /**
     * Get application mock.
     *
     * @param \Radcliffe\Larama\Config\SiteAlias[] $aliases
     *   An array of site aliases
     *
     * @return Double\Radcliffe\Larama\Console\Larama
     */
    protected function getApplicationMock(array $aliases)
    {
        $definition = new InputDefinition();
        $definition->addArgument(new InputArgument('command'));

        $appProphecy = $this->prophesize('\Radcliffe\Larama\Console\Larama');
        $helperProphecy = $this->prophesize('\Symfony\Component\Console\Helper\HelperSet');
        $appProphecy->getHelperSet()->willReturn($helperProphecy->reveal());
        $appProphecy->getName()->willReturn('larama (Laravel Manager)');
        $appProphecy->getVersion()->willReturn('0.1');
        $appProphecy->getDefinition()->willReturn($definition);
        $appProphecy->getAliases()->willReturn($aliases);

        return $appProphecy->reveal();
    }

    /**
     * Asserts that site-alias command returns formatted alias name.
     */
    public function testExecuteWithAliases()
    {
        $aliasProphecy = $this->prophesize('\Radcliffe\Larama\Config\SiteAlias');
        $aliasProphecy->getAlias()->willReturn('example');

        $aliases = [$aliasProphecy->reveal()];

        $command = new SiteAliasCommand('site-alias');
        $command->setApplication($this->getApplicationMock($aliases));
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => 'site-alias'));

        $this->assertEquals("\n * @example\n\n", $commandTester->getDisplay());
    }

    /**
     * Asserts that site-alias command prints no site aliases found.
     */
    public function testExecuteWithNoAliases()
    {
        $aliases = [];

        $command = new SiteAliasCommand('site-alias');
        $command->setApplication($this->getApplicationMock($aliases));
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => 'site-alias'));

        $this->assertEquals("No site aliases found.\n", $commandTester->getDisplay());
    }
}
