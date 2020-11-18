<?php

namespace App\Models\Traits;

trait HasUrl
{
    /**
     * Return url in its idn form. Adds HTML markup to "syntax highlight" url
     * elements.
     *
     * @return string
     */
    public function getAsciiUrlAttribute()
    {
        if (empty($this->attributes['url'])) {
            return null;
        }

        mb_internal_encoding('UTF-8');

        $url = \urldecode($this->attributes['url']);
        
        $host     = \parse_url($url, PHP_URL_HOST);
        $ascii    = \idn_to_ascii($host);
        $idnUrl   = str_replace($host, $ascii, $url);
        $finalUrl = '';

        foreach (preg_split('//u', $idnUrl, null, PREG_SPLIT_NO_EMPTY) as $char) {
            if (mb_strlen($char) != strlen($char)) {
                $class = 'suspicious';
            } elseif (preg_match('#[A-Z]#', $char)) {
                $class = 'capital';
            } elseif (preg_match('#[a-z]#', $char)) {
                $class = 'letter';
            } elseif (preg_match('#[0-9]#', $char)) {
                $class = 'number';
            } elseif (preg_match('#([:/.?$\#_=])#', $char)) {
                $class = 'operator';
            } elseif (empty($char)) {
                $class = 'empty';
            } else {
                $class = 'other';
            }

            $finalUrl .= sprintf('<span class="%s">%s</span>', $class, $char);
        }

        return $finalUrl;
    }
}
