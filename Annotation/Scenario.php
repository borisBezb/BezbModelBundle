<?php

namespace Bezb\ModelBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class Scenario
 * @Annotation
 * @Target("CLASS")
 */
class Scenario
{
    /**
     * @var string
     */
    protected $model;

    /**
     * @var string
     */
    protected $name;

    /**
     * Scenario constructor.
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
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param $model
     * @return $this
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
}