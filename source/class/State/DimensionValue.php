<?php

namespace Planck\State;

use Planck\Traits\Listenable;

class DimensionValue
{

    const EVENT_CHANGE = 'change';

    use Listenable;


    /**
     * @var Dimension
     */
    private $dimension;

    protected $value;


    public function __construct(Dimension $dimension, $value = null)
    {
        $this->dimension = $dimension;
        $this->value = $value;
    }

    public function setValue($value)
    {
        if($value !== $this->value) {
            $this->fireEvent(__CLASS__.'.'.static::EVENT_CHANGE, array(
                'value' => $this
            ));
            if(get_class($this) !==__CLASS__) {
                $this->fireEvent(get_class($this).'.'.static::EVENT_CHANGE, array(
                    'value' => $this
                ));
            }

        }
        $this->value = $value;
    }


    public function getValue()
    {
        return $this->value;
    }


    /**
     * @return Dimension
     */
    public function getDimension()
    {
        return $this->dimension;
    }



}