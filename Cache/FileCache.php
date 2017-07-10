<?php

namespace Bezb\ModelBundle\Cache;

use Bezb\ModelBundle\Annotation;

class FileCache implements AnnotationCacheInterface
{
    /**
     * @var string
     */
    protected $cacheDir;

    /**
     * @var string
     */
    protected $environment;

    /**
     * FileCache constructor.
     * @param $cacheDir
     * @param $environment
     */
    public function __construct($cacheDir, $environment)
    {
        $this->cacheDir = $cacheDir;
        $this->environment = $environment;
    }

    /**
     * @param $entityClass
     * @return mixed|null
     */
    public function getFromCache($entityClass) 
    {
        if ( true === in_array($this->environment, ['dev', 'test']) ) {
            return null;
        }

        $cacheFilePath = $this->getFilePath($entityClass);
        
        if (file_exists($cacheFilePath)) {
            return include $cacheFilePath;
        }
        
        return null;
    }

    /**
     * @param $entityClass
     * @param Annotation\Model $annotation
     */
    public function setToCache($entityClass, Annotation\Model $annotation)
    {
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0775);
        }

        $serialized = serialize($annotation);
        file_put_contents($this->getFilePath($entityClass), '<?php return unserialize(\'' . $serialized . '\');');
    }

    /**
     * @param $entityClass
     * @return string
     */
    protected function getFilePath($entityClass)
    {
         return $this->cacheDir . '/' . str_replace('\\', '', $entityClass) . '.php';
    }
}