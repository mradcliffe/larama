<?php

namespace Radcliffe\Larama\Command;

use Symfony\Component\Console\Command\HelpCommand;

class HelpAliasCommand extends HelpCommand
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        parent::configure();

        $this->setHelp(<<<'EOF'
The <info>%command.name%</info> command displays help for a given command:

  <info>php %command.full_name% list</info>

You can also output the help in other formats by using the <comment>--format</comment> option:

  <info>php %command.full_name% --format=xml list</info>

To display the list of available commands, please use the <info>list</info> command.

You can also provide a "site alias" to bootstrap a Laravel console application running in a given directory:

 <info>php %application.name% @<alias> help</info>

To display the list of available aliases, please use the <info>site-alias</info> command.
EOF
        );
    }
}
