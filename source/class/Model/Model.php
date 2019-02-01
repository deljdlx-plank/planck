<?php
namespace Planck\Model;

use Phi\Database\Source;
use Planck\Application;
use Planck\Exception;

class Model
{

    const DEFAULT_SOURCE_NAME = 'main';

    /**
     * @var Source[]
     */
    protected $sources = [];

    public function __construct()
    {

    }


    public function addSource(Source $source, $name = null)
    {
        if($name === null ) {
            $name = static::DEFAULT_SOURCE_NAME;
        }
        $this->sources[$name] = $source;
        return $this;
    }

    public function getSource($name = null) {
        if($name === null ) {
            $name = static::DEFAULT_SOURCE_NAME;
        }

        if(array_key_exists($name, $this->sources)) {
            return $this->sources[$name];
        }
        else {
            throw new Exception('Datasource '.$name.' does not exists');
        }
    }


    /**
     * @param $entityName
     * @param null $sourceName
     * @return Entity
     * @throws Exception
     */
    public function getEntity($entityName, $sourceName = null)
    {

        $source = $this->getSource($sourceName);

        if(class_exists($entityName)) {
            $repositoryClassName = str_replace('\Entity\\', '\Repository\\', $entityName);
            $repository = new $repositoryClassName($source);
            $instance = new $entityName($repository);
        }
        else {
            throw new Exception('Model entity '.$entityName.' does not exists');
        }

        return $instance;
    }


    public function getRepository($repositoryName, $sourceName = null)
    {

        $source = $this->getSource($sourceName);

        if(class_exists($repositoryName)) {
            $repository = new $repositoryName($source);
            return $repository;
        }
        else {
            throw new Exception('Model repository '.$repositoryName.' does not exists');
        }

        return $instance;
    }


    /**
     * @param $fingerPrint
     * @return Entity|Repository
     */
    public function getInstanceByFingerPrint($fingerPrint)
    {
        if(is_string($fingerPrint)) {
            $data = json_decode($fingerPrint);
        }
        else {
            $data = $fingerPrint;
        }

        if($data->type == 'repository') {
            return $this->getRepository($data->instance);
        }
        else if($data->type == 'entity') {
            $instance = $this->getEntity($data->instance);
            if($data->id) {
                $instance->loadById($data->id);
            }
            return $instance;
        }

    }




}