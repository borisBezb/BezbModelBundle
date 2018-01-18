<?php

namespace Bezb\ModelBundle\Component;

/**
 * Class Scenario
 * @package Bezb\ModelBundle\Component
 */
abstract class BaseScenario extends ModelSubscriber implements ScenarioInterface
{
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

