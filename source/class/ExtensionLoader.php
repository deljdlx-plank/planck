<?php

namespace Planck;

use Phi\Core\Autoloader;
use Planck\Traits\IsApplicationObject;

class ExtensionLoader
{
    use IsApplicationObject;

    protected $extentionFilepath;


    /**
     * @var Autoloader
     */
    protected $autoloader;


    /**
     * @var Extension[]
     */
    protected $loadedExtensions = [];



    public function __construct(Autoloader $autoloader = null)
    {
        if($autoloader === null)
        {
            $autoloader = new Autoloader();
        }
        $this->autoloader = $autoloader;
        $this->autoloader->register();
    }



    public function setExtensionFilepath($path)
    {
        $this->extentionFilepath = $path;
        return $this;
    }

    public function isExtensionLoaded($extensionName)
    {
        if(array_key_exists($extensionName, $this->loadedExtensions)) {
            return true;
        }
        return false;
    }


    public function getExtension($extensionName)
    {
        if(!array_key_exists($extensionName, $this->loadedExtensions)) {
            throw new Exception('Extension '.$extensionName.' is not loaded');
        }
        return $this->loadedExtensions[$extensionName];
    }


    public function loadExtension($extensionName, $register = false, $pattern = '')
    {
        if(array_key_exists($extensionName, $this->loadedExtensions)) {
            throw new Exception('Extension '.$extensionName.' already loaded');
        }

        $baseName = basename($extensionName);
        $extensionPath = $this->extentionFilepath.'/'.$baseName;

        if(!is_dir($extensionPath)) {
            throw new Exception('Can not load extension '.$extensionName.'. Path '.$extensionPath.' does not exists');
        }



        $this->autoloader->addNamespace($extensionName, $extensionPath.'/source/class');

        $extension = new $extensionName(
            $this->getApplication()
        );



        $this->loadedExtensions[$extensionName] = $extension;

        if($register) {
            $this->getApplication()->addExtension($extension, $pattern);
        }

        return $extension;

    }


}
