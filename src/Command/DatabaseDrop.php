<?php

namespace Radcliffe\Larama\Command;

use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Drop all database tables without dropping the database.
 */
class DatabaseDrop extends Command {

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
            $question = new ConfirmationQuestion('Are you sure you want to drop the database? ', FALSE);
            if (!$helper->ask($input, $output, $question)) {
                return;
            }
        }

        $container = $this->getApplication()->getLaravel();
        $database = $container->make(\Illuminate\Database\DatabaseManager::class);
        $connection = $database->connection();
        var_dump($connection);

        return 0;
    }
}
