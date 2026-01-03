<?php

/**
 **********************************************************************
 * -------------------------------------------------------------------
 * Project Name : Abdal Phpian Render
 * File Name    : DiacriticsHandlerTest.php
 * Author       : Ebrahim Shafiei (EbraSha)
 * Email        : Prof.Shafiei@Gmail.com
 * Created On   : 2026-01-02 21:35:22
 * Description  : Unit tests for DiacriticsHandler class
 * -------------------------------------------------------------------
 *
 * "Coding is an engaging and beloved hobby for me. I passionately and insatiably pursue knowledge in cybersecurity and programming."
 * – Ebrahim Shafiei
 *
 **********************************************************************
 */

namespace Abdal\PhpianRender\Tests;

use Abdal\PhpianRender\DiacriticsHandler;
use PHPUnit\Framework\TestCase;

/**
 * Test cases for DiacriticsHandler class
 */
class DiacriticsHandlerTest extends TestCase
{
    private DiacriticsHandler $handler;

    protected function setUp(): void
    {
        $this->handler = new DiacriticsHandler();
    }

    public function testExtractDiacritics(): void
    {
        $text = 'بِت';
        $result = $this->handler->extract($text);
        $this->assertArrayHasKey('base', $result);
        $this->assertArrayHasKey('diacritics', $result);
    }

    public function testApplyDiacritics(): void
    {
        $base = 'بت';
        $diacritics = [0 => ["\u{0650}"]];
        $result = $this->handler->apply($base, $diacritics);
        $this->assertNotEmpty($result);
    }

    public function testRemoveDiacritics(): void
    {
        $text = 'بِت';
        $result = $this->handler->remove($text);
        $this->assertEquals('بت', $result);
    }

    public function testIsDiacritic(): void
    {
        $this->assertTrue($this->handler->isDiacritic("\u{064E}")); // Fatha
        $this->assertTrue($this->handler->isDiacritic("\u{064F}")); // Damma
        $this->assertTrue($this->handler->isDiacritic("\u{0650}")); // Kasra
        $this->assertFalse($this->handler->isDiacritic('ب'));
    }

    public function testExtractNoDiacritics(): void
    {
        $text = 'بت';
        $result = $this->handler->extract($text);
        $this->assertEquals('بت', $result['base']);
        $this->assertEmpty($result['diacritics']);
    }
}

