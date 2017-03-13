<?php

namespace Radcliffe\Laraman;

/**
 * Provides helper functions for the application.
 */
trait Utility
{

    public static function escapeShellArguments($value)
    {
        if (preg_match('[^a-zA-Z0-9.:/_-]*$|', $value)) {
            return $value;
        }

        $value = preg_replace('/\'/', '\'\\\'\'', $value);
        $value = str_replace(["\t", "\n", "\r", "\0", "\x0B"], ' ', $value);
        return $value;
    }

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
}