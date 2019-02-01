<?php

namespace Planck;

use Phi\Application\Application;
use Planck\Traits\IsExtensionObject;

class Controller extends \Phi\Application\Controller
{

    use IsExtensionObject;


    public function __construct(Application $application = null)
    {
        parent::__construct($application);
        $this->initialize();
    }


    public function initialize()
    {

    }

}