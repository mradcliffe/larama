<?php

namespace Radcliffe\Larama\Console;

use Radcliffe\Larama\Command\AppStatusCommand;
use Radcliffe\Larama\Command\HelpAliasCommand;
use Radcliffe\Larama\Command\SiteAliasCommand;
use Radcliffe\Larama\Config\Environment;
use Radcliffe\Larama\Config\SiteAlias;
use Radcliffe\Larama\Console\Input\AliasInput;
use Radcliffe\Larama\Utility;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\ListCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Larama application class.
 */
class Larama extends Application
{
    use Utility;

    /**
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * @var \Radcliffe\Larama\Config\Environment
     */
    protected $environment;

    /**
     * @var \Radcliffe\Larama\Config\SiteAlias[]
     */
    protected $aliases;

    /**
     * @var array
     */
    protected $configDir;

    /**
     * {@inheritdoc}
     */
    public function __construct($name = 'larama', $version = '0.1')
    {
        $this->loadConfiguration();

        parent::__construct($name, $version);
    }

    /**
     * {@inheritdoc}
     */
    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        $alias = null;
        $input = null === $input ? new AliasInput() : $input;
        $output = null === $output ? new ConsoleOutput : $output;
        $alias_name = $input->getAlias();

        if ($alias_name) {
            // Attempt to load the environment from the site alias.
            if (isset($this->aliases[$alias_name])) {
                $alias = $this->aliases[$alias_name];
            }
        }

        // Attempt to load the environment.
        $this->environment = $this->loadEnvironment($alias);

        if ($this->environment) {
            // Run the app through Laravel container.
            $kernel = $this->environment->loadKernel();
            if (!is_a($kernel, '\Radcliffe\Larama\Console\Kernel')) {
                // Add the larama commands to the new kernel.
            }

            $status = $kernel->handle($input, $output);
            $kernel->terminate($input, $status);
        } else {
            // Run the app through this Symfony console application.
            $status = parent::run($input, $output);
        }

        exit($status);
    }

    /**
     * Load configuration for a laravel site.
     */
    protected function loadConfiguration()
    {
        $this->aliases = [];
        // Find possible configuration files.
        $directories = $this->getConfigDirectories();
        $configs = [];

        foreach ($directories as $directory_name) {
            if (realpath($directory_name)) {
                $configs += $this->findConfigFiles($directory_name);
            }
        }

        // Load and parse configuration files.
        foreach ($configs as $config_file) {
            $info = Yaml::parse(file_get_contents($config_file));
            $this->aliases = $this->mergeConfiguration($info);
        }
    }

    /**
     * Environment factory.
     *
     * @param \Radcliffe\Larama\Config\SiteAlias $alias
     *   An optional site alias.
     *
     * @return \Radcliffe\Larama\Config\Environment|null
     *   A laravel environment or null if one could not be loaded.
     */
    public function loadEnvironment(SiteAlias $alias = null)
    {
        try {
            $environment = new Environment($alias);
            $environment->loadEnvironment();
            return $environment;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get configuration (YAML) files.
     *
     * @param $directory
     *   The directory to scan.
     * @return array
     *   An array of configuration files.
     */
    protected function findConfigFiles($directory)
    {
        $configs = [];
        $files = scandir($directory);
        foreach ($files as $file_name) {
            $file_path = $directory . '/' . $file_name;
            if (preg_match('/\.(yaml|yml)$/', $file_name) && realpath($file_path)) {
                $configs[] = realpath($file_path);
            }
        }
        return $configs;
    }

    /**
     * Merge configuration into app configuration.
     *
     * @param $info
     *   Site alias configuration from configuration file.
     *
     * @return \Radcliffe\Larama\Config\SiteAlias[]
     *   An array of site aliases.
     */
    protected function mergeConfiguration($info)
    {
        $aliases = $this->aliases;

        // Merge aliases.
        if (isset($info['aliases'])) {
            foreach ($info['aliases'] as $alias => $info) {
                $aliases[$alias] = new SiteAlias($alias, $info);
            }
        }

        return $aliases;
    }

    /**
     * Set the configuration directories.
     *
     * By default: '/etc/larama/config', '~/.larama/config', and
     * '~/.config/larama'.
     *
     * @param array $directories
     *   Additional directories to use.
     *
     * @return array
     *   The config directories.
     */
    public function setConfigDirectories($directories = [])
    {
        $homedir = Utility::getHomeDirectory();
        $this->configDir = $directories + [
            '/etc/larama/config',
            $homedir . '/.larama/config',
            $homedir . '/.config/larama',
        ];

        return $this->configDir;
    }

    /**
     * Get the configuration directories.
     *
     * @return array
     *   The config directories. By default, an empty array.
     */
    public function getConfigDirectories()
    {
        if ($this->configDir === null) {
            $this->configDir = $this->setConfigDirectories();
        }

        return $this->configDir;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultCommands()
    {
        return [
            new HelpAliasCommand(),
            new ListCommand(),
            new AppStatusCommand(),
            new SiteAliasCommand(),
        ];
    }

    /**
     * Get the laravel environment.
     *
     * @return \Radcliffe\Larama\Config\Environment
     *   The environment.
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * Get the site aliases.
     *
     * @return \Radcliffe\Larama\Config\SiteAlias[]
     *   An array of site aliases.
     */
    public function getAliases()
    {
        return $this->aliases;
    }
}
