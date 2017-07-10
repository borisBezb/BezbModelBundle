<?php

namespace Bezb\ModelBundle\Component;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\MappingException;
use Doctrine\Common\Persistence\Mapping\MappingException as CommonMappingException;
use Bezb\ModelBundle\Annotation;
use Bezb\ModelBundle\Cache\AnnotationCacheInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Bezb\ModelBundle\Exception\ModelMappingException;

class ModelFactory implements ModelFactoryInterface
{
	/**
	 * @var Container
	 */
	protected $container;

	/**
	 * @var EntityManager
	 */
	protected $em;

	/**
	 * @var EventDispatcherInterface
	 */
	protected $eventDispatcher;

    /**
     * @var Reader
     */
    protected $annotationReader;

    /**
     * @var AnnotationCacheInterface
     */
    protected $cache;

	/**
	 * @var array
	 */
	protected $scenarios = [];

    /**
     * @var array
     */
    protected $mountedScenarios = [];

    /**
     * @var array
     */
    protected $behaviors = [];

    /**
     * ModelFactory constructor.
     * @param EntityManager $em
     * @param Container $container
     * @param EventDispatcherInterface $eventDispatcher
     * @param Reader $annotationReader
     * @param AnnotationCacheInterface $cache
     */
	public function __construct(
        EntityManager $em,
        Container $container,
        EventDispatcherInterface $eventDispatcher,
        Reader $annotationReader,
        AnnotationCacheInterface $cache
    ) {
		$this->em = $em;
		$this->container = $container;
		$this->eventDispatcher = $eventDispatcher;
        $this->annotationReader = $annotationReader;
        $this->cache = $cache;
	}

    /**
     * @param $entityClass
     * @return mixed
     * @throws \Exception
     */
    public function create($entityClass)
    {
        $annotation = $this->getAnnotation($entityClass);
        $modelName = $annotation->getName();

        $scenarios = [];
        if (isset($this->scenarios[$modelName])) {
            $scenarios = array_keys($this->scenarios[$modelName]);

            if (!isset($this->mountedScenarios[$modelName])) {
                $this->mountScenarios($modelName);
            }
        }

        $modelClass = $annotation->getClass();
        $model = new $modelClass(
            $this->container,
            $this->em, 
            $entityClass, 
            $annotation->getName(), 
            $scenarios,
            $annotation->getBehaviors()
        );

        try {
            $repository = $this->em->getRepository($entityClass);
            $model->setRepository($repository);
        } catch(MappingException $e) {

        } catch(CommonMappingException $e) {

        }

        return $model;
    }

    /**
     * @param $modelName
     * @param $modelClass
     * @param $entityClass
     * @return mixed
     */
    public function customCreate($modelName, $modelClass, $entityClass)
    {
        $annotation = $this->getAnnotation($entityClass);
        $scenarios = [];
        if (isset($this->scenarios[$modelName])) {
            $scenarios = array_keys($this->scenarios[$modelName]);

            if (!isset($this->mountedScenarios[$modelName])) {
                $this->mountScenarios($modelName);
            }
        }
     
        $model = new $modelClass(
            $this->container,
            $this->em,
            $entityClass,
            $modelName,
            $scenarios,
            $annotation->getBehaviors()
        );

        try {
            $repository = $this->em->getRepository($entityClass);
            $model->setRepository($repository);
        } catch(MappingException $e) {

        } catch(CommonMappingException $e) {

        }

        return $model;
    }

    /**
     * @param $modelName
     */
    protected function mountScenarios($modelName)
    {
        $scenarios = $this->scenarios[$modelName];

        foreach ($scenarios as $name => $serviceId) {

            /** @var Scenario $scenario */
            $scenario = $this->container->get($serviceId);
            $scenario
                ->setName($name)
                ->setModelName($modelName)
                ->setModelFactory($this)
            ;

            $this->eventDispatcher->addSubscriber($scenario);
        }
    }

    /**
     * @param $entityClass
     * @return Annotation\Model
     * @throws \Exception
     */
    protected function getAnnotation($entityClass)
    {
        $annotation = $this->cache->getFromCache($entityClass);
        if (!$annotation) {
            $reflection = new \ReflectionClass($entityClass);
            
            /** @var Annotation\Model $annotation */
            $annotation = $this->annotationReader->getClassAnnotation(
                $reflection,
                Annotation\Model::class
            );

            if (!$annotation) {
                throw new ModelMappingException("$entityClass must be wrapped in Model annotation");
            }
            
            if (!$annotation->getName()) {
                $annotation->setName($this->makeModelName($entityClass));
            }
            
            if (!$annotation->getClass()) {
                $annotation->setClass(SimpleModel::class);
            }
            
            $this->cache->setToCache($entityClass, $annotation);
        }

        return $annotation;
    }
    
    /**
     * @param $class
     * @return string
     */
    protected function makeModelName($class)
    {
        $pos = strrpos($class, '\\') + 1;
        return strtolower(
            preg_replace('/([a-z])(?=[A-Z])/', '$1_', substr($class, $pos))
        );
    }
    
    /**
     * @param $name
     * @param Behavior $behavior
     */
	public function addBehavior($name, Behavior $behavior)
	{
        if (isset($this->behaviors[$name])) {
            return;
        }
        
        $behavior->setName($name);
        
		$this->behaviors[$name] = $behavior;
        $this->eventDispatcher->addSubscriber($behavior);
	}

    /**
     * @param string $modelName
     * @param string $name
     * @param string $serviceId
     */
	public function addScenario($modelName, $name, $serviceId)
    {
        if (!isset($this->scenarios[$modelName])) {
            $this->scenarios[$modelName] = [];
        }

		if (isset($this->scenarios[$modelName][$name])) {
			return;
		}

        $this->scenarios[$modelName][$name] = $serviceId;
	}


    /**
     * @param $modelName
     * @param $name
     * @return null
     */
	public function getScenario($modelName, $name)
    {
        if(
            !isset($this->scenarios[$modelName][$name]) ||
            !$this->container->has($this->scenarios[$modelName][$name])
        ) {
            return null;
        }

        /** @var Scenario $scenario */
        $scenario = $this->container->get($this->scenarios[$modelName][$name]);
        $scenario
            ->setName($name)
            ->setModelName($modelName)
            ->setModelFactory($this)
        ;

        return $scenario;
    }
}