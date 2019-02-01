<?php
namespace Planck\State\Application;

use Planck\Application;
use PlanckState\ApplicationState;
use PlanckState\Dimension;

class Execution extends \Planck\State\Application
{

    public function __construct(Application $application)
    {
        parent::__construct($application);

        /*
        $this->addDimension(
            new Dimension('step')
        );

        $this->addDimension(
            new Dimension('status')
        );

        $this->addDimension(
            new Dimension('isLogged')
        );

        $this->addDimension(
            new Dimension('user')
        );


        $this->addDimension(
            new Dimension('content')
        );



        $this->addDimension(
            new Dimension('routes')
        );

        $this->addDimension(
            new Dimension('errors')
        );
        */



    }

}