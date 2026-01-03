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
     * Map of Persian characters to their presentation forms.
     * Each key is the Unicode point (decimal), and the value is an array of forms:
     * [Isolated, Final, Initial, Medial]
     *
     * @var array
     */
    private array $charsMap;

    /**
     * Characters that do not connect to the left (next character).
     * Examples: Alef, Dal, Zal, Re, Ze, Zhe, Vav.
     *
     * @var array
     */
    private array $nonConnectors;

    /**
     * Constructor to initialize the character maps.
     */
    public function __construct()
    {
        $this->initializeMaps();
    }

    /**
     * The main reshaping method.
     *
     * @param string $text The input string (UTF-8).
     * @return string The reshaped string with presentation forms.
     */
    public function reshape(string $text): string
    {
        if (empty($text)) {
            return '';
        }

        // Convert the string to an array of Unicode code points
        $codePoints = $this->utf8ToUnicode($text);
        $count = count($codePoints);
        $result = [];

        for ($i = 0; $i < $count; $i++) {
            $current = $codePoints[$i];

            // If the character is not in our map, keep it as is
            if (!isset($this->charsMap[$current])) {
                $result[] = $current;
                continue;
            }

            $prev = ($i > 0) ? $codePoints[$i - 1] : null;
            $next = ($i < $count - 1) ? $codePoints[$i + 1] : null;

            // Determine connectivity
            $connectToPrev = $this->canConnectToPrev($prev, $current);
            $connectToNext = $this->canConnectToNext($current, $next);

            // Select the appropriate form
            if ($connectToPrev && $connectToNext) {
                // Medial form
                $form = $this->charsMap[$current][3];
            } elseif ($connectToPrev) {
                // Final form
                $form = $this->charsMap[$current][1];
            } elseif ($connectToNext) {
                // Initial form
                $form = $this->charsMap[$current][2];
            } else {
                // Isolated form
                $form = $this->charsMap[$current][0];
            }

            $result[] = $form;
        }

        // Convert the reshaped code points back to UTF-8 string
        return $this->unicodeToUtf8($result);
    }

    /**
     * Checks if the current character can connect to the previous character.
     *
     * @param int|null $prev The previous character code point.
     * @param int $current The current character code point.
     * @return bool
     */
    private function canConnectToPrev(?int $prev, int $current): bool
    {
        if ($prev === null) {
            return false;
        }

        // Check if the previous character is a valid Persian letter
        if (!isset($this->charsMap[$prev])) {
            return false;
        }

        // Check if the previous character is a non-connector (like Alef, Re, etc.)
        if (in_array($prev, $this->nonConnectors)) {
            return false;
        }

        return true;
    }

    /**
     * Checks if the current character can connect to the next character.
     *
     * @param int $current The current character code point.
     * @param int|null $next The next character code point.
     * @return bool
     */
    private function canConnectToNext(int $current, ?int $next): bool
    {
        if ($next === null) {
            return false;
        }

        // Check if the next character is a valid Persian letter
        if (!isset($this->charsMap[$next])) {
            return false;
        }

        return true;
    }

    /**
     * Converts a UTF-8 string to an array of Unicode code points.
     *
     * @param string $str
     * @return array
     */
    private function utf8ToUnicode(string $str): array
    {
        $result = [];
        $length = mb_strlen($str, 'UTF-8');

        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($str, $i, 1, 'UTF-8');
            $result[] = mb_ord($char, 'UTF-8');
        }

        return $result;
    }

    /**
     * Converts an array of Unicode code points back to a UTF-8 string.
     *
     * @param array $codePoints
     * @return string
     */
    private function unicodeToUtf8(array $codePoints): string
    {
        $str = '';

        foreach ($codePoints as $cp) {
            $str .= mb_chr($cp, 'UTF-8');
        }

        return $str;
    }

    /**
     * Initializes the character maps.
     * Only Persian characters are included (no Arabic-specific characters).
     */
    private function initializeMaps(): void
    {
        // Format: [Isolated, Final, Initial, Medial]
        // Values are decimal Unicode of Presentation Forms-B or A
        $this->charsMap = [
            // --- Standard Persian Letters ---
            // Alef (0x0627) - Non-connector
            0x0627 => [0xFE8D, 0xFE8E, 0xFE8D, 0xFE8E],
            // Alef with Madda (0x0622) - Non-connector
            0x0622 => [0xFE81, 0xFE82, 0xFE81, 0xFE82],
            // Beh (0x0628)
            0x0628 => [0xFE8F, 0xFE90, 0xFE91, 0xFE92],
            // Peh (Persian) (0x067E)
            0x067E => [0xFB56, 0xFB57, 0xFB58, 0xFB59],
            // Teh (0x062A)
            0x062A => [0xFE95, 0xFE96, 0xFE97, 0xFE98],
            // Theh (0x062B)
            0x062B => [0xFE99, 0xFE9A, 0xFE9B, 0xFE9C],
            // Jeem (0x062C)
            0x062C => [0xFE9D, 0xFE9E, 0xFE9F, 0xFEA0],
            // Che (Persian) (0x0686)
            0x0686 => [0xFB7A, 0xFB7B, 0xFB7C, 0xFB7D],
            // Hah (0x062D)
            0x062D => [0xFEA1, 0xFEA2, 0xFEA3, 0xFEA4],
            // Khah (0x062E)
            0x062E => [0xFEA5, 0xFEA6, 0xFEA7, 0xFEA8],
            // Dal (0x062F) - Non-connector
            0x062F => [0xFEA9, 0xFEAA, 0xFEA9, 0xFEAA],
            // Thal (0x0630) - Non-connector
            0x0630 => [0xFEAB, 0xFEAC, 0xFEAB, 0xFEAC],
            // Reh (0x0631) - Non-connector
            0x0631 => [0xFEAD, 0xFEAE, 0xFEAD, 0xFEAE],
            // Zain (0x0632) - Non-connector
            0x0632 => [0xFEAF, 0xFEB0, 0xFEAF, 0xFEB0],
            // Zhe (Persian) (0x0698) - Non-connector
            0x0698 => [0xFB8A, 0xFB8B, 0xFB8A, 0xFB8B],
            // Seen (0x0633)
            0x0633 => [0xFEB1, 0xFEB2, 0xFEB3, 0xFEB4],
            // Sheen (0x0634)
            0x0634 => [0xFEB5, 0xFEB6, 0xFEB7, 0xFEB8],
            // Sad (0x0635)
            0x0635 => [0xFEB9, 0xFEBA, 0xFEBB, 0xFEBC],
            // Dad (0x0636)
            0x0636 => [0xFEBD, 0xFEBE, 0xFEBF, 0xFEC0],
            // Tah (0x0637)
            0x0637 => [0xFEC1, 0xFEC2, 0xFEC3, 0xFEC4],
            // Zah (0x0638)
            0x0638 => [0xFEC5, 0xFEC6, 0xFEC7, 0xFEC8],
            // Ain (0x0639)
            0x0639 => [0xFEC9, 0xFECA, 0xFECB, 0xFECC],
            // Ghain (0x063A)
            0x063A => [0xFECD, 0xFECE, 0xFECF, 0xFED0],
            // Feh (0x0641)
            0x0641 => [0xFED1, 0xFED2, 0xFED3, 0xFED4],
            // Qaf (0x0642)
            0x0642 => [0xFED5, 0xFED6, 0xFED7, 0xFED8],
            // Keheh (Persian Kaf) (0x06A9) - Critical for Persian text
            0x06A9 => [0xFB8E, 0xFB8F, 0xFB90, 0xFB91],
            // Gaf (Persian) (0x06AF)
            0x06AF => [0xFB92, 0xFB93, 0xFB94, 0xFB95],
            // Lam (0x0644)
            0x0644 => [0xFEDD, 0xFEDE, 0xFEDF, 0xFEE0],
            // Meem (0x0645)
            // Forms: Isolated: FEE1, Final: FEE2, Initial: FEE3, Medial: FEE4
            0x0645 => [0xFEE1, 0xFEE2, 0xFEE3, 0xFEE4],
            // Noon (0x0646)
            0x0646 => [0xFEE5, 0xFEE6, 0xFEE7, 0xFEE8],
            // Vav (0x0648) - Non-connector
            0x0648 => [0xFEE9, 0xFEEA, 0xFEE9, 0xFEEA],
            // Heh (0x0647)
            0x0647 => [0xFEEB, 0xFEEC, 0xFEED, 0xFEEE],
            // -----------------------------------------------------------
            // CRITICAL FIX: PERSIAN YE (U+06CC)
            // -----------------------------------------------------------
            // Farsi Yeh (0x06CC)
            // Mapped to Presentation Forms-B which are dotless in final/isolated
            // and have dots in initial/medial.
            // Isolated: 0xFBFC, Final: 0xFBFD, Initial: 0xFBFE, Medial: 0xFBFF
            0x06CC => [0xFBFC, 0xFBFD, 0xFBFE, 0xFBFF],
            // Tatweel (Kashida) (0x0640) - Connects both sides
            0x0640 => [0x0640, 0x0640, 0x0640, 0x0640],
        ];

        // List of characters that do NOT connect to the left (next character)
        $this->nonConnectors = [
            0x0627, // Alef
            0x0622, // Alef Madda
            0x062F, // Dal
            0x0630, // Thal
            0x0631, // Re
            0x0632, // Zain
            0x0698, // Zhe
            0x0648, // Vav
        ];
    }
}
