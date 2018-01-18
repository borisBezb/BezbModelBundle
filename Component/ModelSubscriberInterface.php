<?php

namespace Bezb\ModelBundle\Component;

/**
 * Interface ModelSubscriberInterface
 * @package Bezb\ModelBundle\Component
 */
interface ModelSubscriberInterface
{
    /**
     * @param $name
     * @return mixed
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param $modelName
     * @return mixed
     */
    public function setModelName($modelName);

    /**
     * @return string
     */
    public function getModelName();
}