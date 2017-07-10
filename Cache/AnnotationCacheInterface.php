<?php

namespace Bezb\ModelBundle\Cache;

use Bezb\ModelBundle\Annotation;

interface AnnotationCacheInterface
{
    /**
     * @param $entityClass
     * @param Annotation\Model $annotation
     */
    public function setToCache($entityClass, Annotation\Model $annotation);

    /**
     * @param $entityClass
     * @return null|Annotation\Model
     */
    public function getFromCache($entityClass);
}