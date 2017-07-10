<?php

namespace Bezb\ModelBundle\Component;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

interface ModelFactoryInterface
{
	/**
	 * @param string $entityClass
	 * @return ModelInterface
	 */
	public function create($entityClass);

	/**
	 * @param string $name
	 * @param Behavior $behavior
	 */
	public function addBehavior($name, Behavior $behavior);

	/**
	 * @param string $modelName
	 * @param string $name
	 * @param string $serviceId
	 */
	public function addScenario($modelName, $name, $serviceId);

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