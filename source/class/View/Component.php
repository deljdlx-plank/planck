<?php

namespace Planck\View;

use Phi\Event\Traits\Listenable;
use Planck\Application;
use Planck\Exception;
use Planck\Model\Entity;
use Planck\Traits\HasLocalResource;
use Planck\Traits\IsExtensionObject;


class Component extends \Phi\HTML\Component
{


    use IsExtensionObject;
    use Listenable;
    use HasLocalResource;


    const EVENT_CONSTRUCT = 'event_contruct';



    protected $manager;

    protected $frontPackages = [];



    public function __construct($tagName = '')
    {

        parent::__construct($tagName);

        $this->application = $this->getApplication();




        $this->initialize();


        if($this->getApplication()->exists('view-component-manager')) {
            $this->manager = $this->getApplication()->get('view-component-manager');
        }
        else {
            $this->manager = $this->getDefaultComponentManager();
        }

        $this->manager->registerComponent($this);







        $eventName = get_class($this).':'.static::EVENT_CONSTRUCT;

        $this->fireEvent(
            $eventName,
            array(
                'component' => $this
            )
        );
    }




    public function initialize()
    {

    }



    public function setVariable($name, $value)
    {
        parent::setVariable($name, $value);
        return $this;
    }




    public function loadDataFromJSON($data)
    {
        foreach ($data as $name => $values) {
            if(isset($values['metadata'])) {
                if(isset($values['metadata']['className'])) {
                    if(is_subclass_of($values['metadata']['className'], Entity::class)) {
                        $this->loadEntityFromJSON($name, $values);
                    }
                }
            }
        }
        //print_r($data);
    }

    public function loadEntityFromJSON($name, $data)
    {
        $metadata = $data['metadata'];

        if(isset($metadata['className'])) {
            $className = $metadata['className'];


            if(!class_exists($className)) {
                throw new Exception('Class name'.$className.' does not exists');
            }
        }



        $dataEntry = $data['data'];


        if(isset($dataEntry['metadata'])) {

            if(isset($dataEntry['metadata']['fingerprint'])) {

                $instance = $this->getApplication()->getModelInstanceByFingerPrint($dataEntry['metadata']['fingerprint']);
            }

            $this->setVariable($name, $instance);
        }

        return $this;


    }





    public function i18n($string, $key = null, $package = 'main', $localization = null)
    {
        return $this->application->get('i18n-engine')->localize($string, $key, $package, $localization);
    }


    public function html($buffer, $parse = false)
    {
        $this->dom->html($buffer, $parse);
        return $this;
    }


    protected function getDefaultComponentManager()
    {
        return new ComponentManager();
    }
}


