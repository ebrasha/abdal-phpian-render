<?php

/**
 **********************************************************************
 * -------------------------------------------------------------------
 * Project Name : Abdal Phpian Render
 * File Name    : Helper.php
 * Author       : Ebrahim Shafiei (EbraSha)
 * Email        : Prof.Shafiei@Gmail.com
 * Created On   : 2026-01-02 21:35:22
 * Description  : Helper functions for RTL text processing (wordWrap, isRTL, etc.)
 * -------------------------------------------------------------------
 *
 * "Coding is an engaging and beloved hobby for me. I passionately and insatiably pursue knowledge in cybersecurity and programming."
 * – Ebrahim Shafiei
 *
 **********************************************************************
 */

namespace Abdal\PhpianRender;

/**
 * Helper class provides utility functions for RTL text processing
 */
class Helper
{
    /**
     * RTL character ranges
     */
    private const RTL_RANGES = [
        [0x0590, 0x05FF], // Hebrew
        [0x0600, 0x06FF], // Arabic
        [0x0700, 0x074F], // Syriac
        [0x0750, 0x077F], // Arabic Supplement
        [0x08A0, 0x08FF], // Arabic Extended-A
        [0xFB50, 0xFDFF], // Arabic Presentation Forms-A
        [0xFE70, 0xFEFF], // Arabic Presentation Forms-B
    ];

    /**
     * Persian/Arabic specific characters
     */
    private const PERSIAN_ARABIC_CHARS = [
        0x067E, 0x0686, 0x06AF, 0x0698, // Persian: پ, چ, گ, ژ
    ];

    /**
     * Check if text is RTL (Right-to-Left)
     *
     * @param string $text Text to check
     * @return bool True if text is RTL
     */
    public function isRTL(string $text): bool
    {
        if (empty($text)) {
            return false;
        }

        $rtlCount = 0;
        $ltrCount = 0;
        $chars = mb_str_split($text, 1, 'UTF-8');

        foreach ($chars as $char) {
            if (ctype_space($char) || ctype_punct($char)) {
                continue;
            }

            $codePoint = $this->getCodePoint($char);

            if ($this->isRTLCodePoint($codePoint)) {
                $rtlCount++;
            } elseif ($this->isLTRCodePoint($codePoint)) {
                $ltrCount++;
            }
        }

        return $rtlCount > $ltrCount;
    }

    /**
     * Word wrap for RTL text
     * Prevents breaking words incorrectly at line endings
     *
     * @param string $text Text to wrap
     * @param int $width Maximum line width (in characters)
     * @param string $break Line break character
     * @param bool $cut Whether to cut words if they exceed width
     * @return string Wrapped text
     */
    public function wordWrap(string $text, int $width = 75, string $break = "\n", bool $cut = false): string
    {
        if (empty($text) || $width <= 0) {
            return $text;
        }

        $isRTL = $this->isRTL($text);
        $lines = explode("\n", $text);
        $wrapped = [];

        foreach ($lines as $line) {
            if (mb_strlen($line, 'UTF-8') <= $width) {
                $wrapped[] = $line;
                continue;
            }

            $wrappedLines = $this->wrapLine($line, $width, $break, $cut, $isRTL);
            $wrapped = array_merge($wrapped, $wrappedLines);
        }

        return implode($break, $wrapped);
    }

    /**
     * Wrap a single line
     *
     * @param string $line Line to wrap
     * @param int $width Maximum width
     * @param string $break Line break character
     * @param bool $cut Whether to cut words
     * @param bool $isRTL Whether text is RTL
     * @return array Array of wrapped lines
     */
    private function wrapLine(string $line, int $width, string $break, bool $cut, bool $isRTL): array
    {
        $wrapped = [];
        $currentLine = '';
        $words = $this->splitIntoWords($line, $isRTL);

        foreach ($words as $word) {
            $wordLength = mb_strlen($word, 'UTF-8');

            if (empty($currentLine)) {
                $currentLine = $word;
            } elseif (mb_strlen($currentLine, 'UTF-8') + 1 + $wordLength <= $width) {
                $currentLine .= ' ' . $word;
            } else {
                // Current line is full
                if ($wordLength > $width && $cut) {
                    // Word is too long, need to cut it
                    $remaining = $word;
                    while (mb_strlen($remaining, 'UTF-8') > $width) {
                        $wrapped[] = mb_substr($remaining, 0, $width, 'UTF-8');
                        $remaining = mb_substr($remaining, $width, null, 'UTF-8');
                    }
                    $currentLine = $remaining;
                } else {
                    // Move word to next line
                    $wrapped[] = $currentLine;
                    $currentLine = $word;
                }
            }
        }

        if (!empty($currentLine)) {
            $wrapped[] = $currentLine;
        }

        return $wrapped;
    }

    /**
     * Split text into words (handling RTL properly)
     *
     * @param string $text Text to split
     * @param bool $isRTL Whether text is RTL
     * @return array Array of words
     */
    private function splitIntoWords(string $text, bool $isRTL): array
    {
        if ($isRTL) {
            // For RTL, split on spaces and punctuation, but keep words together
            $pattern = '/(\S+)/u';
            preg_match_all($pattern, $text, $matches);
            return $matches[1] ?? [];
        } else {
            // For LTR, standard word splitting
            return preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
        }
    }

    /**
     * Get Unicode code point of a character
     *
     * @param string $char Character
     * @return int Code point
     */
    private function getCodePoint(string $char): int
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
     * Check if code point is RTL
     *
     * @param int $codePoint Unicode code point
     * @return bool True if RTL
     */
    private function isRTLCodePoint(int $codePoint): bool
    {
        foreach (self::RTL_RANGES as $range) {
            if ($codePoint >= $range[0] && $codePoint <= $range[1]) {
                return true;
            }
        }

        return in_array($codePoint, self::PERSIAN_ARABIC_CHARS, true);
    }

    /**
     * Check if code point is LTR
     *
     * @param int $codePoint Unicode code point
     * @return bool True if LTR
     */
    private function isLTRCodePoint(int $codePoint): bool
    {
        // Basic Latin, Latin Extended, etc.
        return ($codePoint >= 0x0041 && $codePoint <= 0x007A) || // Basic Latin
               ($codePoint >= 0x00C0 && $codePoint <= 0x024F) || // Latin Extended
               ($codePoint >= 0x1E00 && $codePoint <= 0x1EFF);   // Latin Extended Additional
    }

    /**
     * Reverse text (useful for RTL display)
     *
     * @param string $text Text to reverse
     * @return string Reversed text
     */
    public function reverse(string $text): string
    {
        $chars = mb_str_split($text, 1, 'UTF-8');
        return implode('', array_reverse($chars));
    }

    /**
     * Clean text from zero-width characters and unwanted marks
     *
     * @param string $text Text to clean
     * @return string Cleaned text
     */
    public function clean(string $text): string
    {
        // Remove zero-width characters
        $text = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $text);
        
        // Remove other unwanted marks
        $text = preg_replace('/[\x{200E}\x{200F}]/u', '', $text); // LRM, RLM
        
        return $text;
    }
}

