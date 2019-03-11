<?php

namespace Planck\Traits;


use Planck\Exception;
use Planck\Exception\DoesNotExist;

trait IsExtensionObject
{
    use IsApplicationObject;


    private $extensionName;
    private $moduleName;

    private $module;
    private $extension;


    public function getModuleName()
    {

        $className = get_class($this);

        if(!preg_match('`.*?\\\\Extension\\\\.+?\\\\Module\\\\(.*?)\\\\`', $className)) {
            throw new Exception('Can not extract module name (Component name : "'.$className.'"');
        }

        if(!$this->moduleName) {
            $this->moduleName = preg_replace('`.*?\\\\Extension\\\\.+?\\\\Module\\\\(.*?)\\\\.*`', '$1', $className);
        }
        return $this->moduleName;
    }

    public function getExtensionName()
    {

        $className = get_class($this);

        if(!preg_match('`(.*?\\\\Extension\\\\.+?)\\\\`', $className)) {
            throw new Exception('Can not extract extension name (Component name : "'.$className.'"');
        }

        if(!$this->extensionName) {
            $this->extensionName = preg_replace('`(.*?\\\\Extension\\\\.+?)\\\\.*`', '$1', $className);
        }
        return $this->extensionName;
    }



    public function buildURL($routerName, $routeName, $parameters = array())
    {
        return  $this->getExtension()->buildURL(
            $this->getModuleName(),
            $routerName,
            $routeName,
            $parameters
        );
    }

    public function hasExtension()
    {
        try {
            $this->getApplication()->getExtension(
                $this->getExtensionName()
            );
            return true;
        }
        catch(DoesNotExist $exception) {
            return false;
        }
    }

    public function getExtension()
    {
        if(!$this->extension) {
            $this->extension = $this->getApplication()->getExtension(
                $this->getExtensionName()
            );
        }
        return $this->extension;
    }

    public function getModule()
    {
        if(!$this->module) {
            $extension = $this->getExtension();

            $this->module = $extension->getModule(
                $this->getModuleName()
            );
        }

        return $this->module;

    }

}

