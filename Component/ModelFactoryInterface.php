<?php

namespace Bezb\ModelBundle\Component;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Interface ModelFactoryInterface
 * @package Bezb\ModelBundle\Component
 */
interface ModelFactoryInterface
{
	/**
	 * @param string $entityClass
	 * @return ModelInterface
	 */
	public function create($entityClass);

    /**
     * @param $behaviorClass
     * @return mixed
     */
	public function addBehavior(BehaviorInterface $behaviorClass);

    /**
     * @param $scenarioClass
     * @return mixed
     */
	public function addScenario($scenarioClass);

    /**
     * @param $modelName
     * @param $name
     * @return null
     */
    public function getScenario($modelName, $name);

	/**
	 * @param $modelName
	 * @param $modelClass
	 * @param $entityClass
	 * @return ModelInterface
	 */
	public function customCreate($modelName, $modelClass, $entityClass);
}