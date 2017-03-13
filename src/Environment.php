<?php

namespace Radcliffe\Laraman;

use Dotenv\Dotenv;
use Radcliffe\Laraman\Config\SiteAlias;

/**
 * Defines a Laravel site application environment.
 */
class Environment
{

    /**
     * @var \Radcliffe\Laraman\Config\SiteAlias
     */
    protected $alias;

    /**
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $dispatcher;

    /**
     * Initialize method.
     *
     * @param \Radcliffe\Laraman\Config\SiteAlias|NULL $alias
     *   A site alias to bootstrap.
     */
    public function __construct(SiteAlias $alias = null)
    {
        // Try to load the alias from the current working directory.
        if (!$alias) {
            $this->alias = SiteAlias::createFromDirectory(getcwd());
        } else {
            $this->alias = $alias;
        }
    }

    public function loadEnvironment()
    {
        if ($this->isLoaded()) {
            return;
        }

        if (!isset($this->alias)) {
            throw new \InvalidArgumentException('Could not find alias.');
        }

        if (!$this->canLoad()) {
            throw new \InvalidArgumentException('Could not find site directory.');
        }

        try {
            // Try to load the Laravel application container for the console.
            require $this->getBaseDir() . '/vendor/autoload.php';

            $this->container = new \Illuminate\Foundation\Application(realpath($this->getBaseDir()));
            $this->container->singleton(
                \Illuminate\Contracts\Console\Kernel::class,
                \App\Console\Kernel::class
            );
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function canLoad()
    {
        return $this->alias->getBaseDir() && realpath($this->alias->getBaseDir());
    }

    public function isLoaded()
    {
        return false;
    }

    public function getBaseDir()
    {
        return $this->alias->getBaseDir();
    }
}