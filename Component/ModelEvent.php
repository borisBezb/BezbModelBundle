<?php

namespace Bezb\ModelBundle\Component;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\Event;

class ModelEvent extends Event 
{
	const BEFORE_SAVE = "before_save";
	const AFTER_SAVE = "after_save";
	const AFTER_FIND = "after_find";
	const BEFORE_DELETE = "before_delete";
	const AFTER_DELETE = "after_delete";

	/**
	 * @var ModelInterface
	 */
	protected $model;

	/**
	 * @var \Symfony\Component\DependencyInjection\Container
	 */
	protected $container;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	public function __construct(Model $model, Container $container) 
    {
		$this->model = $model;
		$this->container = $container;
		$this->em = $this->container->get("doctrine")->getManager();
	}

	/**
	 * @return Model
	 */
	public function getModel() 
    {
		return $this->model;
	}

	/**
	 * @return Container
	 */
	public function getContainer() 
    {
		return $this->container;
	}

	/**
	 * @return EntityManager
	 */
	public function getManager() 
    {
		return $this->em;
	}
}