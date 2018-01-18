<?php

namespace Bezb\ModelBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class Model
 * @Annotation
 * @Target("CLASS")
 */
class Model
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var array
     */
    protected $behaviors = [];

    /**
     * Model constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $properties = get_object_vars($this);
        foreach ($properties as $key => $value) {
            if (isset($data[$key])) {
                $this->{$key} = $data[$key];
            }
        }
    }

    /**
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * If model's name has not defined, will be returned underscored class name of model
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $class
     * @return $this
     */
    public function setClass($class)
    {
        $this->class = $class;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return array
     */
    public function getBehaviors()
    {
        return $this->behaviors;
    }
}