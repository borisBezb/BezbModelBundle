<?php

namespace Bezb\ModelBundle\Component;

class BehaviorEvent extends ModelEvent 
{
    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     * @return $this
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
        
        return $this;
    }
}