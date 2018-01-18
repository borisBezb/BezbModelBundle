<?php

namespace Bezb\ModelBundle\Component;

/**
 * Class ModelSubscriber
 * @package Bezb\ModelBundle\Component
 */
abstract class ModelSubscriber implements ModelSubscriberInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
	 * @var string
	 */
    protected $modelName;

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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

	/**
	 * @param $modelName
	 * @return $this
	 */
	public function setModelName($modelName) 
	{
		$this->modelName = $modelName;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getModelName() 
	{
		return $this->modelName;
	}
}

