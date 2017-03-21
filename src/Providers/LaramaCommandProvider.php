<?php

namespace Radcliffe\Larama\Providers;

use Illuminate\Support\ServiceProvider;
use Radcliffe\Larama\Command\AppStatusCommand;
use Radcliffe\Larama\Command\DatabaseCLICommand;
use Radcliffe\Larama\Command\DatabaseDropCommand;
use Radcliffe\Larama\Command\DatabaseDumpCommand;

/**
 * Provide extra commands to artisan.
 */
class LaramaCommandProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->commands($this->getCommands());
    }

    /**
     * Get the commands in a testable way.
     *
     * @return array
     */
    public function getCommands()
    {
        return [
            AppStatusCommand::class,
            DatabaseCLICommand::class,
            DatabaseDropCommand::class,
            DatabaseDumpCommand::class,
        ];
    }
}
