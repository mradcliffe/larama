<?php

namespace Radcliffe\Larama\Config;

/**
 * Defines a Laravel site application environment.
 */
class Environment
{

    /**
     * @var \Radcliffe\Larama\Config\SiteAlias
     */
    protected $alias;

    /**
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * @var \Illuminate\Contracts\Console\Application
     */
    protected $artisan;

    /**
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $dispatcher;

    /**
     * Initialize method.
     *
     * @param \Radcliffe\Larama\Config\SiteAlias|NULL $alias
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

        // Try to load the Laravel application container for the console.
        require $this->getBaseDir() . '/vendor/autoload.php';

        // Bootstrap the Laravel application.
        $this->container = new \Illuminate\Foundation\Application(realpath($this->getBaseDir()));
        $this->container->singleton(
            \Illuminate\Contracts\Console\Kernel::class,
            \Radcliffe\Larama\Console\Kernel::class
        );
        $this->container->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            \Illuminate\Foundation\Exceptions\Handler::class
        );
        // db.connection
    }

    public function loadKernel()
    {
        if ($this->isLoaded()) {
            return $this->container->make(\Illuminate\Contracts\Console\Kernel::class);
        }

        throw new \RuntimeException('Laravel container not instantiated.');
    }

    public function canLoad()
    {
        return $this->alias->getBaseDir() && realpath($this->alias->getBaseDir());
    }

    public function isLoaded()
    {
        return isset($this->container);
    }

    public function getBaseDir()
    {
        return $this->alias->getBaseDir();
    }
}
