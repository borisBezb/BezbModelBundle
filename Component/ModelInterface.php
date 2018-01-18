<?php

namespace Bezb\ModelBundle\Component;

use Symfony\Component\Form\Form;

/**
 * Interface ModelInterface
 * @package Bezb\ModelBundle\Component
 */
interface ModelInterface
{
	/**
	 * @return string
	 */
	public function getName();

	/**
	 * @param $entity
	 * @return mixed
	 */
	public function setEntity($entity);

	/**
	 * @return mixed
	 */
	public function getEntity();

	/**
	 * @param bool $isNew
	 * @return mixed
	 */
	public function setIsNew($isNew);

	/**
	 * @return bool
	 */
	public function getIsNew();

	/**
	 * @param $repository
	 * @return mixed
	 */
	public function setRepository($repository);

	/**
	 * @param string $scenario
	 * @return mixed
	 */
	public function setScenario($scenario);

	/**
	 * @return string
	 */
	public function getScenario();

	/**
	 * @param Form $form
	 * @return mixed
	 */
	public function setForm(Form $form);

	/**
	 * @return Form
	 */
	public function getForm();

	/**
	 * @param bool $validate
	 * @return bool
	 */
	public function save($validate = true);

	/**
	 * @return bool
	 */
	public function validate();

	/**
	 * @return bool
	 */
	public function update();

	/**
	 * @return mixed
	 */
	public function refresh();

	/**
	 * @return mixed
	 */
	public function delete();

	/**
	 * @param array $fields
	 * @return bool
	 */
	public function findBy($fields);
}