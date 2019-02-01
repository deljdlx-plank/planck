<?php

namespace Planck\Model\Traits;

use Planck\Model\GenericObjectProperty;
use Planck\Exception\EntityPropertyDoesNotExist;
Trait HasProperties
{



    /**
     * @var GenericObjectProperty[]
     */
    protected $properties = array();



    public function hasProperty($propertyName)
    {
        $property = null;

        if(array_key_exists($propertyName, $this->properties)) {
            return true;
        }


        if(method_exists($this, 'getAncestors')) {

            $ancestors = $this->getAncestors();
            foreach ($ancestors as $ancestor) {
                if ($ancestor->hasProperty($propertyName)) {
                    return true;
                }
            }
        }

        return false;


    }

    public function getProperty($propertyName)
    {

        $property = null;

        if(array_key_exists($propertyName, $this->properties)) {
            $property = $this->properties[$propertyName];

            if($this->properties[$propertyName]->hasValue()) {
                return $this->properties[$propertyName];
            }
        }


        if(method_exists($this, 'getAncestors')) {



            $ancestors = $this->getAncestors();

            foreach ($ancestors as $ancestor) {
                try {
                    $ancestorProperty = $ancestor->getProperty($propertyName);
                    if(!$property) {
                        $property = $ancestorProperty;
                    }

                    if($ancestorProperty->hasValue()) {
                        $property->setValue(
                            $ancestorProperty->getValue()
                        );
                    }
                    $property->isInherited(true);
                    return $property;
                }
                catch(EntityPropertyDoesNotExist $e) {

                }
            }
        }


        if($property) {
            return $property;
        }

        throw new EntityPropertyDoesNotExist('No property with name '.$propertyName);
    }


    public function addProperty(GenericObjectProperty $property)
    {
        $this->properties[$property->name] = $property;
    }


    /**
     * @return GenericObjectProperty[]
     */
    public function getProperties($autoload = false)
    {
        if($autoload) {
            foreach ($this->properties as $propertyName => $property) {
                $this->getProperty($propertyName);
            }
        }
        return $this->properties;
    }


    public function getOwnProperties($autoload = false)
    {
        $ownProperties = array();
        $properties = $this->getProperties($autoload);
        foreach ($properties as $property) {
            if(!$property->isInherited()) {
                $ownProperties[] = $property;
            }
        }

        return $ownProperties;


    }



    public function loadPropertiesFromJson($json, $flush =false)
    {
        if($flush) {
            $this->properties = [];
        }

        $objects = json_decode($json, true);

        if(!empty($objects)) {
            foreach ($objects as $object) {
                $property = new GenericObjectProperty($object);
                $this->addProperty($property);
            }
        }
        return $this;

    }


    public function _jsonSerializeTraitHasProperties()
    {

        $values = array();
        $values['properties'] =  $this->properties;
        return $values;

    }




    public function getValue($name)
    {


        if($name == 'properties') {
            return json_encode($this->properties);
        }
        else {
            return parent::getValue($name);
        }
    }

    public function getValues()
    {
        $values = parent::getValues();

        $values['properties'] = json_encode($this->properties);
        return $values;

        //$this->setValue('properties', json_encode($this->properties));
        //return parent::getValues();
    }



    public function setValue($name, $value)
    {

        if($name == 'properties') {
            if(!is_string($value)) {
                $value = json_encode($value);
            }
            $this->loadPropertiesFromJson($value);
        }

        parent::setValue($name, $value);


        return $this;
    }



}