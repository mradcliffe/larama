<?php

namespace Radcliffe\Larama\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * Provides shared options for database commands.
 */
trait DatabaseOptionsTrait
{
    /**
     * Add connection options to database commands.
     *
     * @param Command $command
     *   The database command to attach the options to.
     */
    protected function addDatabaseOptions(Command $command)
    {
        $command
            ->addOption('connection', '', InputOption::VALUE_OPTIONAL, 'Provide the database connection string instead of using the default connection.')
            ->addOption('host', '', InputOption::VALUE_OPTIONAL, 'Provide the database host to connect to.')
            ->addOption('port', '', InputOption::VALUE_OPTIONAL, 'Provide the database port to connect to.')
            ->addOption('database', '', InputOption::VALUE_OPTIONAL, 'Provide the database to connect to.')
            ->addOption('username', '', InputOption::VALUE_OPTIONAL, 'Provide the database user name to connect with.')
            ->addOption('password', '', InputOption::VALUE_OPTIONAL, 'Provide the database password to connect with.')
            ->addOption('schema', '', InputOption::VALUE_OPTIONAL, 'Provide the database schema to connect to (PostgreSQL only).');
    }

    /**
     * Get the database connection to use.
     *
     * @param \SymfonyComponent\Console\Input\InputInterface $input
     *   The input parameters for the command execution.
     * @param string $default
     *   The default configuration.
     *
     * @return string
     *   The database connection to use.
     */
    public function getDatabaseConnection(InputInterface $input, $default = '')
    {
        $connection = $input->getOption('connection');
        return $connection ? $connection : $default;
    }

    /**
     * Get a database parameter either from options or configuration.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *   The input parameters for the command execution.
     * @param array $config
     *   The configuration array for the connection.
     * @param bool $useConnection
     *   Whether or not to always default to using the configuration array.
     *
     * @return array
     *   The database configuration to use for the command.
     */
    public function getDatabaseOptions(InputInterface $input, array $config, $useConnection = false)
    {
        $values = $config;
        $keys = ['database', 'host', 'port', 'username', 'password', 'schema'];

        foreach ($config as $option => $value) {
            if (in_array($option, $keys)) {
                $input_value = $input->getOption($option);
                $values[$option] = $input_value ? $input_value : $value;
            }
        }

        return $values;
    }
}
