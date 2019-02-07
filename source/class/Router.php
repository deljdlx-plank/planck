<?php

namespace Planck;

use Phi\Routing\Route;
use Planck\Traits\HasLocalResource;
use Planck\Traits\IsExtensionObject;


class Router extends \Phi\Routing\Router
{


    use IsExtensionObject;
    use HasLocalResource;

    protected $application;

    public function __construct(Application $application = null)
    {

        if($application) {


            $this->setApplication($application);
        }
        else {
            $this->application = $this->getApplication();
        }

        parent::__construct();

    }



    /**
     * @param Route $route
     * @param $name
     * @return $this
     */
    public function addRoute(Route $route, $name = null)
    {


        $planckRoute = new \Planck\Route();
        $planckRoute->loadFromRoute($route);
        $planckRoute->setApplication($this->application);

        $route = parent::addRoute($planckRoute, $name);


        $route->doBefore(function($route) {

            return true;

            $application = $this->getApplication();

            if($application->exists('user')) {
                if ($application->get('user')->getId()) {
                    return true;
                }
            }
            return false;
        });

        return $route;
    }

    public function addPublicRoute(Route $route, $name = null)
    {

        $planckRoute = new \Planck\Route();
        $planckRoute->loadFromRoute($route);
        $planckRoute->setApplication($this->application);

        return parent::addRoute($planckRoute, $name);
    }

    public function publicGet($name, $validator, $callback, $headers = array())
    {
        return $this->addPublicRoute(
            new Route('get', $validator, $callback, $headers, $name),
            $name
        );
    }

    /**
     * @param $name
     * @param $validator
     * @param $callback
     * @param array $headers
     * @return Router
     */
    public function publicPost($name, $validator, $callback, $headers = array())
    {
        return $this->addPublicRoute(
            new Route('post', $validator, $callback, $headers, $name),
            $name
        );
    }


    /**
     * @return array
     */
    public function getAssets()
    {

        $extensions = $this->getApplication()->getExtensions();
        $assets = [];

        foreach ($extensions as $extension) {
            $extensionAssets = $extension->getAssets();
            if(!empty($extensionAssets)) {
                $assets = array_merge($assets, $extensionAssets);
            }
        }

        /*
        $assets = array_merge(
            $assets,
            $this->getModule()->getAssets(false)
        );
        */

        return $assets;
    }






}

