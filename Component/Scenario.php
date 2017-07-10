<?php

namespace Bezb\ModelBundle\Component;

abstract class Scenario extends ModelSubscriber
{
	const CREATE = "create";
	const UPDATE = "update";
    const DELETE = "delete";

	/**
	 * @var string
	 */
	static protected $name;

	/**
	 * @var ModelFactoryInterface
	 */
	protected $modelFactory;

	/**
	 * @param $name
	 * @return $this
	 */
	public function setName($name) 
	{
		static::$name = $name;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getName() 
	{
		return static::$name;
	}

	/**
	 * @param ModelFactoryInterface $modelFactory
	 * @return $this
	 */
	public function setModelFactory(ModelFactoryInterface $modelFactory)
	{
		$this->modelFactory = $modelFactory;
		
		return $this;
	}

	/**
	 * @return array
	 */
	public static function getSubscribedEvents()
	{
		return [
			Events::scenarioEventName(static::$modelName, static::$name, ModelEvent::BEFORE_SAVE) => array("onBeforeSave", 0),
			Events::scenarioEventName(static::$modelName, static::$name, ModelEvent::AFTER_SAVE) => array("onAfterSave", 0),
			Events::scenarioEventName(static::$modelName, static::$name, ModelEvent::AFTER_FIND) => array("onAfterFind", 0),
			Events::scenarioEventName(static::$modelName, static::$name, ModelEvent::BEFORE_DELETE) => array("onBeforeDelete", 0),
			Events::scenarioEventName(static::$modelName, static::$name, ModelEvent::AFTER_DELETE) => array("onAfterDelete", 0),
		];
	}
}

