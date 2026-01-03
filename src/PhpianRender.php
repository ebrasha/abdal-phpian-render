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
 */
class PhpianRender
{
    private Reshaper $reshaper;
    private BiDi $bidi;
    private NumberConverter $numberConverter;
    private DiacriticsHandler $diacriticsHandler;
    private Helper $helper;

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
            'numberLocale' => 'persian', // 'persian' or 'arabic'
            'preserveDiacritics' => true,
            'clean' => false,
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
        if ($options['bidi']) {
            $text = $this->bidi->process($text);
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
     * @param string $locale Target locale ('persian' or 'arabic')
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
}

