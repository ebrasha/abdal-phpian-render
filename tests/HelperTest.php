<?php

/**
 **********************************************************************
 * -------------------------------------------------------------------
 * Project Name : Abdal Phpian Render
 * File Name    : HelperTest.php
 * Author       : Ebrahim Shafiei (EbraSha)
 * Email        : Prof.Shafiei@Gmail.com
 * Created On   : 2026-01-02 21:35:22
 * Description  : Unit tests for Helper class
 * -------------------------------------------------------------------
 *
 * "Coding is an engaging and beloved hobby for me. I passionately and insatiably pursue knowledge in cybersecurity and programming."
 * – Ebrahim Shafiei
 *
 **********************************************************************
 */

namespace Abdal\PhpianRender\Tests;

use Abdal\PhpianRender\Helper;
use PHPUnit\Framework\TestCase;

/**
 * Test cases for Helper class
 */
class HelperTest extends TestCase
{
    private Helper $helper;

    protected function setUp(): void
    {
        $this->helper = new Helper();
    }

    public function testIsRTLWithPersianText(): void
    {
        $this->assertTrue($this->helper->isRTL('سلام'));
    }

    public function testIsRTLWithArabicText(): void
    {
        $this->assertTrue($this->helper->isRTL('مرحبا'));
    }

    public function testIsRTLWithEnglishText(): void
    {
        $this->assertFalse($this->helper->isRTL('Hello'));
    }

    public function testIsRTLWithMixedText(): void
    {
        // Should return true if RTL characters are dominant
        $this->assertTrue($this->helper->isRTL('سلام Hello'));
    }

    public function testIsRTLEmptyString(): void
    {
        $this->assertFalse($this->helper->isRTL(''));
    }

    public function testWordWrap(): void
    {
        $text = 'این یک متن طولانی است که باید به چند خط تقسیم شود';
        $result = $this->helper->wordWrap($text, 20);
        $this->assertNotEmpty($result);
        $lines = explode("\n", $result);
        $this->assertGreaterThan(1, count($lines));
    }

    public function testWordWrapShortText(): void
    {
        $text = 'سلام';
        $result = $this->helper->wordWrap($text, 20);
        $this->assertEquals($text, $result);
    }

    public function testReverse(): void
    {
        $text = 'سلام';
        $result = $this->helper->reverse($text);
        $this->assertNotEmpty($result);
    }

    public function testClean(): void
    {
        $text = "سلام\u{200B}دنیا";
        $result = $this->helper->clean($text);
        $this->assertStringNotContainsString("\u{200B}", $result);
    }
}

