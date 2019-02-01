<?php


namespace Planck;

class Container extends \Phi\Container\Container
{

    public function __construct()
    {
        if(method_exists($this, 'initialize')) {
            $this->initialize();
        }
    }


}