<?php

namespace Radcliffe\Larama\Console;

use Radcliffe\Larama\Config\SiteAlias;
use Radcliffe\Larama\Utility;
use Symfony\Component\Console\Application;
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
     * @var \Radcliffe\Larama\Environment
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
    public function __construct($name = 'UNKNOWN', $version = 'UNKNOWN')
    {
        parent::__construct($name, $version);

        $this->loadConfiguration();
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
     * By default: '/etc/laraman/config', '~/.laraman/config', and
     * '~/.config/laraman'.
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
            '/etc/laraman/config',
            $homedir . '/.laraman/config',
            $homedir . '/.config/laraman',
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
            $this->configDir = $this->setConfigDirectories ();
        }

        return $this->configDir;
    }
}
