<?php

namespace Radcliffe\Larama\Command;

use Radcliffe\Larama\Utility;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DatabaseDump extends Command
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this
            ->setName('db:dump')
            ->setDescription('Dump the database into a SQL file.')
            ->addOption('--gzip', '', InputOption::VALUE_NONE, 'Compress the database dump.')
            ->addOption('result-file', '', InputOption::VALUE_OPTIONAL, 'Provide a the full path to the filename to save the dump.');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $result_code = 0;
        $file_suffix = '';

        /** @var \Illuminate\Contracts\Container\Container $container */
        $container = $this->getApplication()->getLaravel();
        /** @var \Illuminate\Config\Repository $config */
        $config = $container->make(\Illuminate\Config\Repository::class);

        $driver = $config->get('database.default');
        if ($driver === 'mysql') {
            $command = $this->getMySQLDumpCommand($config->get('database.connections.' . $driver));
        } elseif ($driver === 'pgsql') {
            $command = $this->getPostgreSQLDumpCommand($config->get('database.connections.' . $driver));
        } else {
            throw new \InvalidArgumentException('Database driver not supported.');
        }


        if ($input->getOption('gzip')) {
            $command .= ' | gzip -f';
            $file_suffix .= '.gz';
        }

        if ($input->getOption('result-file')) {
            $file = $input->getOption('result-file');
            $file .= $file_suffix;
            $command .= ' > ' . Utility::escapeShellArguments($file);
        }

        system($command, $result_code);
        $output->writeln($command, OutputInterface::VERBOSITY_VERBOSE);

        return $result_code;
    }

    protected function getMySQLDumpCommand(array $config)
    {
        $command = 'mysqldump';
        $command .= ' ' . $config['database'];
        $command .= ' --host=' . $config['host'];
        $command .= ' --port=' . $config['port'];
        $command .= ' --user=' . $config['username'];
        $command .= ' --password=' . $config['password'];
        $command .= ' --no-autocommit --single-transaction --opt -Q';

        return $command;
    }

    protected function getPostgreSQLDumpCommand(array $config)
    {
        $command = 'pg_dump';
        $command .= ' -b -x -F plain -n ' . $config['schema'];
        $command .= ' --disable-triggers --if-exists --quote-all-identifiers';
        $command .= ' -h ' . $config['host'];
        $command .= ' -p ' . $config['port'];
        $command .= ' -U ' . $config['username'];
        $command .= ' ' . $config['database'];

        return $command;
    }
}
