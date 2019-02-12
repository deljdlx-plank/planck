<?php

namespace Planck\Helper;


class StringUtil
{

    public static function slugify($string)
    {

        $normalized = mb_strtolower($string);
        $normalized = static::removeAccent($normalized);
        $normalized=preg_replace('`\W`', '-', $normalized);
        $normalized=preg_replace('`-+`', '-', $normalized);
        return $normalized;
    }


    public static function removeAccent($str, $charset = 'utf-8')
    {
        $str = htmlentities($str, ENT_NOQUOTES, $charset);
        $str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
        $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
        $str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères
        return $str;
    }







    public static function toCamelCase($string, $separator = '-', $upper = true)
    {
        if($upper) {
            $string = ucfirst($string);
        }

        $string = preg_replace_callback('`('.$separator.'.)`', function($matches) {
            $string = str_replace('-', '', $matches[1]);
            $string = strtoupper($string);
            return $string;
        }, $string);

        return $string;
    }

    public static function camelCaseToSeparated($string, $separator = '-', $toLower = true)
    {

        $string = preg_replace_callback('`([A-Z])`', function($matches) use ($separator) {

            return $separator.mb_strtolower($matches[1]);

        }, $string);

        $string = preg_replace('`^'.$separator.'`', '', $string);

        if($toLower) {
            $string = mb_strtolower($string);
        }

        return $string;
    }


    public static function getClassBaseName($className)
    {
        return basename(str_replace(
            '\\', '/',
            $className
        ));
    }


    public static function mb_ucfirst($str, $encoding = "UTF-8", $lower_str_end = false) {

        if (function_exists('mb_ucfirst')) {
           return mb_ucfirst($str, $encoding, $lower_str_end);
        };

        $first_letter = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding);
        $str_end = "";
        if ($lower_str_end) {
            $str_end = mb_strtolower(mb_substr($str, 1, mb_strlen($str, $encoding), $encoding), $encoding);
        }
        else {
            $str_end = mb_substr($str, 1, mb_strlen($str, $encoding), $encoding);
        }
        $str = $first_letter . $str_end;
        return $str;
    }

}
