<?php

namespace Bezb\ModelBundle\Component;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

abstract class ModelSubscriber implements EventSubscriberInterface
{
	/**
	 * @var string
	 */
	static protected $modelName;

	/**
	 * @param $modelName
	 * @return $this
	 */
	public function setModelName($modelName) 
	{
		static::$modelName = $modelName;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getModelName() 
	{
		return static::$modelName;
	}

	/**
	 * @param ModelEvent $event
	 */
	public function onBeforeSave(ModelEvent $event) 
	{

	}

	/**
	 * @param ModelEvent $event
	 */
	public function onAfterSave(ModelEvent $event) 
	{

	}

	/**
	 * @param ModelEvent $event
	 */
	public function onAfterFind(ModelEvent $event) 
	{

	}

	/**
	 * @param ModelEvent $event
	 */
	public function onBeforeDelete(ModelEvent $event) 
	{

	}

	/**
	 * @param ModelEvent $event
	 */
	public function onAfterDelete(ModelEvent $event) 
	{

	}
}

