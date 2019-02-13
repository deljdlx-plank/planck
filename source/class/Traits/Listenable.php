<?php
namespace Planck\Traits;


use Phi\Traits\Introspectable;
use Planck\Application\Application;

Trait Listenable
{

    /**
     * @var Application
     */
    protected $application;

    use \Phi\Event\Traits\Listenable;
    use Introspectable;


    protected function loadListeners()
    {

        $this->application = Application::getInstance();

        foreach ($this->getParentClasses() as $className) {
            $injection = 'Listener:'.$className;

            if($this->application->exists($injection)) {
                $listeners = $this->application->get($injection);
                foreach ($listeners as $eventName => $listener) {
                    $this->addEventListener($eventName, $listener);
                }
            }
        }

        $injection = 'Listener:'.get_class($this);


        if($this->application->exists($injection)) {

            $listeners = $this->application->get($injection);
            foreach ($listeners as $eventName => $listener) {
                $this->addEventListener($eventName, $listener);
            }
        }
        else {

        }
    }
}



