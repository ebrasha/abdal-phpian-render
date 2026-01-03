<?php

/**
 **********************************************************************
 * -------------------------------------------------------------------
 * Project Name : Abdal Phpian Render
 * File Name    : Reshaper.php
 * Author       : Ebrahim Shafiei (EbraSha)
 * Email        : Prof.Shafiei@Gmail.com
 * Created On   : 2026-01-02 21:35:22
 * Description  : Comprehensive character reshaping algorithm for Persian text
 * -------------------------------------------------------------------
 *
 * "Coding is an engaging and beloved hobby for me. I passionately and insatiably pursue knowledge in cybersecurity and programming."
 * – Ebrahim Shafiei
 *
 **********************************************************************
 */

namespace Abdal\PhpianRender;

/**
 * Reshaper class handles the conversion of Persian characters
 * to their contextual forms (initial, medial, final, isolated)
 */
class Reshaper
{
    /**
     * Arabic/Persian character forms mapping
     * Format: [character] => [isolated, final, initial, medial]
     */
    /**
     * Persian character forms mapping
     * Format: [character] => [isolated, final, initial, medial]
     * Only Persian characters are supported (no Arabic-specific characters)
     */
    private const CHARACTER_FORMS = [
        // Alef variations (used in Persian)
        "\u{0622}" => ["\u{FE81}", "\u{FE82}", "", ""], // آ - final only, no initial/medial
        "\u{0627}" => ["\u{FE8D}", "\u{FE8E}", "", ""], // ا - final only, no initial/medial (doesn't connect forward)
        
        // Common Persian characters
        "\u{0628}" => ["\u{FE8F}", "\u{FE90}", "\u{FE91}", "\u{FE92}"], // ب
        "\u{062A}" => ["\u{FE95}", "\u{FE96}", "\u{FE97}", "\u{FE98}"], // ت
        "\u{062C}" => ["\u{FE9D}", "\u{FE9E}", "\u{FE9F}", "\u{FEA0}"], // ج
        "\u{062D}" => ["\u{FEA1}", "\u{FEA2}", "\u{FEA3}", "\u{FEA4}"], // ح
        "\u{062E}" => ["\u{FEA5}", "\u{FEA6}", "\u{FEA7}", "\u{FEA8}"], // خ
        "\u{062F}" => ["\u{FEA9}", "\u{FEAA}", "", ""], // د - final only, no initial/medial
        "\u{0631}" => ["\u{FEAD}", "\u{FEAE}", "", ""], // ر - final only, no initial/medial
        "\u{0632}" => ["\u{FEAF}", "\u{FEB0}", "", ""], // ز - final only, no initial/medial
        "\u{0633}" => ["\u{FEB1}", "\u{FEB2}", "\u{FEB3}", "\u{FEB4}"], // س
        "\u{0634}" => ["\u{FEB5}", "\u{FEB6}", "\u{FEB7}", "\u{FEB8}"], // ش
        "\u{0635}" => ["\u{FEB9}", "\u{FEBA}", "\u{FEBB}", "\u{FEBC}"], // ص
        "\u{0636}" => ["\u{FEBD}", "\u{FEBE}", "\u{FEBF}", "\u{FEC0}"], // ض
        "\u{0637}" => ["\u{FEC1}", "\u{FEC2}", "\u{FEC3}", "\u{FEC4}"], // ط
        "\u{0638}" => ["\u{FEC5}", "\u{FEC6}", "\u{FEC7}", "\u{FEC8}"], // ظ
        "\u{0639}" => ["\u{FEC9}", "\u{FECA}", "\u{FECB}", "\u{FECC}"], // ع
        "\u{063A}" => ["\u{FECD}", "\u{FECE}", "\u{FECF}", "\u{FED0}"], // غ
        "\u{0641}" => ["\u{FED1}", "\u{FED2}", "\u{FED3}", "\u{FED4}"], // ف
        "\u{0642}" => ["\u{FED5}", "\u{FED6}", "\u{FED7}", "\u{FED8}"], // ق
        "\u{06A9}" => ["\u{FED9}", "\u{FEDA}", "\u{FEDB}", "\u{FEDC}"], // ک (Persian Kaf)
        "\u{0644}" => ["\u{FEDD}", "\u{FEDE}", "\u{FEDF}", "\u{FEE0}"], // ل
        "\u{0645}" => ["\u{FEE1}", "\u{FEE2}", "\u{FEE3}", "\u{FEE4}"], // م
        "\u{0646}" => ["\u{FEE5}", "\u{FEE6}", "\u{FEE7}", "\u{FEE8}"], // ن
        "\u{0647}" => ["\u{FEE9}", "\u{FEEA}", "\u{FEEB}", "\u{FEEC}"], // ه
        "\u{0648}" => ["\u{FEED}", "\u{FEEE}", "", ""], // و - final only, no initial/medial
        
        // Persian specific characters
        "\u{067E}" => ["\u{FB56}", "\u{FB57}", "\u{FB58}", "\u{FB59}"], // پ
        "\u{0686}" => ["\u{FB7A}", "\u{FB7B}", "\u{FB7C}", "\u{FB7D}"], // چ
        "\u{06AF}" => ["\u{FB92}", "\u{FB93}", "\u{FB94}", "\u{FB95}"], // گ
        "\u{0698}" => ["\u{FB8A}", "\u{FB8B}", "", ""], // ژ - final only, no initial/medial
        
        // Persian Yeh (ی)
        // Format: [isolated, final, initial, medial]
        // U+06CC: Persian Yeh (ی)
        // Based on PersianRender.php: 'ی' => ['ی', 'ی', 'ی'] which means all forms are filled
        // All forms are filled to allow proper connection to other characters
        "\u{06CC}" => ["\u{06CC}", "\u{06CC}", "\u{06CC}", "\u{06CC}"], // ی (Persian Yeh) - all forms filled for connection
    ];

    /**
     * Persian characters that connect to the next character
     */
    private const CONNECTING_CHARS = [
        "\u{0628}", "\u{062A}", "\u{062C}", "\u{062D}", "\u{062E}",
        "\u{0633}", "\u{0634}", "\u{0635}", "\u{0636}", "\u{0637}", "\u{0638}",
        "\u{0639}", "\u{063A}", "\u{0641}", "\u{0642}", "\u{06A9}", "\u{0644}",
        "\u{0645}", "\u{0646}", "\u{0647}", "\u{067E}",
        "\u{0686}", "\u{06AF}", "\u{06CC}"
    ];

    /**
     * Persian characters that don't connect to the previous character
     */
    private const NON_CONNECTING_CHARS = [
        "\u{0622}", "\u{0627}", "\u{062F}",
        "\u{0631}", "\u{0632}", "\u{0648}", "\u{0698}"
    ];

    /**
     * Lam-Alef combinations (Persian only - no Arabic-specific combinations)
     */
    private const LAM_ALEF_COMBINATIONS = [
        "\u{0644}\u{0627}" => "\u{FEFB}", // لا
        "\u{0644}\u{0622}" => "\u{FEF5}", // لآ
    ];

    /**
     * Diacritics (tashkeel) that should be preserved
     */
    private const DIACRITICS = [
        "\u{064B}", "\u{064C}", "\u{064D}", "\u{064E}", "\u{064F}", "\u{0650}",
        "\u{0651}", "\u{0652}", "\u{0653}", "\u{0654}", "\u{0655}", "\u{0656}",
        "\u{0657}", "\u{0658}", "\u{0659}", "\u{065A}", "\u{065B}", "\u{065C}",
        "\u{065D}", "\u{065E}", "\u{065F}", "\u{0670}"
    ];

    /**
     * Reshape Persian text to contextual forms
     *
     * @param string $text Input text to reshape
     * @return string Reshaped text with proper character forms
     */
    public function reshape(string $text): string
    {
        if (empty($text)) {
            return $text;
        }

        // Handle Lam-Alef combinations first
        $text = $this->handleLamAlef($text);

        // Separate diacritics from base characters
        $chars = $this->separateDiacritics(mb_str_split($text, 1, 'UTF-8'));
        
        $reshaped = [];
        $length = count($chars);

        for ($i = 0; $i < $length; $i++) {
            $char = $chars[$i]['char'];
            $diacritics = $chars[$i]['diacritics'];

            if (!isset(self::CHARACTER_FORMS[$char])) {
                $reshaped[] = $char . $diacritics;
                continue;
            }

            // Determine character form
            $prevChar = ($i > 0) ? $chars[$i - 1]['char'] : null;
            $nextChar = ($i < $length - 1) ? $chars[$i + 1]['char'] : null;

            $form = $this->determineForm($char, $prevChar, $nextChar);
            $reshapedChar = self::CHARACTER_FORMS[$char][$form];

            $reshaped[] = $reshapedChar . $diacritics;
        }

        return implode('', $reshaped);
    }

    /**
     * Handle Lam-Alef combinations
     *
     * @param string $text Input text
     * @return string Text with Lam-Alef combinations replaced
     */
    private function handleLamAlef(string $text): string
    {
        foreach (self::LAM_ALEF_COMBINATIONS as $combination => $replacement) {
            $text = str_replace($combination, $replacement, $text);
        }
        return $text;
    }

    /**
     * Separate diacritics from base characters
     *
     * @param array $chars Array of characters
     * @return array Array with 'char' and 'diacritics' keys
     */
    private function separateDiacritics(array $chars): array
    {
        $result = [];
        $i = 0;

        while ($i < count($chars)) {
            $char = $chars[$i];
            $diacritics = '';

            // Collect following diacritics
            $j = $i + 1;
            while ($j < count($chars) && in_array($chars[$j], self::DIACRITICS, true)) {
                $diacritics .= $chars[$j];
                $j++;
            }

            $result[] = [
                'char' => $char,
                'diacritics' => $diacritics
            ];

            $i = $j;
        }

        return $result;
    }

    /**
     * Determine the form of a character (isolated, final, initial, medial)
     * Based on correct logic similar to PersianRender.php:
     * - Check if previous character can connect forward (has initial form)
     * - Check if current character can connect forward (has initial form)
     * - Check if next character can connect backward (has final form)
     *
     * @param string $char Current character
     * @param string|null $prevChar Previous character
     * @param string|null $nextChar Next character
     * @return int Form index (0=isolated, 1=final, 2=initial, 3=medial)
     */
    private function determineForm(string $char, ?string $prevChar, ?string $nextChar): int
    {
        $result = 0;

        // Based on PersianRender.php logic:
        // Check if current character can connect forward (to next character)
        // Current character must have initial form (index 2) and next character must have final form (index 1)
        // In PersianRender: checks if char[2] exists and next[0] exists
        // In our format [isolated, final, initial, medial]: checks if char[2] exists and next[1] exists
        if ($nextChar !== null && 
            isset(self::CHARACTER_FORMS[$char]) &&
            isset(self::CHARACTER_FORMS[$nextChar]) &&
            self::CHARACTER_FORMS[$char][2] !== '' && // Current char has initial form (not empty)
            self::CHARACTER_FORMS[$nextChar][1] !== '') { // Next char has final form (not empty)
            $result += 4; // Can connect forward (initial)
        }

        // Check if previous character can connect forward (to current character)
        // Previous character must have initial form (index 2) and current character must have final form (index 1)
        // In PersianRender: checks if char[0] exists and prev[2] exists
        // In our format: checks if char[1] exists and prev[2] exists
        if ($prevChar !== null && 
            isset(self::CHARACTER_FORMS[$prevChar]) &&
            isset(self::CHARACTER_FORMS[$char]) &&
            self::CHARACTER_FORMS[$prevChar][2] !== '' && // Previous char has initial form (not empty)
            self::CHARACTER_FORMS[$char][1] !== '') { // Current char has final form (not empty)
            $result += 2; // Can connect from previous (final)
        }

        // Determine form based on result
        if ($result === 6) {
            return 3; // Medial (connected from both sides)
        } elseif ($result === 4) {
            return 2; // Initial (connects to next)
        } elseif ($result === 2) {
            return 1; // Final (connected from previous)
        } else {
            return 0; // Isolated (no connections)
        }
    }
}

