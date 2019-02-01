<?php
namespace Planck\Model;

class FingerPrint
{

    private $object;
    public function __construct($object)
    {
        $this->object = $object;
    }


    public function __toString()
    {

        if($this->object instanceof Entity) {
            json_encode(array(
                'type' => 'entity',
                'instance' => get_class($this->object),
                'id' => $this->getId()
            ));
        }

        return get_class($this->object);
        // TODO: Implement __toString() method.
    }


}
