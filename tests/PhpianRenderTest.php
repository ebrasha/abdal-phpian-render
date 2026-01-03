<?php

/**
 **********************************************************************
 * -------------------------------------------------------------------
 * Project Name : Abdal Phpian Render
 * File Name    : PhpianRenderTest.php
 * Author       : Ebrahim Shafiei (EbraSha)
 * Email        : Prof.Shafiei@Gmail.com
 * Created On   : 2026-01-02 21:35:22
 * Description  : Unit tests for main PhpianRender class
 * -------------------------------------------------------------------
 *
 * "Coding is an engaging and beloved hobby for me. I passionately and insatiably pursue knowledge in cybersecurity and programming."
 * – Ebrahim Shafiei
 *
 **********************************************************************
 */

namespace Abdal\PhpianRender\Tests;

use Abdal\PhpianRender\PhpianRender;
use PHPUnit\Framework\TestCase;

/**
 * Test cases for PhpianRender class
 */
class PhpianRenderTest extends TestCase
{
    private PhpianRender $render;

    protected function setUp(): void
    {
        $this->render = new PhpianRender();
    }

    public function testProcessWithDefaultOptions(): void
    {
        $text = 'سلام';
        $result = $this->render->process($text);
        $this->assertNotEmpty($result);
    }

    public function testProcessWithCustomOptions(): void
    {
        $text = 'عدد 123';
        $result = $this->render->process($text, [
            'convertNumbers' => true,
            'numberLocale' => 'persian'
        ]);
        $this->assertNotEmpty($result);
    }

    public function testReshape(): void
    {
        $text = 'سلام';
        $result = $this->render->reshape($text);
        $this->assertNotEmpty($result);
    }

    public function testProcessBiDi(): void
    {
        $text = 'سلام Hello';
        $result = $this->render->processBiDi($text);
        $this->assertNotEmpty($result);
    }

    public function testConvertNumbers(): void
    {
        $text = 'عدد 123';
        $result = $this->render->convertNumbers($text, 'persian');
        $this->assertStringContainsString('۱۲۳', $result);
    }

    public function testWordWrap(): void
    {
        $text = 'این یک متن طولانی است';
        $result = $this->render->wordWrap($text, 10);
        $this->assertNotEmpty($result);
    }

    public function testIsRTL(): void
    {
        $this->assertTrue($this->render->isRTL('سلام'));
        $this->assertFalse($this->render->isRTL('Hello'));
    }

    public function testGetInstances(): void
    {
        $this->assertNotNull($this->render->getReshaper());
        $this->assertNotNull($this->render->getBiDi());
        $this->assertNotNull($this->render->getNumberConverter());
        $this->assertNotNull($this->render->getDiacriticsHandler());
        $this->assertNotNull($this->render->getHelper());
    }
}

