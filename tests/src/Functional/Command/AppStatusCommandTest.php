<?php

namespace Radcliffe\Tests\Larama\Command;

use PHPUnit\Framework\TestCase;
use Radcliffe\Larama\Config\SiteAlias;
use Radcliffe\Larama\Console\Larama;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;

/**
 * Tests the status command within a laravel instance.
 *
 * @group larama_functional
 */
class AppStatusCommandTest extends TestCase
{
    /**
     * Asserts that status output from a laravel instance.
     */
    public function testExecute()
    {
        $input = new ArrayInput(['status']);
        $output = new StreamOutput(fopen('php://memory', 'w', false));

        // Bootstrap larama.
        $alias = SiteAlias::createFromDirectory('../laravel');
        $larama = new Larama('larama', '0.1');
        $environment = $larama->loadEnvironment($alias);
        $kernel = $environment->loadKernel();

        $status = $kernel->handle($input, $output);
        $kernel->terminate($input, $status);

        // Get the actual output from the output stream.
        rewind($output->getStream());
        $actual = stream_get_contents($output->getStream());

        $this->assertRegExp('/Laravel version\s+\:\s(\d\.\d\.\d)/', $actual);
    }
}
