<?php

/**
 **********************************************************************
 * -------------------------------------------------------------------
 * Project Name : Abdal Phpian Render
 * File Name    : VersionTest.php
 * Author       : Ebrahim Shafiei (EbraSha)
 * Email        : Prof.Shafiei@Gmail.com
 * Created On   : 2026-01-02 21:35:22
 * Description  : Unit tests for Version class
 * -------------------------------------------------------------------
 *
 * "Coding is an engaging and beloved hobby for me. I passionately and insatiably pursue knowledge in cybersecurity and programming."
 * – Ebrahim Shafiei
 *
 **********************************************************************
 */

namespace Abdal\PhpianRender\Tests;

use Abdal\PhpianRender\Version;
use PHPUnit\Framework\TestCase;

/**
 * Test cases for Version class
 */
class VersionTest extends TestCase
{
    public function testGetVersion(): void
    {
        $version = Version::getVersion();
        $this->assertNotEmpty($version);
        $this->assertIsString($version);
        $this->assertMatchesRegularExpression('/^\d+\.\d+\.\d+$/', $version);
    }

    public function testGetMajor(): void
    {
        $major = Version::getMajor();
        $this->assertIsInt($major);
        $this->assertGreaterThanOrEqual(0, $major);
    }

    public function testGetMinor(): void
    {
        $minor = Version::getMinor();
        $this->assertIsInt($minor);
        $this->assertGreaterThanOrEqual(0, $minor);
    }

    public function testGetPatch(): void
    {
        $patch = Version::getPatch();
        $this->assertIsInt($patch);
        $this->assertGreaterThanOrEqual(0, $patch);
    }

    public function testCompare(): void
    {
        $currentVersion = Version::getVersion();
        
        // Compare with same version
        $this->assertEquals(0, Version::compare($currentVersion));
        
        // Compare with lower version
        $this->assertGreaterThanOrEqual(0, Version::compare('0.0.1'));
        
        // Compare with higher version
        $this->assertLessThanOrEqual(0, Version::compare('999.999.999'));
    }
}

