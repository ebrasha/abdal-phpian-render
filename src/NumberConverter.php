<?php

/**
 **********************************************************************
 * -------------------------------------------------------------------
 * Project Name : Abdal Phpian Render
 * File Name    : NumberConverter.php
 * Author       : Ebrahim Shafiei (EbraSha)
 * Email        : Prof.Shafiei@Gmail.com
 * Created On   : 2026-01-02 21:35:22
 * Description  : Number conversion between English and Persian numerals
 * -------------------------------------------------------------------
 *
 * "Coding is an engaging and beloved hobby for me. I passionately and insatiably pursue knowledge in cybersecurity and programming."
 * – Ebrahim Shafiei
 *
 **********************************************************************
 */

namespace Abdal\PhpianRender;

/**
 * NumberConverter class handles conversion between
 * English and Persian numerals
 */
class NumberConverter
{
    /**
     * English to Persian digit mapping
     */
    private const ENGLISH_TO_PERSIAN = [
        '0' => '۰',
        '1' => '۱',
        '2' => '۲',
        '3' => '۳',
        '4' => '۴',
        '5' => '۵',
        '6' => '۶',
        '7' => '۷',
        '8' => '۸',
        '9' => '۹',
    ];

    /**
     * Persian to English digit mapping
     */
    private const PERSIAN_TO_ENGLISH = [
        '۰' => '0',
        '۱' => '1',
        '۲' => '2',
        '۳' => '3',
        '۴' => '4',
        '۵' => '5',
        '۶' => '6',
        '۷' => '7',
        '۸' => '8',
        '۹' => '9',
    ];

    /**
     * English to Arabic digit mapping
     */
    private const ENGLISH_TO_ARABIC = [
        '0' => '٠',
        '1' => '١',
        '2' => '٢',
        '3' => '٣',
        '4' => '٤',
        '5' => '٥',
        '6' => '٦',
        '7' => '٧',
        '8' => '٨',
        '9' => '٩',
    ];

    /**
     * Arabic to English digit mapping
     */
    private const ARABIC_TO_ENGLISH = [
        '٠' => '0',
        '١' => '1',
        '٢' => '2',
        '٣' => '3',
        '٤' => '4',
        '٥' => '5',
        '٦' => '6',
        '٧' => '7',
        '٨' => '8',
        '٩' => '9',
    ];

    /**
     * Convert English digits to Persian
     *
     * @param string $text Input text containing English digits
     * @return string Text with Persian digits
     */
    public function toPersian(string $text): string
    {
        return strtr($text, self::ENGLISH_TO_PERSIAN);
    }

    /**
     * Convert Persian digits to English
     *
     * @param string $text Input text containing Persian digits
     * @return string Text with English digits
     */
    public function persianToEnglish(string $text): string
    {
        return strtr($text, self::PERSIAN_TO_ENGLISH);
    }

    /**
     * Convert English digits to Arabic
     *
     * @param string $text Input text containing English digits
     * @return string Text with Arabic digits
     */
    public function toArabic(string $text): string
    {
        return strtr($text, self::ENGLISH_TO_ARABIC);
    }

    /**
     * Convert Arabic digits to English
     *
     * @param string $text Input text containing Arabic digits
     * @return string Text with English digits
     */
    public function arabicToEnglish(string $text): string
    {
        return strtr($text, self::ARABIC_TO_ENGLISH);
    }

    /**
     * Convert any digits (Persian/Arabic) to English
     * Note: Arabic digits are still supported for conversion to English
     * but conversion to Arabic is not supported (Persian only)
     *
     * @param string $text Input text
     * @return string Text with English digits
     */
    public function toEnglish(string $text): string
    {
        $text = $this->persianToEnglish($text);
        $text = $this->arabicToEnglish($text); // Still support reading Arabic digits
        return $text;
    }

    /**
     * Convert digits to Persian
     * Note: Only Persian locale is supported (no Arabic conversion)
     *
     * @param string $text Input text
     * @return string Converted text with Persian digits
     */
    public function convertByLocale(string $text): string
    {
        // First convert to English (normalize)
        $text = $this->toEnglish($text);

        // Convert to Persian
        return $this->toPersian($text);
    }
}

