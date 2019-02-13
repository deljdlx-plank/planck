<?php


namespace Planck\Traits;

trait HasAspect
{



    /**
     * @var Aspect[]
     */
    protected $aspects = array();


    //=======================================================

    public function addAspect(\Planck\Aspect $aspect, $alias = null)
    {
        if($alias === null) {
            $alias = get_class($aspect);
        }
        $this->aspects[$alias] = $aspect;
        return $this;
    }

    /**
     * @param $name
     * @return Aspect
     * @throws Exception
     */
    public function getAspect($name)
    {
        if(array_key_exists($name, $this->aspects)) {
            return $this->aspects[$name];
        }
        else {
            throw new Exception('No aspect with name '.$name);
        }
    }

    public function hasAspect($aspectName)
    {
        if(array_key_exists($aspectName, $this->aspects)) {
            return true;
        }
        return false;
    }
}
