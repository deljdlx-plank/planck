<?php

namespace Planck\Helper;

class File
{


    public static function getExtension($filename)
    {
        return pathinfo($filename, PATHINFO_EXTENSION);
    }



    public static function sanitizePath($path)
    {

        $path = preg_replace('`\.+`', '.', $path);
        $path = preg_replace('`\s`', '-', $path);


        return $path;
    }


    public static function normalize($path)
    {
        return str_replace('\\', '/', $path);
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

    public static function rglob($pattern, $flags = 0, $normalize = true)
    {

        if(!$normalize) {
            $files = glob($pattern, $flags);

        }
        else {
            $temp =  glob($pattern, $flags);

            $files = [];
            foreach ($temp as $path) {
                $files[] = str_replace('\\', '/', $path);
            }
        }

        //foreach (glob(dirname($pattern).'/*', GLOB_NOSORT ) as $dir) {
        foreach (glob(dirname($pattern).'/*', 0 ) as $dir) {
            $files = array_merge($files, static::rglob($dir.'/'.basename($pattern), $flags, $normalize));
        }

        return $files;
    }


}


