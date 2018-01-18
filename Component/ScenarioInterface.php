<?php

namespace Bezb\ModelBundle\Component;

/**
 * Interface ScenarioInterface
 * @package Bezb\ModelBundle\Component
 */
interface ScenarioInterface extends ModelSubscriberInterface
{
    const CREATE = "create";
    const UPDATE = "update";
    const DELETE = "delete";

    /**
     * @param ModelEvent $event
     */
    public function onBeforeSave(ModelEvent $event);

    /**
     * @param ModelEvent $event
     */
    public function onAfterSave(ModelEvent $event);

    /**
     * @param ModelEvent $event
     */
    public function onAfterFind(ModelEvent $event);

    /**
     * @param ModelEvent $event
     */
    public function onBeforeDelete(ModelEvent $event);

    /**
     * @param ModelEvent $event
     */
    public function onAfterDelete(ModelEvent $event);
}