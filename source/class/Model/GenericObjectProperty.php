<?php


namespace Planck\Model;


use Planck\Exception;

class GenericObjectProperty implements \JsonSerializable
{


    public $name;
    public $type;
    public $defaultValue;
    public $value;

    public $visibility;
    public $isInherited;

    public $extraValues =array();



    public function __construct($valueObject = null)
    {

        //$this->extraValues = new \stdClass;

        if(!empty($valueObject)) {
            foreach ($valueObject as $name => $value) {
                $this->$name = $value;
            }
        }
    }


    public function getName()
    {
        return $this->name;
    }


    public function isInherited($value =null)
    {
        if($value === null) {
            return $this->isInherited;
        }
        else {
            $this->isInherited = $value;
        }
        return $this;
    }

    public function hasValue()
    {
        if($this->value === null) {
            return false;
        }
        return true;
    }



    public function getValue()
    {

        if($this->value !== null) {
            return $this->value;
        }
        else {
            return $this->defaultValue;
        }

    }


    public function val($value = null)
    {
        if($value === null) {
            return $this->getValue();
        }
        else {
            $this->setValue($value);
        }
        return $this;
    }


    public function setValue($value = null)
    {
        if($this->type == 'bool' && $value !== null) {
            $value =  (bool) $value;
        }
        $this->isInherited(false);
        $this->value = $value;
        return $this;
    }

    public function setExtraValue($name, $value)
    {
        $this->extraValues[$name] = $value;
        return $this;
    }

    public function getExtraValue($name)
    {
        if(isset($this->extraValues[$name])) {
            return $this->extraValues[$name];
        }

        return null;
    }


    public function addValue($value, $key = null)
    {
        if($this->type == 'array') {
            if($key === null ) {
                $this->value[] = $value;
            }
            else {
                $this->value[$key] = $value;
            }

            return $this;
        }
        else {
            throw new Exception('Cannot add value. Property '.$this->name.' is of array type');
        }
    }

    public function jsonSerialize()
    {
        if($this->type === 'bool' && $this->value !== null) {
            $value = (bool) $this->value;
        }
        else {
            $value =$this->value;
        }


        if($this->isInherited && 0) {
            return array();
        }
        else {
            $data = array(
                'name' => $this->name,
                'type' => $this->type,
                'defaultValue'=> $this->defaultValue,
                'value'=> $value,
                'visibility' => $this->visibility,
                'isInherited' => $this->isInherited,
                'extraValues' => $this->extraValues,
            );
        }

        return $data;

    }

    public function __toString()
    {
        return (string) $this->getValue();
    }


}

