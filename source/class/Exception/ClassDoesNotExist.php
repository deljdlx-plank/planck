<?php

namespace Planck\Exception;




class ClassDoesNotExist extends DoesNotExist
{

    public function __construct($className)
    {
        parent::__construct('Class "'.$className.'" does not exist');
    }

}
