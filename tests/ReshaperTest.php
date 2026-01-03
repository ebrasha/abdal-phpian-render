<?php

/**
 **********************************************************************
 * -------------------------------------------------------------------
 * Project Name : Abdal Phpian Render
 * File Name    : ReshaperTest.php
 * Author       : Ebrahim Shafiei (EbraSha)
 * Email        : Prof.Shafiei@Gmail.com
 * Created On   : 2026-01-02 21:35:22
 * Description  : Unit tests for Reshaper class
 * -------------------------------------------------------------------
 *
 * "Coding is an engaging and beloved hobby for me. I passionately and insatiably pursue knowledge in cybersecurity and programming."
 * – Ebrahim Shafiei
 *
 **********************************************************************
 */

namespace Abdal\PhpianRender\Tests;

use Abdal\PhpianRender\Reshaper;
use PHPUnit\Framework\TestCase;

/**
 * Test cases for Reshaper class
 */
class ReshaperTest extends TestCase
{
    private Reshaper $reshaper;

    protected function setUp(): void
    {
        $this->reshaper = new Reshaper();
    }

    public function testReshapeEmptyString(): void
    {
        $this->assertEquals('', $this->reshaper->reshape(''));
    }

    public function testReshapeIsolatedCharacter(): void
    {
        // Test isolated form of ب
        $result = $this->reshaper->reshape('ب');
        $this->assertNotEmpty($result);
    }

    public function testReshapeConnectedCharacters(): void
    {
        // Test connected characters (ب + ت)
        $result = $this->reshaper->reshape('بت');
        $this->assertNotEmpty($result);
        $this->assertNotEquals('بت', $result); // Should be reshaped
    }

    public function testReshapePersianCharacters(): void
    {
        // Test Persian specific characters (پ, چ, گ, ژ)
        $result = $this->reshaper->reshape('پچگژ');
        $this->assertNotEmpty($result);
    }

    public function testReshapeLamAlef(): void
    {
        // Test Lam-Alef combination (لا)
        $result = $this->reshaper->reshape('لا');
        $this->assertNotEmpty($result);
    }

    public function testReshapeWithDiacritics(): void
    {
        // Test reshaping with diacritics
        $text = 'بِت';
        $result = $this->reshaper->reshape($text);
        $this->assertNotEmpty($result);
    }

    public function testReshapeMixedText(): void
    {
        // Test mixed Arabic/Persian text
        $text = 'سلام دنیا';
        $result = $this->reshaper->reshape($text);
        $this->assertNotEmpty($result);
    }

    public function testReshapeNonArabicText(): void
    {
        // Test that non-Arabic text remains unchanged
        $text = 'Hello World';
        $result = $this->reshaper->reshape($text);
        $this->assertEquals($text, $result);
    }

    public function testReshapePersianYeh(): void
    {
        // Test Persian Yeh (ی) reshaping - this was a bug fix
        $text = 'الینا شامی زنجانی';
        $result = $this->reshaper->reshape($text);
        $this->assertNotEmpty($result);
        // Should not contain Lam-Alef combinations incorrectly
        $this->assertStringNotContainsString("\u{FEF5}", $result);
        $this->assertStringNotContainsString("\u{FEF6}", $result);
        $this->assertStringNotContainsString("\u{FEF7}", $result);
        $this->assertStringNotContainsString("\u{FEF8}", $result);
    }
}

