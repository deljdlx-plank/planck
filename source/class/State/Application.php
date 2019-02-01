<?php

namespace Planck\State;



class Application extends \Planck\State
{

    /**
     * @var Application
     */
    protected $application;


    public function __construct(\Planck\Application $application)
    {
        $this->application = $application;
    }


}



