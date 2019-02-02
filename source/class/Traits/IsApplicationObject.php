<?php


namespace Planck\Traits;


use Planck\Application;
use Planck\ApplicationRegistry;

trait IsApplicationObject
{

    /**
     * @var Application
     */
    protected $application;





    /**
     * @return Application
     */
    public static function getDefaultApplication()
    {
        return ApplicationRegistry::getInstance();
        return Application::getInstance();
    }


    public function _initializeTraitIsApplicationObject()
    {
        $this->application = $this->getApplication();
    }


    public function setApplication(Application $application)
    {
        $this->application = $application;
        return $this;
    }

    /**
     * @param null $name
     * @return Application
     */
    public function getApplication()
    {
        if(!$this->application) {

            $this->application = self::getDefaultApplication();
        }
        return $this->application;
    }

    public function getFromContainer($name)
    {
        return $this->getApplication()->get($name);
    }

}