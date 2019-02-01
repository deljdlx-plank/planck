<?php
namespace Planck\State\Application;

use Planck\Application;
use PlanckState\ApplicationState;
use PlanckState\Dimension;

class Data extends \Planck\State\Application
{

    public function __construct(Application $application)
    {
        parent::__construct($application);

        /*
        $this->addDimension(
            new Dimension('project')
        );

        $this->addDimension(
            new Dimension('user')
        );
        */



    }

}