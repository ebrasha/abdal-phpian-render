<?php

/**
 **********************************************************************
 * -------------------------------------------------------------------
 * Project Name : Abdal Phpian Render
 * File Name    : Version.php
 * Author       : Ebrahim Shafiei (EbraSha)
 * Email        : Prof.Shafiei@Gmail.com
 * Created On   : 2026-01-02 21:35:22
 * Description  : Version management class for the package
 * -------------------------------------------------------------------
 *
 * "Coding is an engaging and beloved hobby for me. I passionately and insatiably pursue knowledge in cybersecurity and programming."
 * – Ebrahim Shafiei
 *
 **********************************************************************
 */

namespace Abdal\PhpianRender;

/**
 * Version class manages package version information
 */
class Version
{
    /**
     * Package version
     * Format: MAJOR.MINOR.PATCH
     * MAJOR: Breaking changes
     * MINOR: New features (backward compatible)
     * PATCH: Bug fixes
     */
    public const VERSION = '1.3.1';

    /**
     * Get current version
     *
     * @return string Current version
     */
    public static function getVersion(): string
    {
        return self::VERSION;
    }

    /**
     * Get major version number
     *
     * @return int Major version
     */
    public static function getMajor(): int
    {
        return (int) explode('.', self::VERSION)[0];
    }

    /**
     * Get minor version number
     *
     * @return int Minor version
     */
    public static function getMinor(): int
    {
        return (int) explode('.', self::VERSION)[1];
    }

    /**
     * Get patch version number
     *
     * @return int Patch version
     */
    public static function getPatch(): int
    {
        return (int) explode('.', self::VERSION)[2];
    }

    /**
     * Compare with another version
     *
     * @param string $version Version to compare
     * @return int Returns -1 if current is less, 0 if equal, 1 if greater
     */
    public static function compare(string $version): int
    {
        return version_compare(self::VERSION, $version);
    }
}

