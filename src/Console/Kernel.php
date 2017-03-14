<?php

namespace Radcliffe\Larama\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Symfony\Component\Console\Input\InputOption;

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
        '\Radcliffe\Larama\Command\DatabaseDrop',
        '\Radcliffe\Larama\Command\DatabaseCLI',
        '\Radcliffe\Larama\Command\DatabaseDump',
        '\Radcliffe\Larama\Command\AppStatus',
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

    /**
     * {@inheritdoc}
     */
    protected function getArtisan()
    {
        $this->artisan = parent::getArtisan();
        $this->artisan->setName('artisan');
        $this->artisan
            ->getDefinition()
            ->addOptions([
                new InputOption(
                    'site-alias',
                    '',
                    InputOption::VALUE_OPTIONAL,
                    'Specify a site alias defined in an aliases file.'
                )
            ]);
        return $this->artisan;
    }
}
