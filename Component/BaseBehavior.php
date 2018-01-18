<?php

namespace Bezb\ModelBundle\Component;

/**
 * Class Behavior
 * @package Bezb\ModelBundle\Component
 */
abstract class BaseBehavior extends ModelSubscriber implements BehaviorInterface
{
	/**
	 * @var array
	 */
	protected $parameters = [];

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::behaviorEventName(static::$name, ModelEvent::BEFORE_SAVE) => ["onBeforeSave", 0],
            Events::behaviorEventName(static::$name, ModelEvent::AFTER_SAVE) => ["onAfterSave", 0],
            Events::behaviorEventName(static::$name, ModelEvent::AFTER_FIND) => ["onAfterFind", 0],
            Events::behaviorEventName(static::$name, ModelEvent::BEFORE_DELETE) => ["onBeforeDelete", 0],
            Events::behaviorEventName(static::$name, ModelEvent::AFTER_DELETE) => ["onAfterDelete", 0]
        ];
    }

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
     * @param BehaviorEvent $event
     */
    public function onBeforeSave(BehaviorEvent $event)
    {

    }

    /**
     * @param BehaviorEvent $event
     */
    public function onAfterSave(BehaviorEvent $event)
    {

    }

    /**
     * @param BehaviorEvent $event
     */
    public function onAfterFind(BehaviorEvent $event)
    {

    }

    /**
     * @param BehaviorEvent $event
     */
    public function onBeforeDelete(BehaviorEvent $event)
    {

    }

    /**
     * @param BehaviorEvent $event
     */
    public function onAfterDelete(BehaviorEvent $event)
    {

    }
}

