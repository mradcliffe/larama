<?php

namespace Radcliffe\Larama\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

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
        '\Radcliffe\Larama\Command\AppStatusCommand',
        '\Radcliffe\Larama\Command\DatabaseCLICommand',
        '\Radcliffe\Larama\Command\DatabaseDropCommand',
        '\Radcliffe\Larama\Command\DatabaseDumpCommand',
    ];
}
