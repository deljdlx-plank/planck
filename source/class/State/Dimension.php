<?php

namespace Planck\State;


class Dimension
{


    private $name;

    private $descriptor;


    public function __construct($name)
    {
        $this->name = $name;
    }


    public function getName()
    {
        return $this->name;
    }



    public function getDescriptor()
    {
        return $this->descriptor;
    }





}



