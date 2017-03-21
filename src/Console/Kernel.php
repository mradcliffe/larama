<?php

namespace Radcliffe\Larama\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Symfony\Component\Console\Input\InputOption;

/**
 * Larama console application kernel.
 *
 * This is only used if there is no app console (\App\Console) as a fallback.
 */
class Kernel extends ConsoleKernel
{
    /**
     * {@inheritdoc}
     */
    protected $commands = [
        '\Radcliffe\Larama\Command\AppStatus',
        '\Radcliffe\Larama\Command\DatabaseCLI',
        '\Radcliffe\Larama\Command\DatabaseDrop',
        '\Radcliffe\Larama\Command\DatabaseDump',
    ];
}
