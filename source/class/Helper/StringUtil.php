<?php

namespace Planck\Helper;


class StringUtil
{

    public static function slugify($string, $replacement = '-', $allow = [])
    {

        $normalized = mb_strtolower($string);
        $normalized = static::removeAccent($normalized);
        $normalized = trim($normalized);

        $normalized=preg_replace('`[^A-Za-z0-9\-_'.implode('', (array) $allow).']`', $replacement, $normalized);
        $normalized=preg_replace('`'.self::escapeRegexp($replacement).'+`', $replacement, $normalized);
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




    public static function escapeRegexp($string)
    {
        return preg_quote($string);
    }



    public static function namespaceToSeparated($namespace, $separator = '-')
    {
        return str_replace('\\', $separator, $namespace);
    }


    public static function separatedToClassName($string, $separator = '-')
    {

        $escapedSeparator = static::escapeRegexp($separator);

        return preg_replace_callback('`('.$escapedSeparator.'|^)(.)`',
            function($matches) use ($separator) {

                if($matches[1] == $separator) {
                    return '\\'.strtoupper($matches[2]);
                }
                else {
                    return strtoupper($matches[2]);
                }


            }, $string
        );
    }


    public static function toCamelCase($string, $separator = '-', $upper = true)
    {
        if($upper) {
            $string = ucfirst($string);
        }

        $escapedSeparator = static::escapeRegexp($separator);

        $string = preg_replace_callback('`('.$escapedSeparator.'.)`', function($matches) use ($separator) {

            $string = str_replace($separator, '', $matches[1]);
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
        if(is_object($className)) {
            $className = get_class($className);
        }

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
