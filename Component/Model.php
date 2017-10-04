<?php

namespace Bezb\ModelBundle\Component;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

abstract class Model implements ModelInterface
{
	/**
	 * @var string
	 */
	protected $modelName;

	/**
	 * @var EntityManager
	 */
	protected $em;

	/**
	 * @var EntityRepository
	 */
	protected $repository;

	/**
	 * @var Container
	 */
	protected $container;

	/**
	 * @var mixed
	 */
	protected $entity;

	/**
	 * @var string
	 */
	protected $entityClass;

	/**
	 * @var bool
	 */
	protected $isNew = true;

	/**
	 * @var Form
	 */
	protected $form;

	/**
	 * @var array
	 */
	protected $formData;

	/**
	 * @var string
	 */
	protected $scenario = Scenario::CREATE;

	/**
	 * @var array
	 */
	protected $scenarios;

    /**
     * @var array
     */
	protected $behaviors;

	/**
	 * @param Container $container
	 * @param EntityManager $em
	 * @param $entityClass
	 * @param $modelName
	 * @param array $scenarios
	 * @param array $behaviors
	 */
	public function __construct(Container $container, EntityManager $em, $entityClass, $modelName, $scenarios, $behaviors)
	{
		$this->container = $container;
		$this->em = $em;
		$this->modelName = $modelName;
		$this->entityClass = $entityClass;
		$this->scenarios = $scenarios;
        $this->behaviors = $behaviors;

		$this->entity = new $entityClass;
	}

	/**
	 * @return string
	 */
	public function getModelName()
	{
		return $this->modelName;
	}

    /**
     * @return EntityManager
     */
    public function getEm()
    {
        return $this->em;
    }

	/**
	 * @param $repository
	 * @return mixed|void
	 */
	public function setRepository($repository)
	{
		$this->repository = $repository;
	}

	/**
	 * @param $entity
	 * @return $this
	 */
	public function setEntity($entity)
	{
		$this->entity = $entity;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getEntity()
	{
		return $this->entity;
	}

	/**
	 * @param bool $isNew
	 * @return $this
	 */
	public function setIsNew($isNew)
	{
		$this->isNew = $isNew;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function getIsNew()
	{
		return $this->isNew;
	}

	/**
	 * @param $scenario
	 * @return $this
	 */
	public function setScenario($scenario)
	{
		if(in_array($scenario, $this->scenarios) === false) {
			//return false;
		}

		$this->scenario = $scenario;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getScenario()
	{
		return $this->scenario;
	}

	/**
	 * @param Form $form
	 * @param bool $setData
	 * @return $this|mixed
	 */
	public function setForm(Form $form, $setData = true)
	{
		$this->form = $form;

		if($setData === true) {
			$this->form->setData($this->getEntity());
		}

		return $this;
	}

	/**
	 * @return Form
	 */
	public function getForm()
	{
		return $this->form;
	}

	/**
	 * @param bool $validate
	 * @return bool
	 */
	public function save($validate = true)
	{

		$success = (($validate === false) || ($validate === true && $this->validate() === true));

		if($success === true) {
			return $this->update();
		}

		return false;
	}

	/**
	 * @return bool
	 */
	public function validate()
	{
		if(!($this->form instanceof Form)) {
			return true;
		}

		if($this->formData !== null) {
			$this->form->submit($this->formData);
		} else {
			$this->form->handleRequest($this->getRequest());
		}

		return $this->form->isValid();
	}

	/**
	 * @return bool
	 */
	public function update()
	{
		if($this->beforeSave() === false) {
			return false;
		}

		$this->saveAction();
		$this->afterSave();
		$this->setIsNew(false);

		if($this->getScenario() == Scenario::CREATE) {
			$this->setScenario(Scenario::UPDATE);
		}

		return true;
	}

	/**
	 * @return $this
	 */
	public function refresh()
	{
		if($this->getIsNew() === false) {
			$this->em->refresh($this->entity);
		}

		return $this;
	}

	/**
	 * @return bool
	 */
	public function delete()
    {
        $this->scenario = Scenario::DELETE;
		$this->beforeDelete();

		$this->em->remove($this->getEntity());
		$this->em->flush();

		$this->afterDelete();

        return true;
	}

	/**
	 * @param array $fields
	 * @return bool
	 */
	public function findBy($fields)
	{
		$entity = $this->repository->findOneBy($fields);

		if(!$entity) {
			return false;
		}

		$this->setEntity($entity);
		$this->setIsNew(false);
		$this->setScenario(Scenario::UPDATE);

		if($this->form instanceof Form) {
			$this->form->setData($entity);
		}

		$this->afterFind();

		return true;
	}

	/**
	 * @param $id
	 * @return bool
	 */
	public function findById($id)
	{
		return $this->findBy(["id" => $id]);
	}

	/**
	 * @return Request
	 */
	public function getRequest()
	{
		return $this->container->get("request_stack")->getCurrentRequest();
	}

	/**
	 * @return EventDispatcherInterface
	 */
	public function getEventDispatcher()
	{
		return $this->container->get('event_dispatcher');
	}

	/**
	 * @param array $formData
	 * @return $this
	 */
	public function setFormData(array $formData)
	{
		$this->formData = $formData;

		return $this;
	}

	/**
	 * @return $this
	 */
	public function removeFormData()
	{
		unset($this->formData);

		return $this;
	}

	protected function saveAction()
	{
		if($this->getIsNew() === true) {
			$this->em->persist($this->getEntity());
		}

		$this->em->flush($this->getEntity());
	}

	/**
	 * @return bool
	 */
	protected function beforeSave()
	{
		$eventName = Events::scenarioEventName($this->modelName, $this->scenario, ModelEvent::BEFORE_SAVE);
		$this->getEventDispatcher()->dispatch($eventName, $this->createEvent());

        $this->dispatchBehaviors(ModelEvent::BEFORE_SAVE);

		return true;
	}

	/**
	 * @return bool
	 */
	protected function afterSave()
	{
		$eventName = Events::scenarioEventName($this->modelName, $this->scenario, ModelEvent::AFTER_SAVE);
		$this->getEventDispatcher()->dispatch($eventName, $this->createEvent());

        $this->dispatchBehaviors(ModelEvent::AFTER_SAVE);

		return true;
	}

	/**
	 * @return bool
	 */
	protected function afterFind()
	{
		$eventName = Events::scenarioEventName($this->modelName, $this->scenario, ModelEvent::AFTER_FIND);
		$this->getEventDispatcher()->dispatch($eventName, $this->createEvent());

        $this->dispatchBehaviors(ModelEvent::AFTER_FIND);

		return true;
	}

	/**
	 * @return bool
	 */
	protected function beforeDelete()
	{
		$eventName = Events::scenarioEventName($this->modelName, $this->scenario, ModelEvent::BEFORE_DELETE);
		$this->getEventDispatcher()->dispatch($eventName, $this->createEvent());

        $this->dispatchBehaviors(ModelEvent::BEFORE_DELETE);

		return true;
	}

	/**
	 * @return bool
	 */
	protected function afterDelete()
	{
		$eventName = Events::scenarioEventName($this->modelName, $this->scenario, ModelEvent::AFTER_DELETE);
		$this->getEventDispatcher()->dispatch($eventName, $this->createEvent());

        $this->dispatchBehaviors(ModelEvent::AFTER_DELETE);

		return true;
	}

    /**
     * @param $action
     */
    protected function dispatchBehaviors($action)
    {
        foreach ($this->behaviors as $name => $parameters) {
            $eventName = Events::behaviorEventName($name, $action);
            $this->getEventDispatcher()->dispatch($eventName, $this->createBehaviorEvent($parameters));
        }
    }

	/**
	 * @return ModelEvent
	 */
	protected function createEvent()
	{
		return new ModelEvent($this, $this->container);
	}

    /**
     * @param $parameters
     * @return BehaviorEvent
     */
    protected function createBehaviorEvent($parameters)
    {
        $event = new BehaviorEvent($this, $this->container);
        $event->setParameters($parameters);

        return $event;
    }

    /**
     * @param $entity
     * @return array
     */
    public function getChangeSet($entity = null)
    {
        $uow = $this->em->getUnitOfWork();
        $uow->computeChangeSets();
        return $uow->getEntityChangeSet($entity ?: $this->getEntity());
    }
}