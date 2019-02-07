<?php

namespace Planck;

use Planck\Helper\File;
use Planck\Traits\HasLocalResource;
use Planck\Traits\IsApplicationObject;

class Extension
{

    use IsApplicationObject;
    use HasLocalResource;

    protected $namespace;
    protected $path;

    protected $autoloader;


    protected $frontPackages = [];


    /**
     * @var Module[]
     */
    protected $modules = [];

    /**
     * @var Application
     */
    //protected $application;



    protected $urlPattern;


    public function __construct(Application $application, $namespace, $path)
    {

        $this->namespace = $namespace;
        $this->path = realpath($path);

        if(!is_dir($this->path)) {
            throw new Exception('Extension "'.$path.'"" path does not exist');
        }

        $this->sourcePath = $this->path.'/source/class';


        $this->autoloader = new \Phi\Core\Autoloader();
        $this->autoloader->addNamespace($this->namespace, $this->sourcePath);
        $this->autoloader->register();

        $this->setApplication($application);

        $this->loadAspects($application);
        $this->loadModules();
    }

    public function getFilepath()
    {
        return $this->path;
    }

    public function getAssetsFilepath()
    {
        return realpath($this->getFilepath().'/asset');
    }


    public function getJavascriptsFilepath()
    {
        return realpath($this->getAssetsFilepath().'/javascript');
    }





    public function getName()
    {
        return $this->namespace;
    }

    public function getBaseName()
    {
        return basename(str_replace('\\', '/', get_class($this)));
    }


    /**
     * @return Route[]
     */
    public function getRoutes()
    {
        $routes = [];
        foreach ($this->getModules() as $module) {
            $moduleRoutes = $module->getRoutes();
            $routes = array_merge($routes, $moduleRoutes);
        }
        return $routes;
    }



    public function setURLPattern($pattern)
    {
        $this->urlPattern = $pattern;
        return $this;
    }


    /**
     * @return $this
     */
    public function loadAspects($application)
    {
        $aspectFilepath = $this->sourcePath.'/Aspect';


        if(!is_dir($aspectFilepath)) {
            return $this;
        }

        $aspects = glob($aspectFilepath.'/*');


        foreach ($aspects as $path) {


            $aspectName = str_replace('.php', '', basename($path));
            $className = $this->namespace.'\Aspect\\'.$aspectName;


            $aspect = new $className($application);


            $application->addAspect($aspect, $aspect->getName());

            $this->aspects[$aspect->getName()] = $aspect;


        }

        return $this;
    }




    /**
     * @return $this
     */
    public function loadModules()
    {
        $moduleFilepath = $this->sourcePath.'/Module';

        $modules = glob($moduleFilepath.'/*');

        foreach ($modules as $path) {

            $moduleName = basename($path);
            $namespace = $this->namespace.'\Module\\'.$moduleName;



            $module = new Module($this->application, $namespace, $this, $path);
            $this->modules[$moduleName] = $module;
        }

        return $this;
    }

    /**
     * @return Module[]
     */
    public function getModules()
    {
        return $this->modules;
    }


    /**
     * @param $moduleName
     * @return Module
     * @throws Exception
     */
    public function getModule($moduleName)
    {
        if(array_key_exists($moduleName, $this->modules)) {
            return $this->modules[$moduleName];
        }

        throw new Exception('Module '.$moduleName.' does not exists');
    }


    public function buildURL($moduleName, $routerName, $routeName, $parameters = array())
    {

        return $this->urlPattern.
            $this->getModule($moduleName)
            ->getRouter($routerName)
            ->getRouteByName($routeName)
            ->buildURL($parameters)
        ;
    }


    public function getAssets()
    {

        $assets = [];

        $assetPath = $this->path.'/asset';

        /*
        if(is_file($assetPath.'/javascript/Extension.js')) {
            $script = $this->getLocalJavascriptFile($assetPath.'/javascript/Extension.js');
            $assets[] = $script;
        }
        */

        $javascripts = glob($assetPath.'/javascript/*.js');
        foreach ($javascripts as $javascript) {
            $script = $this->getLocalJavascriptFile($javascript);
            $assets[] = $script;
        }



        $javascripts = File::rglob($assetPath.'/javascript/class/*.js');
        foreach ($javascripts as $javascript) {
            $script = $this->getLocalJavascriptFile($javascript);
            $assets[] = $script;
        }



        $css = File::rglob($assetPath.'/css/*.css');
        foreach ($css as $cssPath) {
            $cssFile = $this->getLocalCSSFile($cssPath);
            $assets[] = $cssFile;
        }


        return $assets;


    }


    public function addFrontPackage($package)
    {
        $key = get_class($package);
        $this->frontPackages[$key] = $package;
        return $this;
    }









}

