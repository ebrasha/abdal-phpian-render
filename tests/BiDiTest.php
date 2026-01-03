<?php

/**
 **********************************************************************
 * -------------------------------------------------------------------
 * Project Name : Abdal Phpian Render
 * File Name    : BiDiTest.php
 * Author       : Ebrahim Shafiei (EbraSha)
 * Email        : Prof.Shafiei@Gmail.com
 * Created On   : 2026-01-02 21:35:22
 * Description  : Unit tests for BiDi class
 * -------------------------------------------------------------------
 *
 * "Coding is an engaging and beloved hobby for me. I passionately and insatiably pursue knowledge in cybersecurity and programming."
 * – Ebrahim Shafiei
 *
 **********************************************************************
 */

namespace Abdal\PhpianRender\Tests;

use Abdal\PhpianRender\BiDi;
use PHPUnit\Framework\TestCase;

/**
 * Test cases for BiDi class
 */
class BiDiTest extends TestCase
{
    private BiDi $bidi;

    protected function setUp(): void
    {
        $this->bidi = new BiDi();
    }

    public function testProcessEmptyString(): void
    {
        $this->assertEquals('', $this->bidi->process(''));
    }

    public function testProcessRTLText(): void
    {
        $text = 'سلام';
        $result = $this->bidi->process($text);
        $this->assertNotEmpty($result);
    }

    public function testProcessLTRText(): void
    {
        $text = 'Hello World';
        $result = $this->bidi->process($text);
        $this->assertNotEmpty($result);
    }

    public function testProcessMixedText(): void
    {
        $text = 'سلام Hello';
        $result = $this->bidi->process($text);
        $this->assertNotEmpty($result);
    }

    public function testProcessWithPunctuation(): void
    {
        $text = 'سلام!';
        $result = $this->bidi->process($text);
        $this->assertNotEmpty($result);
    }

    public function testProcessWithNumbers(): void
    {
        $text = 'عدد 123';
        $result = $this->bidi->process($text);
        $this->assertNotEmpty($result);
    }
}

