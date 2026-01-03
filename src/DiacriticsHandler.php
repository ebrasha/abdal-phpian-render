<?php

/**
 **********************************************************************
 * -------------------------------------------------------------------
 * Project Name : Abdal Phpian Render
 * File Name    : DiacriticsHandler.php
 * Author       : Ebrahim Shafiei (EbraSha)
 * Email        : Prof.Shafiei@Gmail.com
 * Created On   : 2026-01-02 21:35:22
 * Description  : Handles Persian diacritics (tashkeel) preservation during text processing
 * -------------------------------------------------------------------
 *
 * "Coding is an engaging and beloved hobby for me. I passionately and insatiably pursue knowledge in cybersecurity and programming."
 * – Ebrahim Shafiei
 *
 **********************************************************************
 */

namespace Abdal\PhpianRender;

/**
 * DiacriticsHandler class manages Persian diacritics
 * (fatha, damma, kasra, tanween, etc.)
 */
class DiacriticsHandler
{
    /**
     * Diacritics mapping with their names
     */
    private const DIACRITICS = [
        "\u{064B}" => 'fathatan',    // ً
        "\u{064C}" => 'dammatan',    // ٌ
        "\u{064D}" => 'kasratan',    // ٍ
        "\u{064E}" => 'fatha',       // َ
        "\u{064F}" => 'damma',       // ُ
        "\u{0650}" => 'kasra',       // ِ
        "\u{0651}" => 'shadda',      // ّ
        "\u{0652}" => 'sukun',       // ْ
        "\u{0653}" => 'maddah',      // ٓ
        "\u{0654}" => 'hamza_above', // ٔ
        "\u{0655}" => 'hamza_below', // ٕ
        "\u{0656}" => 'subscript_alef', // ٖ
        "\u{0657}" => 'inverted_damma', // ٗ
        "\u{0658}" => 'mark_noon_ghunna', // ٘
        "\u{0659}" => 'zwarakay',   // ٙ
        "\u{065A}" => 'vowel_sign_small_v', // ٚ
        "\u{065B}" => 'vowel_sign_inverted_small_v', // ٛ
        "\u{065C}" => 'dot_below',  // ٜ
        "\u{065D}" => 'reversed_damma', // ٝ
        "\u{065E}" => 'fatha_with_two_dots', // ٞ
        "\u{065F}" => 'wavy_hamza_below', // ٟ
        "\u{0670}" => 'superscript_alef', // ٰ
    ];

    /**
     * Extract diacritics from text
     *
     * @param string $text Input text
     * @return array Array with 'base' (text without diacritics) and 'diacritics' (array of diacritics per position)
     */
    public function extract(string $text): array
    {
        $chars = mb_str_split($text, 1, 'UTF-8');
        $base = [];
        $diacritics = [];
        $position = 0;

        foreach ($chars as $char) {
            if ($this->isDiacritic($char)) {
                if (!isset($diacritics[$position - 1])) {
                    $diacritics[$position - 1] = [];
                }
                $diacritics[$position - 1][] = $char;
            } else {
                $base[] = $char;
                $position++;
            }
        }

        return [
            'base' => implode('', $base),
            'diacritics' => $diacritics
        ];
    }

    /**
     * Apply diacritics to base text
     *
     * @param string $baseText Base text without diacritics
     * @param array $diacritics Array of diacritics per position
     * @return string Text with diacritics applied
     */
    public function apply(string $baseText, array $diacritics): string
    {
        $chars = mb_str_split($baseText, 1, 'UTF-8');
        $result = [];

        foreach ($chars as $index => $char) {
            $result[] = $char;
            if (isset($diacritics[$index]) && is_array($diacritics[$index])) {
                $result[] = implode('', $diacritics[$index]);
            }
        }

        return implode('', $result);
    }

    /**
     * Remove all diacritics from text
     *
     * @param string $text Input text
     * @return string Text without diacritics
     */
    public function remove(string $text): string
    {
        $chars = mb_str_split($text, 1, 'UTF-8');
        $result = [];

        foreach ($chars as $char) {
            if (!$this->isDiacritic($char)) {
                $result[] = $char;
            }
        }

        return implode('', $result);
    }

    /**
     * Check if a character is a diacritic
     *
     * @param string $char Character to check
     * @return bool True if character is a diacritic
     */
    public function isDiacritic(string $char): bool
    {
        return isset(self::DIACRITICS[$char]);
    }

    /**
     * Preserve diacritics during reshaping
     * This method ensures diacritics are not lost when characters are reshaped
     *
     * @param string $originalText Original text with diacritics
     * @param string $reshapedText Reshaped text without diacritics
     * @return string Reshaped text with diacritics preserved
     */
    public function preserveDuringReshape(string $originalText, string $reshapedText): string
    {
        $extracted = $this->extract($originalText);
        $baseChars = mb_str_split($extracted['base'], 1, 'UTF-8');
        $reshapedChars = mb_str_split($reshapedText, 1, 'UTF-8');

        // Map diacritics from original to reshaped
        $newDiacritics = [];
        $originalIndex = 0;

        foreach ($baseChars as $originalChar) {
            // Find matching character in reshaped text
            $found = false;
            for ($i = 0; $i < count($reshapedChars); $i++) {
                if ($this->isSameBaseCharacter($originalChar, $reshapedChars[$i])) {
                    if (isset($extracted['diacritics'][$originalIndex])) {
                        $newDiacritics[$i] = $extracted['diacritics'][$originalIndex];
                    }
                    $found = true;
                    break;
                }
            }
            if (!$found && isset($extracted['diacritics'][$originalIndex])) {
                // If character not found, try to attach to nearest character
                $newDiacritics[0] = $extracted['diacritics'][$originalIndex];
            }
            $originalIndex++;
        }

        return $this->apply($reshapedText, $newDiacritics);
    }

    /**
     * Check if two characters are the same base character
     * (ignoring diacritics and form variations)
     *
     * @param string $char1 First character
     * @param string $char2 Second character
     * @return bool True if same base character
     */
    private function isSameBaseCharacter(string $char1, string $char2): bool
    {
        // Remove diacritics and compare
        $clean1 = $this->remove($char1);
        $clean2 = $this->remove($char2);

        // Normalize to base form for comparison
        $normalized1 = $this->normalizeToBaseForm($clean1);
        $normalized2 = $this->normalizeToBaseForm($clean2);

        return $normalized1 === $normalized2;
    }

    /**
     * Normalize character to its base form for comparison
     *
     * @param string $char Character to normalize
     * @return string Normalized character
     */
    private function normalizeToBaseForm(string $char): string
    {
        // Remove diacritics first
        $char = $this->remove($char);
        
        // Map contextual forms to base forms
        // This is a simplified mapping - in production, you'd want a complete mapping
        $baseForms = [
            // Alef variations
            "\u{FE81}" => "\u{0622}", "\u{FE82}" => "\u{0622}",
            "\u{FE83}" => "\u{0623}", "\u{FE84}" => "\u{0623}",
            "\u{FE87}" => "\u{0625}", "\u{FE88}" => "\u{0625}",
            "\u{FE8D}" => "\u{0627}", "\u{FE8E}" => "\u{0627}",
            // Ba
            "\u{FE8F}" => "\u{0628}", "\u{FE90}" => "\u{0628}",
            "\u{FE91}" => "\u{0628}", "\u{FE92}" => "\u{0628}",
            // Ta
            "\u{FE95}" => "\u{062A}", "\u{FE96}" => "\u{062A}",
            "\u{FE97}" => "\u{062A}", "\u{FE98}" => "\u{062A}",
            // Jeem
            "\u{FE9D}" => "\u{062C}", "\u{FE9E}" => "\u{062C}",
            "\u{FE9F}" => "\u{062C}", "\u{FEA0}" => "\u{062C}",
            // Persian characters
            "\u{FB56}" => "\u{067E}", "\u{FB57}" => "\u{067E}",
            "\u{FB58}" => "\u{067E}", "\u{FB59}" => "\u{067E}",
            "\u{FB7A}" => "\u{0686}", "\u{FB7B}" => "\u{0686}",
            "\u{FB7C}" => "\u{0686}", "\u{FB7D}" => "\u{0686}",
            "\u{FB92}" => "\u{06AF}", "\u{FB93}" => "\u{06AF}",
            "\u{FB94}" => "\u{06AF}", "\u{FB95}" => "\u{06AF}",
        ];

        return $baseForms[$char] ?? $char;
    }
}

