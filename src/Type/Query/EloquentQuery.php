<?php

namespace Galahad\Graphoquent\Type\Query;

use Galahad\Graphoquent\Exception\ModelNotQueryable;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Str;

abstract class EloquentQuery extends Query
{
	/**
	 * @var string
	 */
	protected $className;
	
	/**
	 * Constructor
	 *
	 * @param string $className
	 */
	public function __construct($className)
	{
		$this->className = $className;
	}
	
	/**
	 * Get type from Eloquent model
	 *
	 * @return Type
	 * @throws ModelNotQueryable
	 */
	public function type()
	{
		if (!method_exists($this->className, 'getGraphQLType')) {
			$exception = new ModelNotQueryable();
			$exception->setModel($this->className);
			throw $exception;
		}
		
		return forward_static_call("{$this->className}::getGraphQLType");
	}
	
	/**
	 * Get the name of the model
	 *
	 * @return string
	 */
	protected function getModelName()
	{
		return class_basename($this->className);
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
