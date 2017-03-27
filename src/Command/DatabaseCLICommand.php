<?php

namespace Radcliffe\Larama\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DatabaseCLICommand extends Command
{
    use DatabaseOptionsTrait;

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this
            ->setName('db:cli')
            ->setDescription('Open a SQL command-line interface using application credentials.');

        $this->addDatabaseOptions($this);
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $pipes = [];

        /** @var \Illuminate\Contracts\Container\Container $container */
        $container = $this->getApplication()->getLaravel();
        /** @var \Illuminate\Contracts\Config\Repository $config */
        $config = $container->make(\Illuminate\Config\Repository::class);
        $default_connection = $config->get('database.default');

        $connection = $this->getDatabaseConnection($input, $default_connection);
        $options = $this->getDatabaseOptions($input, $config->get('database.connections.' . $connection), $connection === $default_connection);

        if (!isset($options)) {
            throw new \InvalidArgumentException('Database connection not found.');
        }

        if ($options['driver'] === 'mysql') {
            $command = $this->getMySQLCommand($options);
        } else {
            throw new \InvalidArgumentException('Driver is not supported.');
        }

        $process = proc_open($command, [0 => STDIN, 1 => STDOUT, 2 => STDERR], $pipes);
        $proc_status = proc_get_status($process);
        $exit_code = proc_close($process);

        return ($proc_status['running']) ? $exit_code : $proc_status['exitcode'];
    }

    protected function getMySQLCommand(array $config)
    {
        $command = 'mysql --user=' . $config['username'] . ' --password=' . $config['password'];
        $command .= ' --host=' . $config['host'] . ' --port=' . $config['port'];
        $command .= ' --binary-mode=1 ' . $config['database'];
        return $command;
    }
}
