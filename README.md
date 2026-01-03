# 📦 Abdal Phpian Render

[English](README.md) | [فارسی](README.fa.md)

<div dir="rtl" align="right">

![Abdal Phpian Render](shot.png)

</div>

## 📖 About the Project

**Abdal Phpian Render** is a comprehensive PHP package for fixing and improving the display of Persian texts in graphical environments. This package solves common text display issues in libraries such as GD Library, FPDF, and TCPDF, providing full Right-to-Left (RTL) support.

### 🎯 Why This Software Was Created?

When working with Persian texts in PHP graphical environments, several problems arise:

- **Character Display Issues**: Persian letters are displayed separately without proper connection
- **Display Order Problems**: In mixed texts (Persian + English), word order is not displayed correctly
- **Number Issues**: English numbers appear in Persian text
- **Punctuation Problems**: Parentheses, brackets, and other marks are displayed in the wrong direction
- **Loss of Diacritics**: Persian diacritics are lost during processing

This package solves all these problems and provides a complete and standard solution for rendering RTL texts.

## ✨ Features and Capabilities

### 🔤 Reshaping Algorithm

- ✅ Convert characters to four forms (isolated, final, initial, medial)
- ✅ Full support for Persian-specific characters (پ, چ, گ, ژ)
- ✅ Support for Persian Lam-Alef combinations (لا, لآ)
- ✅ Preserve diacritics during reshaping

### 🔄 BiDi (Bidirectional) Algorithm

- ✅ Automatic detection of display order in mixed texts (Persian + English)
- ✅ Automatic mirroring of punctuation marks (parentheses, brackets, braces, etc.)
- ✅ Smart management of neutral marks based on context

### 🔢 Number Converter

- ✅ Convert English numbers to Persian
- ✅ Automatic conversion of Persian numbers to English (for calculations)

### 🔣 Diacritics Handler

- ✅ Extract and preserve diacritics (fatha, damma, kasra, tanween, etc.)
- ✅ Reapply diacritics after reshaping
- ✅ Prevent loss of diacritics during processing

### 🛠️ Helper Functions

- ✅ `wordWrap()`: Line breaking specifically for RTL texts (prevents incomplete word breaks)
- ✅ `isRTL()`: Automatic detection of input text language
- ✅ `reverse()`: Reverse text
- ✅ `clean()`: Clean invisible and unwanted characters

### 📋 Standards Followed

- ✅ **PSR-12**: Full compliance with PHP coding standards
- ✅ **Type Hinting**: Complete use of type hints for data type safety
- ✅ **PHP 8.1+**: Full compatibility with newer PHP versions
- ✅ **Lightweight**: Light dependencies (only `symfony/polyfill-mbstring`)
- ✅ **Unit Tests**: Complete tests with PHPUnit
- ✅ **Clean Code**: Clean and maintainable code

## 🚀 Installation and Setup

### Install via Composer

```bash
composer require abdal/phpian-render
```

Or add to `composer.json`:

```json
{
    "require": {
        "abdal/phpian-render": "^1.4"
    }
}
```

## 📚 Usage

### Basic Usage

```php
<?php

require_once 'vendor/autoload.php';

use Abdal\PhpianRender\PhpianRender;

// Create an instance of the main class
$renderer = new PhpianRender();

// Simple text processing
$text = 'سلام دنیا';
$processed = $renderer->process($text);
echo $processed;
```

### Full Processing with Options

```php
<?php

use Abdal\PhpianRender\PhpianRender;

$renderer = new PhpianRender();

$text = 'عدد 123 در متن فارسی است';

// Full processing with all features
$processed = $renderer->process($text, [
    'reshape' => true,              // Enable reshaping
    'bidi' => true,                  // Enable bidirectional
    'convertNumbers' => true,        // Convert numbers
    'numberLocale' => 'persian',     // Only 'persian' is supported
    'preserveDiacritics' => true,    // Preserve diacritics
    'clean' => false,                // Clean invisible characters
]);

echo $processed; // Output: عدد ۱۲۳ در متن فارسی است
```

### Using Reshaping Alone

```php
<?php

use Abdal\PhpianRender\PhpianRender;

$renderer = new PhpianRender();

$text = 'سلام';
$reshaped = $renderer->reshape($text);
echo $reshaped; // Characters are displayed connected
```

### Number Conversion

```php
<?php

use Abdal\PhpianRender\PhpianRender;

$renderer = new PhpianRender();

// Convert to Persian
$text = 'عدد 123 است';
$persian = $renderer->convertNumbers($text, 'persian');
echo $persian; // Output: عدد ۱۲۳ است
```

### BiDi Processing for Mixed Texts

```php
<?php

use Abdal\PhpianRender\PhpianRender;

$renderer = new PhpianRender();

$text = 'سلام Hello World';
$bidi = $renderer->processBiDi($text);
echo $bidi; // Display order is correctly fixed
```

### Word Wrap for RTL

```php
<?php

use Abdal\PhpianRender\PhpianRender;

$renderer = new PhpianRender();

$text = 'این یک متن طولانی است که باید به چند خط تقسیم شود';
$wrapped = $renderer->wordWrap($text, 20); // Maximum 20 characters per line

echo $wrapped;
// Output:
// این یک متن طولانی است
// که باید به چند خط
// تقسیم شود
```

### RTL Detection

```php
<?php

use Abdal\PhpianRender\PhpianRender;

$renderer = new PhpianRender();

$text1 = 'سلام';
$text2 = 'Hello';

if ($renderer->isRTL($text1)) {
    echo 'Text is RTL';
}

if (!$renderer->isRTL($text2)) {
    echo 'Text is LTR';
}
```

### Using Static Methods (No Instance Required)

```php
<?php

use Abdal\PhpianRender\PhpianRender;

// Use static methods without creating an instance
$text = 'سلام دنیا';

// Simple processing
$processed = PhpianRender::processStatic($text);
echo $processed;

// Full processing with all options
$text = 'عدد 123 در متن فارسی است';
$processed = PhpianRender::processStatic($text, [
    'reshape' => true,              // Enable reshaping
    'bidi' => true,                  // Enable bidirectional
    'convertNumbers' => true,        // Convert numbers
    'numberLocale' => 'persian',     // Only 'persian' is supported
    'preserveDiacritics' => true,    // Preserve diacritics
    'clean' => false,                // Clean invisible characters
    'reverse' => true,               // Reverse text for RTL display
]);
echo $processed; // Output: عدد ۱۲۳ در متن فارسی است

// Reshape
$reshaped = PhpianRender::reshapeStatic($text);

// Convert numbers
$converted = PhpianRender::convertNumbersStatic('عدد 123', 'persian');

// RTL detection
$isRTL = PhpianRender::isRTLStatic($text);

// Word wrap
$wrapped = PhpianRender::wordWrapStatic('متن طولانی', 20);

// Get package version
$version = PhpianRender::getVersion();
echo "Version: $version";
```

### Using Standalone Classes

```php
<?php

use Abdal\PhpianRender\Reshaper;
use Abdal\PhpianRender\BiDi;
use Abdal\PhpianRender\NumberConverter;
use Abdal\PhpianRender\Helper;

// Direct use of Reshaper
$reshaper = new Reshaper();
$reshaped = $reshaper->reshape('سلام');

// Direct use of BiDi
$bidi = new BiDi();
$processed = $bidi->process('سلام Hello');

// Direct use of NumberConverter
$converter = new NumberConverter();
$persian = $converter->toPersian('123');

// Direct use of Helper
$helper = new Helper();
$isRTL = $helper->isRTL('سلام');
$wrapped = $helper->wordWrap('متن طولانی', 10);
```

### Usage in GD Library

```php
<?php

use Abdal\PhpianRender\PhpianRender;

$renderer = new PhpianRender();

// Process text before displaying in image
$text = 'سلام دنیا';
$processed = $renderer->process($text, [
    'reshape' => true,
    'bidi' => true,
    'convertNumbers' => true,
]);

// Use in GD
$image = imagecreate(400, 200);
$bg = imagecolorallocate($image, 255, 255, 255);
$textColor = imagecolorallocate($image, 0, 0, 0);

// Use Persian font
imagettftext($image, 20, 0, 10, 50, $textColor, 'font.ttf', $processed);

header('Content-Type: image/png');
imagepng($image);
imagedestroy($image);
```

### Usage in FPDF/TCPDF

```php
<?php

use Abdal\PhpianRender\PhpianRender;
require_once('fpdf.php'); // or tcpdf.php

$renderer = new PhpianRender();
$pdf = new FPDF();

$pdf->AddPage();
$pdf->AddFont('DejaVu', '', 'DejaVuSans.ttf', true);

$text = 'سلام دنیا - عدد 123';
$processed = $renderer->process($text, [
    'reshape' => true,
    'bidi' => true,
    'convertNumbers' => true,
]);

$pdf->SetFont('DejaVu', '', 14);
$pdf->Cell(0, 10, $processed, 0, 1);

$pdf->Output();
```

## 🧪 Tests

To run unit tests:

```bash
composer install
vendor/bin/phpunit
```

## 📦 Dependencies

- PHP >= 8.1
- symfony/polyfill-mbstring ^1.28

## 📄 License

This project is licensed under the GPL-2.0-or-later License.

## 🐛 Reporting Issues

If you encounter any issues or have configuration problems, please reach out via email at Prof.Shafiei@Gmail.com. You can also report issues on GitLab or GitHub.

## ❤️ Donation

If you find this project helpful and would like to support further development, please consider making a donation:
- [Donate Here](https://alphajet.ir/abdal-donation)

## 🤵 Programmer

Handcrafted with Passion by **Ebrahim Shafiei (EbraSha)**
- **E-Mail**: Prof.Shafiei@Gmail.com
- **Telegram**: [@ProfShafiei](https://t.me/ProfShafiei)
- **GitHub**: [@ebrasha](https://github.com/ebrasha)
- **Twitter/X**: [@ProfShafiei](https://x.com/ProfShafiei)
- **LinkedIn**: [ProfShafiei](https://www.linkedin.com/in/profshafiei/)

## 📜 License

This project is licensed under the GPLv2 or later License.

