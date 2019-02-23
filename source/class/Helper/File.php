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

    public function rcopy($source, $dest, $createDir = false, $copySymlink = true, $doOnSymLink = null)
    {
        // recursive function to copy
        // all subdirectories and contents:
        if(is_dir($source)) {

            if(is_link($source)) {
                if(is_callable($doOnSymLink)) {
                    call_user_func_array(
                        $doOnSymLink,
                        array($source)
                    );
                }
                if(!$copySymlink) {
                    return;
                }
            }

            $dir_handle=opendir($source);
            $sourcefolder = basename($source);

            if($createDir) {
                mkdir($dest."/".$sourcefolder);
                $destinationPath = $dest."/".$sourcefolder;
            }
            else {
                $destinationPath = $dest;
            }

            while($file=readdir($dir_handle)){
                if($file!="." && $file!=".."){



                    if(is_link($source."/".$file)) {
                        if(is_callable($doOnSymLink) || is_array($doOnSymLink)) {
                            call_user_func_array(
                                $doOnSymLink,
                                array($source."/".$file)
                            );
                        }
                        if(!$copySymlink) {
                            continue;
                        }
                    }


                    if(is_dir($source."/".$file)){
                        static::rcopy($source."/".$file, $destinationPath, true, $copySymlink, $doOnSymLink);
                    } else {

                        echo $source."/".$file."\t => \t".$destinationPath."/".$file;
                        echo "\n";

                        copy($source."/".$file, $destinationPath."/".$file);
                    }
                }
            }
            closedir($dir_handle);
        } else {
            // can also handle simple copy commands
            copy($source, $dest);
        }
    }



    public static function doOnPathes($path, $callback, $recursive = false)
    {

        $currentDir = getcwd();

        $path = realpath($path);

        chdir($path);

        $dir = opendir($path);
        while($entry = readdir($dir)) {
            if($entry != '.' && $entry != '..' && is_dir($path.'/'.$entry)) {
                if(is_dir($path.'/'.$entry)) {
                    chdir($path.'/'.$entry);
                    $returnValue = $callback($path.'/'.$entry);
                    if(!$returnValue) {
                        return;
                    }

                    if($recursive) {
                        static::doOnPathes($path.'/'.$entry, $callback, $recursive);
                    }
                }
            }
        }
        chdir($currentDir);
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


