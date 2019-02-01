<?php
namespace Planck\Aspect;

use Planck\Aspect;

class Application extends Aspect
{

    /**
     * @var \Planck\Application
     */
    protected $application;



    public static function getName()
    {
        return static::class;
    }

    public function __construct(\Planck\Application $application)
    {
        $this->application = $application;
    }


}