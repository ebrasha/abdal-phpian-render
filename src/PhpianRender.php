<?php

/**
 **********************************************************************
 * -------------------------------------------------------------------
 * Project Name : Abdal Phpian Render
 * File Name    : PhpianRender.php
 * Author       : Ebrahim Shafiei (EbraSha)
 * Email        : Prof.Shafiei@Gmail.com
 * Created On   : 2026-01-02 21:35:22
 * Description  : Main class that orchestrates all RTL text processing features
 * -------------------------------------------------------------------
 *
 * "Coding is an engaging and beloved hobby for me. I passionately and insatiably pursue knowledge in cybersecurity and programming."
 * – Ebrahim Shafiei
 *
 **********************************************************************
 */

namespace Abdal\PhpianRender;

/**
 * PhpianRender main class
 * Provides a unified interface for all RTL text processing features
 * Supports both instance and static method calls
 */
class PhpianRender
{
    private Reshaper $reshaper;
    private BiDi $bidi;
    private NumberConverter $numberConverter;
    private DiacriticsHandler $diacriticsHandler;
    private Helper $helper;

    /**
     * Static instance for static method calls
     */
    private static ?PhpianRender $staticInstance = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->reshaper = new Reshaper();
        $this->bidi = new BiDi();
        $this->numberConverter = new NumberConverter();
        $this->diacriticsHandler = new DiacriticsHandler();
        $this->helper = new Helper();
    }

    /**
     * Get or create static instance
     *
     * @return PhpianRender
     */
    private static function getStaticInstance(): PhpianRender
    {
        if (self::$staticInstance === null) {
            self::$staticInstance = new self();
        }
        return self::$staticInstance;
    }

    /**
     * Process text with all RTL features
     *
     * @param string $text Input text
     * @param array $options Processing options
     * @return string Processed text
     */
    public function process(string $text, array $options = []): string
    {
        $defaultOptions = [
            'reshape' => true,
            'bidi' => true,
            'convertNumbers' => false,
            'numberLocale' => 'persian', // Only Persian is supported
            'preserveDiacritics' => true,
            'clean' => false,
            'reverse' => true, // Reverse text for RTL display (like PersianRender)
        ];

        $options = array_merge($defaultOptions, $options);

        // Clean text if requested
        if ($options['clean']) {
            $text = $this->helper->clean($text);
        }

        // Extract diacritics if we need to preserve them
        $originalText = $text;
        $diacritics = null;
        if ($options['preserveDiacritics']) {
            $extracted = $this->diacriticsHandler->extract($text);
            $text = $extracted['base'];
            $diacritics = $extracted['diacritics'];
        }

        // Check if text is mixed (RTL + LTR) or pure RTL
        $isMixed = $this->isMixedText($text);

        // Reshape characters
        $reshapedText = $text;
        if ($options['reshape']) {
            $reshapedText = $this->reshaper->reshape($text);
        }

        // Apply diacritics back if preserved
        if ($options['preserveDiacritics'] && $diacritics !== null) {
            $text = $this->diacriticsHandler->preserveDuringReshape(
                $originalText,
                $reshapedText
            );
        } else {
            $text = $reshapedText;
        }

        // Process bidirectional text
        // For mixed texts (RTL + LTR), use BiDi algorithm
        // For pure RTL texts, just reverse the entire text (like PersianRender)
        if ($options['bidi']) {
            if ($isMixed) {
                // Mixed text: use BiDi algorithm
                $text = $this->bidi->process($text);
            } elseif ($options['reverse'] && $this->helper->isRTL($text)) {
                // Pure RTL text: reverse entire text (like PersianRender.php)
                $chars = mb_str_split($text, 1, 'UTF-8');
                $text = implode('', array_reverse($chars));
            }
        }

        // Convert numbers
        if ($options['convertNumbers']) {
            $text = $this->numberConverter->convertByLocale(
                $text,
                $options['numberLocale']
            );
        }

        return $text;
    }

    /**
     * Check if text contains both RTL and LTR characters
     *
     * @param string $text Input text
     * @return bool True if text is mixed
     */
    private function isMixedText(string $text): bool
    {
        $hasRTL = false;
        $hasLTR = false;
        $chars = mb_str_split($text, 1, 'UTF-8');

        foreach ($chars as $char) {
            if (ctype_space($char) || ctype_punct($char)) {
                continue;
            }

            // Check if it's RTL
            $codePoint = $this->getCharCodePoint($char);
            if (($codePoint >= 0x0590 && $codePoint <= 0x08FF) ||
                ($codePoint >= 0x0600 && $codePoint <= 0x06FF) ||
                in_array($codePoint, [0x067E, 0x0686, 0x06AF, 0x0698])) {
                $hasRTL = true;
            } else {
                // Check if it's LTR (Latin characters or numbers)
                if (($codePoint >= 0x0041 && $codePoint <= 0x007A) ||
                    ($codePoint >= 0x0030 && $codePoint <= 0x0039)) {
                    $hasLTR = true;
                }
            }

            if ($hasRTL && $hasLTR) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get Unicode code point of a character
     *
     * @param string $char Character
     * @return int Code point
     */
    private function getCharCodePoint(string $char): int
    {
        $bytes = unpack('C*', mb_convert_encoding($char, 'UTF-32BE', 'UTF-8'));
        if (empty($bytes)) {
            return 0;
        }
        $codePoint = 0;
        foreach ($bytes as $byte) {
            $codePoint = ($codePoint << 8) | $byte;
        }
        return $codePoint;
    }

    /**
     * Reshape text only
     *
     * @param string $text Input text
     * @return string Reshaped text
     */
    public function reshape(string $text): string
    {
        return $this->reshaper->reshape($text);
    }

    /**
     * Process bidirectional text only
     *
     * @param string $text Input text
     * @return string Processed text
     */
    public function processBiDi(string $text): string
    {
        return $this->bidi->process($text);
    }

    /**
     * Convert numbers
     *
     * @param string $text Input text
     * @param string $locale Target locale (only 'persian' is supported)
     * @return string Text with converted numbers
     */
    public function convertNumbers(string $text, string $locale = 'persian'): string
    {
        return $this->numberConverter->convertByLocale($text, $locale);
    }

    /**
     * Word wrap for RTL text
     *
     * @param string $text Text to wrap
     * @param int $width Maximum line width
     * @param string $break Line break character
     * @param bool $cut Whether to cut words
     * @return string Wrapped text
     */
    public function wordWrap(string $text, int $width = 75, string $break = "\n", bool $cut = false): string
    {
        return $this->helper->wordWrap($text, $width, $break, $cut);
    }

    /**
     * Check if text is RTL
     *
     * @param string $text Text to check
     * @return bool True if RTL
     */
    public function isRTL(string $text): bool
    {
        return $this->helper->isRTL($text);
    }

    /**
     * Get Reshaper instance
     *
     * @return Reshaper
     */
    public function getReshaper(): Reshaper
    {
        return $this->reshaper;
    }

    /**
     * Get BiDi instance
     *
     * @return BiDi
     */
    public function getBiDi(): BiDi
    {
        return $this->bidi;
    }

    /**
     * Get NumberConverter instance
     *
     * @return NumberConverter
     */
    public function getNumberConverter(): NumberConverter
    {
        return $this->numberConverter;
    }

    /**
     * Get DiacriticsHandler instance
     *
     * @return DiacriticsHandler
     */
    public function getDiacriticsHandler(): DiacriticsHandler
    {
        return $this->diacriticsHandler;
    }

    /**
     * Get Helper instance
     *
     * @return Helper
     */
    public function getHelper(): Helper
    {
        return $this->helper;
    }

    /**
     * Get package version
     *
     * @return string Package version
     */
    public static function getVersion(): string
    {
        return Version::getVersion();
    }

    // ========== Static Methods ==========

    /**
     * Process text with all RTL features (static)
     *
     * @param string $text Input text
     * @param array $options Processing options
     * @return string Processed text
     */
    public static function processStatic(string $text, array $options = []): string
    {
        return self::getStaticInstance()->process($text, $options);
    }

    /**
     * Reshape text only (static)
     *
     * @param string $text Input text
     * @return string Reshaped text
     */
    public static function reshapeStatic(string $text): string
    {
        return self::getStaticInstance()->reshape($text);
    }

    /**
     * Process bidirectional text only (static)
     *
     * @param string $text Input text
     * @return string Processed text
     */
    public static function processBiDiStatic(string $text): string
    {
        return self::getStaticInstance()->processBiDi($text);
    }

    /**
     * Convert numbers (static)
     *
     * @param string $text Input text
     * @param string $locale Target locale (only 'persian' is supported)
     * @return string Text with converted numbers
     */
    public static function convertNumbersStatic(string $text, string $locale = 'persian'): string
    {
        return self::getStaticInstance()->convertNumbers($text, $locale);
    }

    /**
     * Word wrap for RTL text (static)
     *
     * @param string $text Text to wrap
     * @param int $width Maximum line width
     * @param string $break Line break character
     * @param bool $cut Whether to cut words
     * @return string Wrapped text
     */
    public static function wordWrapStatic(string $text, int $width = 75, string $break = "\n", bool $cut = false): string
    {
        return self::getStaticInstance()->wordWrap($text, $width, $break, $cut);
    }

    /**
     * Check if text is RTL (static)
     *
     * @param string $text Text to check
     * @return bool True if RTL
     */
    public static function isRTLStatic(string $text): bool
    {
        return self::getStaticInstance()->isRTL($text);
    }
}

