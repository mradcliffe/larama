<?php

namespace Radcliffe\Larama;

/**
 * Provides helper functions for the application.
 */
trait Utility
{

    /**
     * Escape shell argument string.
     *
     * @param string $value
     *   The shell arguments.
     *
     * @return string
     *   Escaped shell arguments.
     */
    public static function escapeShellArguments($value)
    {
        if (preg_match('[^a-zA-Z0-9.:/_-]*$|', $value)) {
            return $value;
        }

        $value = preg_replace('/\'/', '\'\\\'\'', $value);
        $value = str_replace(["\t", "\n", "\r", "\0", "\x0B"], ' ', $value);
        return $value;
    }

    /**
     * Get the user's home directory.
     *
     * @return string
     *   A valid path.
     */
    public static function getHomeDirectory()
    {
        $home = getenv('HOME');
        if ($home) {
            $home = preg_replace('/\$/', '', $home);
        } elseif (isset($_SERVER['HOMEDRIVE']) && isset($_SERVER['HOMEPATH'])) {
            $home = $_SERVER['HOMEDRIVE'] .  $_SERVER['HOMEPATH'];
        }
        return $home;
    }

    /**
     * Check if a valid web URL RFC 2396.
     *
     * @param string $url
     *   An URL to check.
     *
     * @return bool
     *   TRUE if the input URL is a valid URL.
     */
    public static function isValidURL($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL) && preg_match('/^(http|https)\:\/\//', $url);
    }
}
