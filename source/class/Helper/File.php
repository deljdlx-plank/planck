<?php

namespace Planck\Helper;

class File
{


    public static function getExtension($filename)
    {
        return pathinfo($filename, PATHINFO_EXTENSION);
    }



    public static function recursiveRmdir($src)
    {
        $dir = opendir($src);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                $full = $src . '/' . $file;
                if (is_dir($full)) {
                    static::recursiveRmdir($full);
                }
                else {
                    unlink($full);
                }
            }
        }
        closedir($dir);
        rmdir($src);
    }

    public static function rglob($pattern, $flags = 0) {
        $files = glob($pattern, $flags);
        foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
            $files = array_merge($files, static::rglob($dir.'/'.basename($pattern), $flags));
        }
        return $files;
    }


}


