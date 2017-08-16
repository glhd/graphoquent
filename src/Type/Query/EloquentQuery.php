<?php

namespace Galahad\Graphoquent\Type\Query;

use Galahad\Graphoquent\Exception\ModelNotQueryable;
use Galahad\Graphoquent\GraphQL;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

abstract class EloquentQuery extends Query
{
	/**
	 * @var Model
	 */
	protected $model;
	
	/**
	 * @var GraphQL
	 */
	protected $graphQL;
	
	/**
	 * Constructor
	 *
	 * @param GraphQL $graphQL
	 * @param Model $model
	 */
	public function __construct(GraphQL $graphQL, Model $model)
	{
		$this->graphQL = $graphQL;
		$this->model = $model;
	}
	
	/**
	 * Get type from Eloquent model
	 *
	 * @return Type
	 * @throws ModelNotQueryable
	 */
	public function type()
	{
		$className = get_class($this->model);
		if (!method_exists($className, 'getGraphQLType')) {
			$exception = new ModelNotQueryable();
			$exception->setModel($this->model);
			throw $exception;
		}
		
		return forward_static_call("{$className}::getGraphQLType");
	}
	
	/**
	 * Get the name of the model
	 *
	 * @return string
	 */
	protected function getModelName()
	{
		return class_basename(get_class($this->model));
	}
	
	/**
	 * Get the name of the model formatted for a description
	 *
	 * @return string
	 */
	protected function getModelNameForDescription()
	{
		return Str::snake($this->getModelName(), ' ');
	}
}
