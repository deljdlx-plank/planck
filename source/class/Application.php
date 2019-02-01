<?php


namespace Planck;
use Phi\HTTP\Header;
use Phi\Routing\Request;
use Planck\Model\Model;
use Planck\DataLayer;
use Planck\Model\Repository;
use Planck\State\Application\Execution;
use Planck\Traits\HasAspect;

class Application extends \Phi\Application\Application
{

    use HasAspect;


    const DEFAULT_MODULE_FILEPATH = 'source/class/Module';

    const DEFAULT_ROUTER_FOLDER_NAME = 'Router';


    const EVENT_BEFORE_LOADING_MODULE = 'EVENT_BEFORE_LOADING_MODULE';
    const EVENT_AFTER_LOADING_MODULE = 'EVENT_AFTER_LOADING_MODULE';

    const STATUS_404 = 404;


    const STATE_EXECUTION_NAME = Execution::class;


    /**
     * @var Model
     */
    protected $model;


    /**
     * @var ApplicationState[]
     */
    protected $states;


    /**
     * @var Extension[]
     */
    protected $extensions =array();





    public function __construct($path = null, $instanceName = null, $autobuild = true)
    {
        $path = realpath($path);

        if(!$path) {
            throw new Exception('Application can not be initialized. Path '.$path.' does not exists');
        }

        parent::__construct(realpath($path), $instanceName, $autobuild);


        $this->states[static::STATE_EXECUTION_NAME] = new Execution($this);



        $this->addEventListener(self::EVENT_RUN_BEFORE_ROUTING, array($this, 'doBeforeRouting'));
        $this->addEventListener(self::EVENT_RUN_AFTER_ROUTING, array($this, 'doAfterRouting'));
        $this->addEventListener(self::EVENT_NO_RESPONSE, array($this, 'doOnNoResponse'));

        $this->addEventListener(self::EVENT_SUCCESS, array($this, 'doOnSuccess'));



    }


    public function getStatus()
    {
        return $this->states[static::STATE_EXECUTION_NAME];
    }






    //=======================================================

    public function addExtension(Extension $extension, $urlPattern = '')
    {
        $this->extensions[$extension->getName()] = $extension;


        $extension->setURLPattern($urlPattern);

        $extension->setApplication($this);


        foreach ($extension->getModules() as $module) {

            $routers = $module->getRouters();
            foreach ($routers as $router) {

                $router->setApplication($this);

                if($urlPattern !== '') {
                    $router->addValidator(function ($request) use ($urlPattern) {

                        if (strpos($request->getURI(), $urlPattern) !== false) {
                            return true;
                        }
                        return false;
                    });
                }
                $this->addRouter($router, get_class($router));
            }
        }


        return $this;
    }

    public function getExtension($extensionName)
    {
        if(array_key_exists($extensionName, $this->extensions)) {
            return $this->extensions[$extensionName];
        }

        throw new Exception('Extension '.$extensionName.' does not exists');
    }

    public function getExtensions()
    {
        return $this->extensions;
    }


    //=======================================================

    /**
     * @return Route[]
     */
    public function getRoutes()
    {
        $routes = array();

        foreach ($this->extensions as $extension) {
            $extensionRoutes = $extension->getRoutes();
            foreach ($extensionRoutes as $routeName => $route) {
                $key = '/'.$routeName;
                $routes[$key] = $route;
            }


        }
        return $routes;

    }


    /**
     * @param $fingerPrint
     * @return Route|bool
     */
    public function getRouteByFingerPrint($fingerPrint)
    {
        $routes = $this->getRoutes();


        foreach ($routes as $key => $route) {
            if($key == $fingerPrint) {
                return $route;
            }
        }

        return false;

    }



    //=======================================================


    public function doOnSuccess($event)
    {
        $this->getStatus()->ok(true);
    }

    public function doOnNoResponse($event)
    {

        $this->getStatus()->notFound(true);
    }




    public function doBeforeRouting($event)
    {


    }


    public function doAfterRouting($event)
    {

    }


    //=======================================================
    public function setModel(Model $model)
    {
        $this->model = $model;
        return $this;
    }

    public function getModel()
    {
        return $this->model;
    }
    //=======================================================



    public function getModelEntity($entityName)
    {
        return $this->get('model')->getEntity($entityName);
    }

    /**
     * @param $repositoryName
     * @return Repository
     */
    public function getModelRepository($repositoryName)
    {
        return $this->get('model')->getRepository($repositoryName);
    }

    public function getModelInstanceByFingerPrint($fingerPrint)
    {
        return $this->getModel()->getInstanceByFingerPrint($fingerPrint);
    }



    public function getUser($cast = null)
    {
        if($this->hasAspect('user')) {
            return $this->getAspect('user')->getCurrentUser($cast);
        }

        return false;
    }

    public function setUser($user)
    {
        if($this->hasAspect('user')) {
            $this->getAspect('user')->setCurrentUser($user);
        }
        return $this;
    }




    public function run(Request $request = null, array $variables = array(), $flush = false)
    {
        $variables['application'] = $this;
        $returnValue = parent::run($request, $variables, $flush);
        return $returnValue;

    }





    public function setDefaultRouter(\Phi\Routing\Router $router = null)
    {

        if($router === null) {

            $this->routers[static::DEFAULT_ROUTER_NAME] = new Router($this);
        }
        else {
            $this->routers[static::DEFAULT_ROUTER_NAME] = $router;
        }

        return $this;
    }


    protected function initialize()
    {
        $this->session = new \Planck\Session();
    }


}
