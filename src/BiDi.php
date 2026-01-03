<?php

/**
 **********************************************************************
 * -------------------------------------------------------------------
 * Project Name : Abdal Phpian Render
 * File Name    : BiDi.php
 * Author       : Ebrahim Shafiei (EbraSha)
 * Email        : Prof.Shafiei@Gmail.com
 * Created On   : 2026-01-02 21:35:22
 * Description  : Bidirectional text algorithm implementation for mixed RTL/LTR text
 * -------------------------------------------------------------------
 *
 * "Coding is an engaging and beloved hobby for me. I passionately and insatiably pursue knowledge in cybersecurity and programming."
 * – Ebrahim Shafiei
 *
 **********************************************************************
 */

namespace Abdal\PhpianRender;

/**
 * BiDi class handles bidirectional text processing
 * for mixed Persian and English text
 */
class BiDi
{
    /**
     * RTL characters range (Persian, Hebrew, etc.)
     */
    private const RTL_RANGE_START = 0x0590;
    private const RTL_RANGE_END = 0x08FF;

    /**
     * LTR punctuation marks that need to be mirrored
     */
    private const MIRRORED_PUNCTUATION = [
        '(' => ')',
        ')' => '(',
        '[' => ']',
        ']' => '[',
        '{' => '}',
        '}' => '{',
        '<' => '>',
        '>' => '<',
    ];

    /**
     * Neutral punctuation that should be treated based on context
     */
    private const NEUTRAL_PUNCTUATION = [
        '.', ',', ';', ':', '!', '?', '-', '_', '|', '/', '\\', '&', '*', '%', '$', '#', '@', '~', '`', '^'
    ];

    /**
     * Process bidirectional text
     *
     * @param string $text Input text
     * @return string Processed text with correct bidirectional ordering
     */
    public function process(string $text): string
    {
        if (empty($text)) {
            return $text;
        }

        // Split text into segments (words and punctuation)
        $segments = $this->segmentText($text);
        
        // Determine base direction
        $baseDirection = $this->detectBaseDirection($text);
        
        // Process segments based on direction
        $processed = $this->reorderSegments($segments, $baseDirection);
        
        // Mirror punctuation if needed
        $processed = $this->mirrorPunctuation($processed, $baseDirection);
        
        return implode('', $processed);
    }

    /**
     * Segment text into words and punctuation
     *
     * @param string $text Input text
     * @return array Array of segments with direction info
     */
    private function segmentText(string $text): array
    {
        $segments = [];
        $chars = mb_str_split($text, 1, 'UTF-8');
        $currentSegment = '';
        $currentDirection = null;

        foreach ($chars as $char) {
            $charDirection = $this->getCharDirection($char);

            if ($charDirection === 'neutral') {
                // Neutral characters (spaces, punctuation) break segments
                if (!empty($currentSegment)) {
                    $segments[] = [
                        'text' => $currentSegment,
                        'direction' => $currentDirection
                    ];
                    $currentSegment = '';
                }
                $segments[] = [
                    'text' => $char,
                    'direction' => 'neutral'
                ];
            } else {
                if ($currentDirection !== null && $currentDirection !== $charDirection) {
                    // Direction change
                    if (!empty($currentSegment)) {
                        $segments[] = [
                            'text' => $currentSegment,
                            'direction' => $currentDirection
                        ];
                    }
                    $currentSegment = $char;
                } else {
                    $currentSegment .= $char;
                }
                $currentDirection = $charDirection;
            }
        }

        if (!empty($currentSegment)) {
            $segments[] = [
                'text' => $currentSegment,
                'direction' => $currentDirection
            ];
        }

        return $segments;
    }

    /**
     * Get character direction
     *
     * @param string $char Character
     * @return string 'rtl', 'ltr', or 'neutral'
     */
    private function getCharDirection(string $char): string
    {
        if (mb_strlen($char, 'UTF-8') === 0) {
            return 'neutral';
        }

        $codePoint = $this->getCodePoint($char);

        // Check if it's RTL
        if (($codePoint >= self::RTL_RANGE_START && $codePoint <= self::RTL_RANGE_END) ||
            ($codePoint >= 0x0600 && $codePoint <= 0x06FF)) { // Arabic block
            return 'rtl';
        }

        // Check if it's LTR (Latin, numbers, etc.)
        if (($codePoint >= 0x0041 && $codePoint <= 0x007A) || // Basic Latin
            ($codePoint >= 0x0030 && $codePoint <= 0x0039)) { // Numbers
            return 'ltr';
        }

        // Check if it's neutral punctuation
        if (in_array($char, self::NEUTRAL_PUNCTUATION, true) ||
            in_array($char, array_keys(self::MIRRORED_PUNCTUATION), true) ||
            in_array($char, array_values(self::MIRRORED_PUNCTUATION), true) ||
            ctype_space($char)) {
            return 'neutral';
        }

        return 'neutral';
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
     * Detect base direction of text
     *
     * @param string $text Input text
     * @return string 'rtl' or 'ltr'
     */
    private function detectBaseDirection(string $text): string
    {
        $rtlCount = 0;
        $ltrCount = 0;
        $chars = mb_str_split($text, 1, 'UTF-8');

        foreach ($chars as $char) {
            $direction = $this->getCharDirection($char);
            if ($direction === 'rtl') {
                $rtlCount++;
            } elseif ($direction === 'ltr') {
                $ltrCount++;
            }
        }

        return $rtlCount >= $ltrCount ? 'rtl' : 'ltr';
    }

    /**
     * Reorder segments based on bidirectional rules
     *
     * @param array $segments Array of segments
     * @param string $baseDirection Base direction
     * @return array Reordered segments
     */
    private function reorderSegments(array $segments, string $baseDirection): array
    {
        if ($baseDirection === 'ltr') {
            return array_column($segments, 'text');
        }

        // For RTL base direction, reverse the order
        $result = [];
        $currentRun = [];
        $currentDirection = null;

        foreach ($segments as $segment) {
            if ($segment['direction'] === 'neutral') {
                if (!empty($currentRun)) {
                    $result = array_merge($result, array_reverse($currentRun));
                    $currentRun = [];
                }
                $result[] = $segment['text'];
            } else {
                if ($currentDirection !== null && $currentDirection !== $segment['direction']) {
                    // Direction change - process current run
                    $result = array_merge($result, array_reverse($currentRun));
                    $currentRun = [$segment['text']];
                } else {
                    $currentRun[] = $segment['text'];
                }
                $currentDirection = $segment['direction'];
            }
        }

        if (!empty($currentRun)) {
            $result = array_merge($result, array_reverse($currentRun));
        }

        return $result;
    }

    /**
     * Mirror punctuation marks based on direction
     *
     * @param array $segments Array of text segments
     * @param string $baseDirection Base direction
     * @return array Segments with mirrored punctuation
     */
    private function mirrorPunctuation(array $segments, string $baseDirection): array
    {
        if ($baseDirection !== 'rtl') {
            return $segments;
        }

        $result = [];
        foreach ($segments as $segment) {
            $chars = mb_str_split($segment, 1, 'UTF-8');
            $mirrored = '';

            foreach ($chars as $char) {
                if (isset(self::MIRRORED_PUNCTUATION[$char])) {
                    $mirrored .= self::MIRRORED_PUNCTUATION[$char];
                } else {
                    $mirrored .= $char;
                }
            }

            $result[] = $mirrored;
        }

        return $result;
    }
}

