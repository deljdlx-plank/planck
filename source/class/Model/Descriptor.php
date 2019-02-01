<?php

namespace Planck\Model;


class Descriptor
{

    /**
     * @var Repository
     */
    protected $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
        $this->fields = $this->repository->describe();
    }


    public function getFields() {
        $fields = array();
        foreach ($this->fields as $field) {
            $fields[] = $field['name'];
        }

        return $fields;

    }

    public function getDescriptors()
    {
        $descriptors = array();
        foreach ($this->fields as $fieldDescriptor) {
            $descriptor = new FieldDescriptor($fieldDescriptor);
            $fieldName = $fieldDescriptor['name'];
            $descriptors[$fieldName] = $descriptor;
        }


        return $descriptors;
    }


    public function getFieldDescriptor($fieldName)
    {

    }


}
