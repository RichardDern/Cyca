<?php

namespace App\Helpers;

use ForceUTF8\Encoding as UTF8;

/**
 * Helper class to cleanup, format, sanitize strings.
 */
class Cleaner
{
    /**
     * Ensures string doesn't contain any "undesirable" characters, such as
     * extra-spaces or line-breaks. This is not a purifying method. Only basic
     * cleanup is done here.
     *
     * @param string $string
     * @param mixed  $stripTags
     * @param mixed  $removeExtraSpaces
     *
     * @return string
     */
    public static function cleanupString($string, $stripTags = false, $removeExtraSpaces = false)
    {
        if (empty($string)) {
            return null;
        }

        $string = trim($string);
        $string = UTF8::toUTF8($string, UTF8::ICONV_TRANSLIT);
        $string = html_entity_decode($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $string = str_replace('&apos;', "'", $string);

        if ($removeExtraSpaces) {
            $string = preg_replace('/[[:space:]]+/', ' ', $string);
        }

        if ($stripTags) {
            return strip_tags(trim($string));
        }

        return self::sanitize($string);
    }

    /**
     * Perform some sanitizing actions on specified string.
     *
     * @param mixed $string
     *
     * @return string
     */
    public static function sanitize($string)
    {
        return $string;
    }
}
