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

        return $route;
    }








}

