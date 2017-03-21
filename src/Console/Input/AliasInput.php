<?php

namespace Radcliffe\Larama\Console\Input;

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputDefinition;

/**
 * Extends Symfony Console ArgvInput to extract out an alias argument.
 */
class AliasInput extends ArgvInput
{
    private $alias;

    /**
     * {@inheritdoc}
     */
    public function __construct($argv = null, InputDefinition $definition = null)
    {
        // Copy ArgvInput::__construct because it uses private properties, and these are necessary to extract the alias
        // before argument validation.
        if (null === $argv) {
            $argv = $_SERVER['argv'];
        }

        if (isset($argv[1]) && substr($argv[1], 0, 1) === '@') {
            $token = array_splice($argv, 1, 1);
            $this->parseAlias(reset($token));
        }

        parent::__construct($argv, $definition);
    }

    /**
     * Parse the alias from the argument.
     *
     * @param string $token
     *   The token to parse.
     *
     * @throws \RuntimeException
     */
    protected function parseAlias($token)
    {
        if (preg_match('/^\@[^a-z0-9\_\-\.]/', $token)) {
            throw new \RuntimeException(
                sprintf("Alias must contain only alphanumeric, underscore, hyphen or periods: %s", $token)
            );
        } elseif (strlen($token) === 1) {
            throw new \RuntimeException("No alias given.");
        } else {
            $this->addAlias(substr($token, 1));
        }
    }

    /**
     * Set the alias.
     *
     * @param string $alias
     *   The alias to set.
     */
    private function addAlias($alias)
    {
        $this->alias = $alias;
    }

    /**
     * Get the alias string.
     *
     * @return string|false
     *   The alias that is set or false.
     */
    public function getAlias()
    {
        return isset($this->alias) ? $this->alias : false;
    }
}
