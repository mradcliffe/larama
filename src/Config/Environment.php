<?php

namespace Radcliffe\Larama\Config;

use Radcliffe\Larama\Providers\LaramaCommandProvider;

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

    /**
     * Load the Laravel environment.
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
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
        $loader = require $this->getBaseDir() . '/vendor/autoload.php';

        $class_name = $this->findConsoleKernel($loader);

        // Bootstrap the Laravel application.
        $this->container = new \Illuminate\Foundation\Application(realpath($this->getBaseDir()));
        $this->dispatcher = $this->container->make(\Illuminate\Contracts\Events\Dispatcher::class);

        // Load the console kernel into the container.
        $this->container->singleton(
            \Illuminate\Contracts\Console\Kernel::class,
            $class_name
        );

        // Load the framework debug handler into the container.
        $this->container->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            \Illuminate\Foundation\Exceptions\Handler::class
        );

        // Inject the command provider into the container.
        $this->container->register(LaramaCommandProvider::class, [], true);
    }

    /**
     * Load the console kernel for the given application.
     *
     * @return \Illuminate\Contracts\Console\Kernel
     *   A console application.
     */
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

    /**
     * Find the console kernel in the laravel application.
     *
     * @param \Composer\Autoload\ClassLoader $loader
     *   The autoloader.
     *
     * @return string
     *   The class name of the console kernel to use.
     */
    protected function findConsoleKernel($loader)
    {
        $classmap = $loader->getClassMap();
        $classes = array_keys($classmap);

        // Find the first console kernel that implements the console contract. Hopefully this works well.
        $name = array_reduce($classes, function (&$result, $class_name) {
            if (!$result &&
                is_subclass_of($class_name, '\Illuminate\Contracts\Console\Kernel') &&
                ($class_name !== '\Illuminate\Foundation\Console\Kernel' ||
                $class_name !== '\Radcliffe\Larama\Console\Kernel')) {
                $result = $class_name;
            }
            return $result;
        }, false);

        // Fallback to a sensible default.
        if (!$name) {
            $name = '\Radcliffe\Larama\Console\Kernel';
        }

        return $name;
    }
}
