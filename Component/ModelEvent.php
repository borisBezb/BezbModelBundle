<?php

namespace Bezb\ModelBundle\Component;

use Symfony\Contracts\EventDispatcher\Event;

class ModelEvent extends Event 
{
	const BEFORE_SAVE = "before_save";
	const AFTER_SAVE = "after_save";
	const AFTER_FIND = "after_find";
	const BEFORE_DELETE = "before_delete";
	const AFTER_DELETE = "after_delete";

	static public $methodMapping = [
	    'onBeforeSave'      => self::BEFORE_SAVE,
        'onAfterSave'       => self::AFTER_SAVE,
        'onAfterFind'       => self::AFTER_FIND,
        'onBeforeDelete'    => self::BEFORE_DELETE,
        'onAfterDelete'     => self::AFTER_DELETE
    ];

	/**
	 * @var ModelInterface
	 */
	protected $model;

    /**
     * ModelEvent constructor.
     * @param Model $model
     */
	public function __construct(Model $model)
    {
		$this->model = $model;
	}

	/**
	 * @return Model
	 */
	public function getModel() 
    {
		return $this->model;
	}
}