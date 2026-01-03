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

    public function testGetVersion(): void
    {
        $version = PhpianRender::getVersion();
        $this->assertNotEmpty($version);
        $this->assertIsString($version);
        $this->assertMatchesRegularExpression('/^\d+\.\d+\.\d+$/', $version);
    }

    // Static method tests
    public function testProcessStatic(): void
    {
        $text = 'سلام';
        $result = PhpianRender::processStatic($text);
        $this->assertNotEmpty($result);
    }

    public function testReshapeStatic(): void
    {
        $text = 'سلام';
        $result = PhpianRender::reshapeStatic($text);
        $this->assertNotEmpty($result);
    }

    public function testProcessBiDiStatic(): void
    {
        $text = 'سلام Hello';
        $result = PhpianRender::processBiDiStatic($text);
        $this->assertNotEmpty($result);
    }

    public function testConvertNumbersStatic(): void
    {
        $text = 'عدد 123';
        $result = PhpianRender::convertNumbersStatic($text, 'persian');
        $this->assertStringContainsString('۱۲۳', $result);
    }

    public function testWordWrapStatic(): void
    {
        $text = 'این یک متن طولانی است';
        $result = PhpianRender::wordWrapStatic($text, 10);
        $this->assertNotEmpty($result);
    }

    public function testIsRTLStatic(): void
    {
        $this->assertTrue(PhpianRender::isRTLStatic('سلام'));
        $this->assertFalse(PhpianRender::isRTLStatic('Hello'));
    }

    public function testStaticAndInstanceMethodsProduceSameResults(): void
    {
        $text = 'سلام دنیا';
        
        // Test process
        $instanceResult = $this->render->process($text);
        $staticResult = PhpianRender::processStatic($text);
        $this->assertEquals($instanceResult, $staticResult);
        
        // Test reshape
        $instanceResult = $this->render->reshape($text);
        $staticResult = PhpianRender::reshapeStatic($text);
        $this->assertEquals($instanceResult, $staticResult);
        
        // Test isRTL
        $instanceResult = $this->render->isRTL($text);
        $staticResult = PhpianRender::isRTLStatic($text);
        $this->assertEquals($instanceResult, $staticResult);
    }

    public function testProcessStaticWithFullOptions(): void
    {
        $text = 'عدد 123 در متن فارسی';
        
        $options = [
            'reshape' => true,
            'bidi' => true,
            'convertNumbers' => true,
            'numberLocale' => 'persian',
            'preserveDiacritics' => true,
            'clean' => false,
            'reverse' => true,
        ];
        
        $result = PhpianRender::processStatic($text, $options);
        $this->assertNotEmpty($result);
        
        // Should contain Persian numbers
        $this->assertStringContainsString('۱۲۳', $result);
    }

    public function testProcessStaticWithCustomOptions(): void
    {
        $text = 'سلام Hello';
        
        // Test with only reshape
        $result1 = PhpianRender::processStatic($text, ['reshape' => true, 'bidi' => false]);
        $this->assertNotEmpty($result1);
        
        // Test with only number conversion
        $result2 = PhpianRender::processStatic('عدد 123', ['reshape' => false, 'convertNumbers' => true]);
        $this->assertStringContainsString('۱۲۳', $result2);
    }
}

