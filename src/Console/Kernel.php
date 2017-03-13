<?php

namespace Radcliffe\Larama\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

/**
 * Larama console application kernel.
 *
 * This file will not execute unless run with a Laravel app configuration.
 */
class Kernel extends ConsoleKernel
{
    /**
     * {@inheritdoc}
     */
    protected $commands = [
//        '\Radcliffe\Larama\Command\DatabaseDrop',
//        '\Radcliffe\Larama\Command\DatabaseConsole',
//        '\Radcliffe\Larama\Command\DatabaseDump',
    ];

    /**
     * {@inheritdoc}
     */
    protected $bootstrappers = [
        'Illuminate\Foundation\Bootstrap\DetectEnvironment',
        'Illuminate\Foundation\Bootstrap\LoadConfiguration',
        'Illuminate\Foundation\Bootstrap\ConfigureLogging',
        'Illuminate\Foundation\Bootstrap\HandleExceptions',
        'Illuminate\Foundation\Bootstrap\RegisterFacades',
        'Illuminate\Foundation\Bootstrap\SetRequestForConsole',
        'Illuminate\Foundation\Bootstrap\RegisterProviders',
        'Illuminate\Foundation\Bootstrap\BootProviders',
    ];
}
