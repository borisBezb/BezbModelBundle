<?php

namespace Bezb\ModelBundle\Component;

use Doctrine\ORM\{ EntityManagerInterface, EntityRepository };
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class Model
 * @package Bezb\ModelBundle\Component
 */
abstract class Model implements ModelInterface
{
	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var EntityManagerInterface
	 */
	protected $em;

    /**
     * @var EventDispatcherInterface
     */
	protected $eventDispatcher;

    /**
     * @var RequestStack
     */
	protected $requestStack;

	/**
	 * @var EntityRepository
	 */
	protected $repository;

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
	protected $scenario = ScenarioInterface::CREATE;

    /**
     * @var array
     */
	protected $scenarioParameters = [];

	/**
	 * @var array
	 */
	protected $scenarios;

    /**
     * @var array
     */
	protected $behaviors;

    /**
     * Model constructor.
     * @param EntityManagerInterface $em
     * @param EventDispatcherInterface $eventDispatcher
     * @param RequestStack $requestStack
     * @param $entityClass
     * @param $name
     * @param $scenarios
     * @param $behaviors
     */
	public function __construct
    (
	     EntityManagerInterface $em,
         EventDispatcherInterface $eventDispatcher,
         RequestStack $requestStack,
         $entityClass,
         $name,
         $scenarios,
         $behaviors
    )
	{
		$this->em = $em;
		$this->eventDispatcher = $eventDispatcher;
		$this->requestStack = $requestStack;
		$this->name = $name;
		$this->entityClass = $entityClass;
		$this->scenarios = $scenarios;
        $this->behaviors = $behaviors;

		$this->entity = new $entityClass;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
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
     * @param array $parameters
	 * @return $this
	 */
	public function setScenario($scenario, $parameters = [])
	{
		if (false === in_array($scenario, $this->scenarios)) {
			return false;
		}

		$this->scenario = $scenario;
		$this->scenarioParameters = $parameters;

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

		if (true === $setData) {
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

		$success = (false === $validate) || (true === $this->validate());

		if (true === $success) {
			return $this->update();
		}

		return false;
	}

	/**
	 * @return bool
	 */
	public function validate()
	{
		if (!($this->form instanceof Form)) {
			return true;
		}

		if ($this->formData !== null) {
			$this->form->submit($this->formData);
		} else {
			$this->form->handleRequest($this->requestStack->getCurrentRequest());
		}

		return $this->form->isSubmitted() && $this->form->isValid();
	}

	/**
	 * @return bool
	 */
	public function update()
	{
		if ($this->beforeSave() === false) {
			return false;
		}

		$this->saveAction();
		$this->afterSave();
		$this->setIsNew(false);

		if ($this->getScenario() == ScenarioInterface::CREATE) {
			$this->setScenario(ScenarioInterface::UPDATE);
		}

		return true;
	}

	/**
	 * @return $this
	 */
	public function refresh()
	{
		if ($this->getIsNew() === false) {
			$this->em->refresh($this->entity);
		}

		return $this;
	}

	/**
	 * @return bool
	 */
	public function delete()
    {
        $this->scenario = ScenarioInterface::DELETE;
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

		if (!$entity) {
			return false;
		}

		$this->setEntity($entity);
		$this->setIsNew(false);
		$this->setScenario(ScenarioInterface::UPDATE);

		if ($this->form instanceof Form) {
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
     * @return array
     */
	public function getScenarioParameters()
    {
        return $this->scenarioParameters;
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
		if ($this->getIsNew() === true) {
			$this->em->persist($this->getEntity());
		}

		$this->em->flush($this->getEntity());
	}

	/**
	 * @return bool
	 */
	protected function beforeSave()
	{
        $this->dispatchScenario(ModelEvent::BEFORE_SAVE);
        $this->dispatchBehaviors(ModelEvent::BEFORE_SAVE);

		return true;
	}

	/**
	 * @return bool
	 */
	protected function afterSave()
	{
        $this->dispatchScenario(ModelEvent::AFTER_SAVE);
        $this->dispatchBehaviors(ModelEvent::AFTER_SAVE);

		return true;
	}

	/**
	 * @return bool
	 */
	protected function afterFind()
	{
        $this->dispatchScenario(ModelEvent::AFTER_FIND);
        $this->dispatchBehaviors(ModelEvent::AFTER_FIND);

		return true;
	}

	/**
	 * @return bool
	 */
	protected function beforeDelete()
	{
        $this->dispatchScenario(ModelEvent::BEFORE_DELETE);
        $this->dispatchBehaviors(ModelEvent::BEFORE_DELETE);

		return true;
	}

	/**
	 * @return bool
	 */
	protected function afterDelete()
	{
		$this->dispatchScenario(ModelEvent::AFTER_DELETE);
        $this->dispatchBehaviors(ModelEvent::AFTER_DELETE);

		return true;
	}

    /**
     * @param $action
     */
	protected function dispatchScenario($action)
    {
        $eventName = Events::scenarioEventName($this->name, $this->scenario, $action);
        $this->eventDispatcher->dispatch($this->createEvent(), $eventName);
    }

    /**
     * @param $action
     */
    protected function dispatchBehaviors($action)
    {
        foreach ($this->behaviors as $name => $parameters) {
            $eventName = Events::behaviorEventName($name, $action);
            $this->eventDispatcher->dispatch($this->createBehaviorEvent($parameters), $eventName);
        }
    }

	/**
	 * @return ModelEvent
	 */
	protected function createEvent()
	{
		return new ModelEvent($this);
	}

    /**
     * @param $parameters
     * @return BehaviorEvent
     */
    protected function createBehaviorEvent($parameters)
    {
        $event = new BehaviorEvent($this);
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