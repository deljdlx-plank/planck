<?php

namespace Planck;



use Phi\Traits\Collection;
use Planck\Exception\DoesNotExist;

class Runtime
{

    use Collection;


    protected static $instance;

    protected $autoloader;


    /**
     * @return static
     */
    public static function getInstance()
    {

        if(!static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public function instanciate($className)
    {
        if(!class_exists($className)) {
            throw new DoesNotExist('Can not instanciate "'.$className.'", class not found');
        }
    }


    protected function __construct()
    {

    }


}


