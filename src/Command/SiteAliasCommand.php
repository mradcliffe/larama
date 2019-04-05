<?php

namespace Radcliffe\Larama\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SiteAliasCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this
            ->setName('site-alias')
            ->setDescription('List all available site aliases.')
            ->setHelp(<<<EOF
Larama will search for site aliases in the following directories:

/etc/larama/config
\$HOME/.config/larama
\$HOME/.larama/config

Alias files are defined as YAML files as an associative array keyed by <info>aliases</info>:

<info>aliases</info>:
  <info>name</info>:
    <info>approot</info>: '/var/www/example.com'
    <info>url</info>: 'http://example.com'
    <info>fqdn</info>: 'example.com'
</info>
EOF
            );
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $aliases = $this->getApplication()->getAliases();

        if (!empty($aliases)) {
            $elements = array_map(function ($element) {
                return sprintf(' * @%s', $element->getAlias());
            }, $aliases);
            $output->writeln($elements);
        } else {
            $output->writeln('<info>No site aliases found.</info>');
        }
    }
}
