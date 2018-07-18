<?php

namespace Bezb\ModelBundle\Component;

use Bezb\ModelBundle\Annotation;
use Bezb\ModelBundle\Cache\AnnotationCacheInterface;
use Bezb\ModelBundle\Exception\{ RuntimeException, ModelMappingException };
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\MappingException;
use Doctrine\Common\Persistence\Mapping\MappingException as CommonMappingException;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class ModelFactory
 * @package Bezb\ModelBundle\Component
 */
class ModelFactory implements ModelFactoryInterface
{
	/**
	 * @var ContainerInterface
	 */
	protected $container;

	/**
	 * @var EntityManagerInterface
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
     * @var RequestStack
     */
    protected $requestStack;

	public function __construct(
        EntityManagerInterface $em,
        ContainerInterface $container,
        EventDispatcherInterface $eventDispatcher,
        Reader $annotationReader,
        AnnotationCacheInterface $cache,
        RequestStack $requestStack
    )
    {
		$this->em = $em;
		$this->container = $container;
		$this->eventDispatcher = $eventDispatcher;
        $this->annotationReader = $annotationReader;
        $this->cache = $cache;
        $this->requestStack = $requestStack;
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
            $this->em,
            $this->eventDispatcher,
            $this->requestStack,
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

        foreach ($scenarios as $name => $scenarioClass) {

            /** @var BaseScenario $scenario */
            $scenario = $this->container->get($scenarioClass);
            $scenario
                ->setName($name)
                ->setModelName($modelName)
            ;

            $reflection = new \ReflectionClass($scenarioClass);
            $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

            foreach ($methods as $method) {
                if ($method->class == $scenarioClass  && array_key_exists($method->name, ModelEvent::$methodMapping)) {
                    $action = ModelEvent::$methodMapping[$method->name];
                    
                    $this->eventDispatcher->addListener(
                        Events::scenarioEventName($modelName, $name, $action),
                        [$scenario, $method->name]
                    );
                }
            }
        }
	//mark scenarios for model as mounted
        $this->mountedScenarios[$modelName] = true;
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
                $annotation->setName($this->buildNameByClass($entityClass));
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
    protected function buildNameByClass($class)
    {
        $pos = strrpos($class, '\\') + 1;
        return strtolower(
            preg_replace('/([a-z])(?=[A-Z])/', '$1_', substr($class, $pos))
        );
    }

    /**
     * @param BehaviorInterface $behavior
     * @return mixed|void
     * @throws RuntimeException
     */
	public function addBehavior(BehaviorInterface $behavior)
	{
	    $reflection = new \ReflectionClass($behavior);

	    /** @var Annotation\Behavior $annotation */
	    $annotation = $this->annotationReader->getClassAnnotation(
	        $reflection, Annotation\Behavior::class
        );

        if (!$annotation->getName()) {
            $annotation->setName($this->buildNameByClass($reflection->name));
        }

        $behavior->setName($annotation->getName());
        
        $reflection = new \ReflectionClass($behavior);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            if ($method->class == $reflection->name && array_key_exists($method->name, ModelEvent::$methodMapping)) {
                $action = ModelEvent::$methodMapping[$method->name];
                
                $this->eventDispatcher->addListener(
                    Events::behaviorEventName($behavior->getName(), $action),
                    [$behavior, $method->name]
                );
            }
        }
	}

    /**
     * @param $scenarioClass
     * @return mixed|void
     * @throws RuntimeException
     */
    public function addScenario($scenarioClass)
    {
        $reflection = new \ReflectionClass($scenarioClass);

        /** @var Annotation\Scenario $annotation */
        $annotation = $this->annotationReader->getClassAnnotation(
            $reflection,
            Annotation\Scenario::class
        );

        if (!$annotation->getName()) {
            $annotation->setName($this->buildNameByClass($scenarioClass));
        }

        if (!$annotation->getModel()) {
            throw new RuntimeException("Scenario $scenarioClass has to contain model attribute");
        }

        $this->scenarios[$annotation->getModel()][$annotation->getName()] = $scenarioClass;
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

        /** @var BaseScenario $scenario */
        $scenario = $this->container->get($this->scenarios[$modelName][$name]);
        $scenario
            ->setName($name)
            ->setModelName($modelName)
        ;

        return $scenario;
    }
}
