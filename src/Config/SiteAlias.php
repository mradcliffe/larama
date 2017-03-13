<?php

namespace Radcliffe\Laraman\Config;

/**
 * A configuration model for a Laravel application.
 */
class SiteAlias
{

    /**
     * @var string
     */
    protected $alias;

    /**
     * @var string
     */
    protected $fqdn;

    /**
     * @var string
     */
    protected $webRoot;

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * Initialize method.
     *
     * @param $alias
     *   The site alias name.
     * @param array $values
     *   An array of values to set for the site.
     */
    public function __construct($alias, array $values)
    {
        $this->alias = $alias;

        if (isset($values['fqdn'])) {
            $this->fqdn = $values['fqdn'];
        }

        if (isset($values['webroot'])) {
            $this->webRoot = $values['webroot'];
        }

        if (isset($values['url'])) {
            $this->baseUrl = $values['url'];
        }
    }

    public static function createFromDirectory($directory)
    {
        if (realpath($directory) && self::isLaravelRoot($directory)) {
            return new static(
                basename($directory),
                array(
                    'webroot' => $directory,
                )
            );
        } elseif ($directory !== '/' && realpath($directory . '/..')) {
            return self::createFromDirectory(realpath($directory . '/..'));
        }

        // Fallback to an empty alias.
        return new static(null, []);
    }

    /**
     * Check if directory is a potential laravel root.
     *
     * @param string $directory
     *   The directory to check.
     * @return bool
     *   TRUE if the directory is a laravel root.
     */
    protected static function isLaravelRoot($directory)
    {
        $exists = realpath($directory . '/vendor/laravel/framework');
        return $exists === false ? false : true;
    }


    /**
     * Get the base directory.
     *
     * @return string
     *   The base directory for the application.
     */
    public function getBaseDir()
    {
        return $this->webRoot;
    }

    /**
     * Get the alias name.
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Get the fully-qualified domain name.
     *
     * @return string
     */
    public function getFQDN()
    {
        return $this->fqdn;
    }

    /**
     * Get the URL.
     *
     * @return string
     */
    public function getBaseURL()
    {
        return $this->baseUrl;
    }
}
