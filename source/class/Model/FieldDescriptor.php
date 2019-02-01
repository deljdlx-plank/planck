<?php

namespace Planck\Model;

class FieldDescriptor
{


    protected $defaultFieldDescriptor =array(
        'cid' => null,
        'name' => null,
        'type' => null,
        'notnull' => null,
        'dflt_value' => null,
        'pk' => null,

        '@isCaption' => true,
    );

    public function __construct($fieldDescriptor)
    {

        $fieldDescriptor = $this->decorate($fieldDescriptor);
        $this->fieldDescriptor = array_merge($this->defaultFieldDescriptor, $fieldDescriptor);
    }

    public function getCaption()
    {
        return $this->fieldDescriptor['name'];
    }

    public function isCaption($value = null)
    {
        if($value!==null) {
            $this->fieldDescriptor['@isCaption'] = $value;
        }
        return $this->fieldDescriptor['@isCaption'];
    }


    protected function decorate($fieldDescriptor)
    {


        if(!isset($fieldDescriptor['@isCaption'])) {
            if($fieldDescriptor['type'] == 'TEXT') {
                $fieldDescriptor['@isCaption'] = false;
            }
            else if($fieldDescriptor['type'] == 'INT') {
                $fieldDescriptor['@isCaption'] = false;
            }
            else if($fieldDescriptor['type'] == 'DATETIME') {
                $fieldDescriptor['@isCaption'] = false;
            }

        }
        return $fieldDescriptor;
    }




}
