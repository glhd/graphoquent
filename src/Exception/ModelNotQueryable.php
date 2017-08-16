<?php

namespace Galahad\Graphoquent\Exception;

use Illuminate\Database\Eloquent\Model;

class ModelNotQueryable extends Exception
{
	/**
	 * @var Model
	 */
	protected $model;
	
	/**
	 * @return mixed
	 */
	public function getModel()
	{
		return $this->model;
	}
	
	/**
	 * @param Model $model
	 * @return $this
	 */
	public function setModel(Model $model)
	{
		$this->model = $model;
		
		return $this;
	}
}
