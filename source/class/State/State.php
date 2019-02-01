<?php

namespace Planck;


use Phi\Event\Event;
use Planck\Traits\Listenable;

class State
{

    use Listenable;


    /**
     * @var Dimension[]
     */
    protected $dimensions;


    /**
     * @var DimensionValue[]
     */
    private $values = array();



    public function addDimension(Dimension $dimension)
    {
        $this->dimensions[$dimension->getName()] = $dimension;
        return $this;
    }

    public function getDimensions()
    {
        return $this->dimensions;
    }



    public function getValue($name)
    {
        return $this->getValueObject($name)->getValue();
    }


    public function getValueObject($name)
    {
        $dimension = $this->getDimension($name);

        if(array_key_exists($name, $this->values)) {
            return $this->values[$dimension->getName()];
        }
        else {

            $dimension = $this->getDimension($name);
            $valueObject= new DimensionValue(
                $dimension,
                null
            );

            $self = $this;
            $valueObject->addEventListener(
                get_class($valueObject).'.'.DimensionValue::EVENT_CHANGE,
                function($event) use ($self) {
                    $self->relayEvent($event);
                });

            $this->values[$dimension->getName()] = $valueObject;

            return $this->values[$dimension->getName()];
        }
    }



    protected function relayEvent(Event $event)
    {
        $event->setVariable('state', $this);
        $this->fireEvent($event);
        return $this;
    }

    public function getValues()
    {
        return $this->values;
    }

    public function getDimension($name)
    {
        if(array_key_exists($name, $this->dimensions)) {
            return $this->dimensions[$name];
        }

        throw new Exception('Dimension '.$name.' does not exist');
    }


    public function setValue($dimentionName, $value)
    {

        $valueObject = $this->getValueObject($dimentionName);
        $valueObject->setValue($value);



        $this->values[$valueObject->getDimension()->getName()] = $valueObject;
        return $this;
    }



}



