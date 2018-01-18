<?php

namespace Bezb\ModelBundle\Component;

interface BehaviorInterface extends ModelSubscriberInterface
{
    /**
     * @return array
     */
    public function getParameters();

    /**
     * @param $parameters
     * @return mixed
     */
    public function setParameters($parameters);

    /**
     * @param BehaviorEvent $event
     */
    public function onBeforeSave(BehaviorEvent $event);

    /**
     * @param BehaviorEvent $event
     */
    public function onAfterSave(BehaviorEvent $event);

    /**
     * @param BehaviorEvent $event
     */
    public function onAfterFind(BehaviorEvent $event);

    /**
     * @param BehaviorEvent $event
     */
    public function onBeforeDelete(BehaviorEvent $event);

    /**
     * @param BehaviorEvent $event
     */
    public function onAfterDelete(BehaviorEvent $event);
}