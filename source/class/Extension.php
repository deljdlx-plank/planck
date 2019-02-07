<?php

namespace Planck;

use Phi\HTML\CSSFile;
use Phi\HTML\JavascriptFile;
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

    public function getAssetsFilepath($normalize = true)
    {
        $path = realpath($this->getFilepath().'/asset');
        if(!$normalize) {
            return $path;
        }
        else {
            return str_replace('\\', '/', $path);
        }
    }


    public function getJavascriptsFilepath()
    {
        return $this->getAssetsFilepath().'/javascript';
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


        $javascripts = glob($assetPath.'/javascript/*.js');
        foreach ($javascripts as $javascript) {


            $javascript = File::normalize($javascript);

            $javascriptBasename = str_replace($this->getAssetsFilepath(), '', $javascript);

            $script = $this->getExtensionJavascript($javascriptBasename);
            $assets[] = $script;
        }


        $javascripts = File::rglob($assetPath.'/javascript/class/*.js');
        foreach ($javascripts as $javascript) {
            $javascriptBasename = str_replace($this->getAssetsFilepath(), '', $javascript);
            $script = $this->getExtensionJavascript($javascriptBasename);
            $assets[] = $script;
        }



        $css = File::rglob($assetPath.'/css/*.css');
        foreach ($css as $cssPath) {
            $cssBasename = str_replace($this->getAssetsFilepath(), '', $cssPath);
            $cssFile = $this->getExtensionCSS($cssBasename);
            $assets[] = $cssFile;
        }
        return $assets;
    }



    public function getExtensionCSS($css)
    {

        $loaderURL = $this->getFromContainer('extension-css-loader-url' );

        $url = $loaderURL.'&css='.$css.'&extension='.rawurlencode($this->getName());

        $data = array(
            'data-name' => $css
        );

        $data = null;

        $cssInstance = new CSSFile($url, $data);

        $cssInstance->setKey($css);
        return $cssInstance;
    }



    public function getExtensionJavascript($javascript)
    {

        $loaderURL = $this->getFromContainer('extension-javascript-loader-url' );

        $url = $loaderURL.'&javascript='.$javascript.'&extension='.rawurlencode($this->getName());

        $data = array(
            'data-name' => $javascript
        );

        $data = null;

        $javascriptInstance = new JavascriptFile($url, $data);

        $javascriptInstance->setKey($javascript);
        return $javascriptInstance;
    }







}

