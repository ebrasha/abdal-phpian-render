<?php

/**
 **********************************************************************
 * -------------------------------------------------------------------
 * Project Name : Abdal Phpian Render
 * File Name    : basic-usage.php
 * Author       : Ebrahim Shafiei (EbraSha)
 * Email        : Prof.Shafiei@Gmail.com
 * Created On   : 2026-01-02 21:35:22
 * Description  : Basic usage examples for Abdal Phpian Render package
 * -------------------------------------------------------------------
 *
 * "Coding is an engaging and beloved hobby for me. I passionately and insatiably pursue knowledge in cybersecurity and programming."
 * – Ebrahim Shafiei
 *
 **********************************************************************
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Abdal\PhpianRender\PhpianRender;

// Initialize the renderer
$renderer = new PhpianRender();

// Example 1: Basic text processing
echo "=== Example 1: Basic Processing ===\n";
$text = 'سلام دنیا';
$processed = $renderer->process($text);
echo "Original: $text\n";
echo "Processed: $processed\n\n";

// Example 2: Reshape only
echo "=== Example 2: Reshape Only ===\n";
$text = 'سلام';
$reshaped = $renderer->reshape($text);
echo "Original: $text\n";
echo "Reshaped: $reshaped\n\n";

// Example 3: Number conversion
echo "=== Example 3: Number Conversion ===\n";
$text = 'عدد 123 است';
$converted = $renderer->convertNumbers($text, 'persian');
echo "Original: $text\n";
echo "Converted: $converted\n\n";

// Example 4: BiDi processing
echo "=== Example 4: BiDi Processing ===\n";
$text = 'سلام Hello World';
$bidi = $renderer->processBiDi($text);
echo "Original: $text\n";
echo "BiDi: $bidi\n\n";

// Example 5: Word wrap
echo "=== Example 5: Word Wrap ===\n";
$text = 'این یک متن طولانی است که باید به چند خط تقسیم شود';
$wrapped = $renderer->wordWrap($text, 20);
echo "Original: $text\n";
echo "Wrapped:\n$wrapped\n\n";

// Example 6: RTL detection
echo "=== Example 6: RTL Detection ===\n";
$text1 = 'سلام';
$text2 = 'Hello';
echo "Text: '$text1' - Is RTL: " . ($renderer->isRTL($text1) ? 'Yes' : 'No') . "\n";
echo "Text: '$text2' - Is RTL: " . ($renderer->isRTL($text2) ? 'Yes' : 'No') . "\n\n";

// Example 7: Full processing with options
echo "=== Example 7: Full Processing with Options ===\n";
$text = 'عدد 123 در متن فارسی';
$processed = $renderer->process($text, [
    'reshape' => true,
    'bidi' => true,
    'convertNumbers' => true,
    'numberLocale' => 'persian',
    'preserveDiacritics' => true,
    'clean' => true,
]);
echo "Original: $text\n";
echo "Fully Processed: $processed\n\n";

// Example 8: Static method calls (no need to instantiate)
echo "=== Example 8: Static Method Calls ===\n";
$text = 'سلام دنیا';

// Using static methods directly (simple usage)
$processed = PhpianRender::processStatic($text);
echo "Static Process (Simple): $processed\n";

$reshaped = PhpianRender::reshapeStatic($text);
echo "Static Reshape: $reshaped\n";

$isRTL = PhpianRender::isRTLStatic($text) ? 'Yes' : 'No';
echo "Static IsRTL: $isRTL\n";

$converted = PhpianRender::convertNumbersStatic('عدد 123', 'persian');
echo "Static Convert: $converted\n\n";

// Example 8b: Static method with full options
echo "=== Example 8b: Static Method with Full Options ===\n";
$text = 'عدد 123 در متن فارسی است';

// Full processing with all options using static method
$processed = PhpianRender::processStatic($text, [
    'reshape' => true,              // Enable reshaping
    'bidi' => true,                  // Enable bidirectional
    'convertNumbers' => true,        // Convert numbers
    'numberLocale' => 'persian',     // 'persian' or 'arabic'
    'preserveDiacritics' => true,    // Preserve diacritics
    'clean' => false,                // Clean invisible characters
    'reverse' => true,               // Reverse text for RTL display
]);
echo "Original: $text\n";
echo "Fully Processed (Static): $processed\n\n";

// Example 9: Get version
echo "=== Example 9: Get Version ===\n";
$version = PhpianRender::getVersion();
echo "Package Version: $version\n";

