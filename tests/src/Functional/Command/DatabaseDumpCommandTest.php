<?php

namespace Radcliffe\Tests\Larama\Functional\Command;

use PHPUnit\Framework\TestCase;
use Radcliffe\Larama\Config\SiteAlias;
use Radcliffe\Larama\Console\Larama;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;

/**
 * Tests that database dump command is functional.
 *
 * @group larama_db
 */
class DatabaseDumpCommandTest extends TestCase
{
    protected static $result_file = 'backup.sql';

    /**
     * @var bool
     */
    protected $gzip;

    /**
     * Get the console kernel from a site alias environment.
     *
     * @return \Illuminate\Contracts\Console\Kernel
     */
    protected function getConsoleKernel()
    {
        // Bootstrap larama.
        $alias = SiteAlias::createFromDirectory('../laravel');
        $larama = new Larama('larama', '0.1');
        $environment = $larama->loadEnvironment($alias);
        return $environment->loadKernel();
    }

    /**
     * Asserts that the database dump command works.
     *
     * @todo abstract out driver to test PostgreSQL as well.
     */
    public function testExecute()
    {
        $input = new ArrayInput(['db:dump', '--result-file=backup.sql']);
        $output = new StreamOutput(fopen('php://memory', 'w', false));
        $kernel = $this->getConsoleKernel();
        $status = $kernel->handle($input, $output);
        $kernel->terminate($input, $status);
    }

    /**
     * Asserts that the database dump command works for gzip option.
     */
    public function testExecuteWithGzip()
    {
        $input = new ArrayInput(['db:dump', '--gzip', '--result-file=backup.sql']);
        $output = new StreamOutput(fopen('php://memory', 'w', false));
        $kernel = $this->getConsoleKernel();
        $status = $kernel->handle($input, $output);
        $kernel->terminate($input, $status);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $filename = self::$result_file;
        $filename .= $this->gzip ? '.gz' : '';

        // Remove
        if (file_exists($filename)) {
            unlink($filename);
        }
    }
}
