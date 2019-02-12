<?php

namespace Planck\Traits;


use Planck\Model\Exception\ClassDoesNotExist;
use Planck\Pattern\Decorator;

trait Decorable
{

    /**
     * @param $decoratorClassName
     * @return Decorator
     */
    public function decorate($decoratorClassName)
    {
        if(!class_exists($decoratorClassName)) {
            throw new ClassDoesNotExist($decoratorClassName);
        }
        $decorator = new $decoratorClassName($this);
        return $decorator;
    }


    public function isDecorated($decoratorClassName)
    {
        if($this instanceof $decoratorClassName) {
            return true;
        }
        return false;
    }


}


