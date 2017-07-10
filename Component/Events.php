<?php

namespace Bezb\ModelBundle\Component;

final class Events
{
	/**
	 * @param $modelName
	 * @param $scenario
	 * @param $action
	 * @return string
	 */
	static public function scenarioEventName($modelName, $scenario, $action)
    {
		return join(".", ["model", $modelName, $scenario, $action]);
	}

	/**
	 * @param $name
	 * @param $action
	 * @return string
	 */
	static public function behaviorEventName($name, $action)
    {
		return join(".", ["model", "behavior", $name, $action]);
	}
}