<?php

namespace Bezb\ModelBundle\Component;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

abstract class Behavior extends ModelSubscriber
{
	/**
	 * @var string
	 */
	protected static $name;

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
	 * @param $parameters
	 * @return $this
	 */
	public function setParameters($parameters)
	{
		$this->parameters = $parameters;

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
	 * @param $name
	 * @return $this
	 */
	public function setName($name)
	{
		static::$name = $name;

		return $this;
	}

	/**
	 * @return array
	 */
	public static function getSubscribedEvents()
	{
		return [
			Events::behaviorEventName(static::$name, ModelEvent::BEFORE_SAVE) => array("onBeforeSave", 0),
			Events::behaviorEventName(static::$name, ModelEvent::AFTER_SAVE) => array("onAfterSave", 0),
			Events::behaviorEventName(static::$name, ModelEvent::AFTER_FIND) => array("onAfterFind", 0),
			Events::behaviorEventName(static::$name, ModelEvent::BEFORE_DELETE) => array("onBeforeDelete", 0),
			Events::behaviorEventName(static::$name, ModelEvent::AFTER_DELETE) => array("onAfterDelete", 0),
		];
	}
}

