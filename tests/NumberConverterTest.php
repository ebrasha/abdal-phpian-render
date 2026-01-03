<?php

/**
 **********************************************************************
 * -------------------------------------------------------------------
 * Project Name : Abdal Phpian Render
 * File Name    : NumberConverterTest.php
 * Author       : Ebrahim Shafiei (EbraSha)
 * Email        : Prof.Shafiei@Gmail.com
 * Created On   : 2026-01-02 21:35:22
 * Description  : Unit tests for NumberConverter class
 * -------------------------------------------------------------------
 *
 * "Coding is an engaging and beloved hobby for me. I passionately and insatiably pursue knowledge in cybersecurity and programming."
 * – Ebrahim Shafiei
 *
 **********************************************************************
 */

namespace Abdal\PhpianRender\Tests;

use Abdal\PhpianRender\NumberConverter;
use PHPUnit\Framework\TestCase;

/**
 * Test cases for NumberConverter class
 */
class NumberConverterTest extends TestCase
{
    private NumberConverter $converter;

    protected function setUp(): void
    {
        $this->converter = new NumberConverter();
    }

    public function testToPersian(): void
    {
        $this->assertEquals('۱۲۳', $this->converter->toPersian('123'));
        $this->assertEquals('۰', $this->converter->toPersian('0'));
        $this->assertEquals('۹', $this->converter->toPersian('9'));
    }

    public function testPersianToEnglish(): void
    {
        $this->assertEquals('123', $this->converter->persianToEnglish('۱۲۳'));
        $this->assertEquals('0', $this->converter->persianToEnglish('۰'));
        $this->assertEquals('9', $this->converter->persianToEnglish('۹'));
    }

    public function testToArabic(): void
    {
        $this->assertEquals('١٢٣', $this->converter->toArabic('123'));
        $this->assertEquals('٠', $this->converter->toArabic('0'));
        $this->assertEquals('٩', $this->converter->toArabic('9'));
    }

    public function testArabicToEnglish(): void
    {
        $this->assertEquals('123', $this->converter->arabicToEnglish('١٢٣'));
        $this->assertEquals('0', $this->converter->arabicToEnglish('٠'));
        $this->assertEquals('9', $this->converter->arabicToEnglish('٩'));
    }

    public function testToEnglish(): void
    {
        $this->assertEquals('123', $this->converter->toEnglish('۱۲۳'));
        $this->assertEquals('123', $this->converter->toEnglish('١٢٣'));
    }

    public function testConvertByLocalePersian(): void
    {
        $result = $this->converter->convertByLocale('123', 'persian');
        $this->assertEquals('۱۲۳', $result);
    }

    public function testConvertByLocaleArabic(): void
    {
        $result = $this->converter->convertByLocale('123', 'arabic');
        $this->assertEquals('١٢٣', $result);
    }

    public function testMixedTextConversion(): void
    {
        $text = 'عدد 123 است';
        $result = $this->converter->toPersian($text);
        $this->assertStringContainsString('۱۲۳', $result);
    }
}

