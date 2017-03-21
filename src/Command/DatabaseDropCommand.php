<?php

namespace Radcliffe\Larama\Command;

use Illuminate\Database\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Drop all database tables without dropping the database.
 */
class DatabaseDropCommand extends Command
{

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('db:drop')
            ->setDescription('Drop database tables and constraints.')
            ->addOption('yes', 'y', InputOption::VALUE_NONE, 'Skip confirmation and proceed.');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getOption('yes')) {
            $helper = $this->getHelper('question');
            $question = new ConfirmationQuestion('Are you sure you want to drop the database? ', false);
            if (!$helper->ask($input, $output, $question)) {
                return;
            }
        }

        $this->output = $output;

        // Get the database connection.
        $container = $this->getApplication()->getLaravel();
        /** @var \Illuminate\Database\DatabaseManager $database */
        $database = $container->make(\Illuminate\Database\DatabaseManager::class);
        /** @var \Illuminate\Database\Connection $connection */
        $connection = $database->connection();

        // Find and drop tables, views, etc...
        try {
            $connection->beginTransaction();
            if ($connection->getDriverName() === 'mysql') {
                $this->dropMySQL($connection);
            } else {
                $this->drop($connection);
            }
            $connection->commit();
        } catch (\PDOException $e) {
            $connection->rollBack();
            $output->writeln($e->getMessage(), OutputInterface::OUTPUT_NORMAL);
        }

        return 0;
    }

    /**
     * MySQL does not provide a clean way to drop tables, views, etc... in one go.
     *
     * @param \Illuminate\Database\Connection $connection
     *   The database connection.
     */
    protected function dropMySQL(Connection $connection)
    {
        $pdo = $connection->getPdo();
        $tables = $pdo
            ->query("SHOW FULL TABLES;")
            ->fetchAll();

        $pdo->query('SET FOREIGN_KEY_CHECKS=0;');
        foreach ($tables as $info) {
            if ($info[1] === 'BASE TABLE') {
                $table_name = $info[0];
                $this->output->writeln('Dropping table ' . $table_name, OutputInterface::VERBOSITY_VERBOSE);
                $pdo->query('DROP TABLE IF EXISTS ' . $table_name . ' CASCADE;');
            } elseif ($info[1] === 'VIEW') {
                $name = $info[0];
                $this->output->writeln('Dropping view ' . $name, OutputInterface::VERBOSITY_VERBOSE);
                $pdo->query('DROP VIEW IF EXISTS ' . $name . ' CASCADE;');
            }
        }
        $pdo->query('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Cascade drop.
     *
     * @param \Illuminate\Database\Connection $connection
     *   The database connection.
     */
    protected function drop(Connection $connection)
    {
        $result = $connection->select(
            "SELECT tablename FROM pg_tables WHERE schemaname = :schema",
            [':schema' => $connection->getConfig('schema')]
        );

        foreach ($result as $row) {
            $this->output->writeln('Dropping table ' . $row->tablename, OutputInterface::VERBOSITY_VERBOSE);
            $connection->getPdo()->query('DROP TABLE IF EXISTS ' . $row->tablename . ' CASCADE');
        }
    }
}
