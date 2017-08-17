<?php

namespace Galahad\Graphoquent;

use Galahad\Graphoquent\Exception\ModelNotQueryable;
use Galahad\Graphoquent\Http\Request;
use Galahad\Graphoquent\Type\Query\Query;
use GraphQL\Executor\ExecutionResult;
use GraphQL\GraphQL as BaseGraphQL;
use GraphQL\Schema;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Validator\Rules\OperationExtractor;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Graphoquent
{
	/**
	 * @var Gate
	 */
	protected $gate;
	
	/**
	 * @var array
	 */
	protected $config;
	
	/**
	 * @var Collection|Type[]
	 */
	protected $types;
	
	/**
	 * @var Collection|Query[]
	 */
	protected $queries;
	
	/**
	 * @var Collection|Query[]
	 */
	protected $mutations;
	
	/**
	 * Constructor
	 *
	 * @param Gate $gate
	 * @param array $config
	 */
	public function __construct(Gate $gate, array $config)
	{
		$this->gate = $gate;
		$this->config = Arr::except($config, ['types']);
	}
	
	/**
	 * @param Request $request
	 * @return ExecutionResult
	 */
	public function executeForRequest(Request $request)
	{
		return BaseGraphQL::executeAndReturnResult(
			$this->schemaForActor($request->getActor()),
			$request->getRequestString(),
			$request->getRoot(),
			$request->getContext(),
			$request->getVariables(),
			$request->getOperation()
		);
	}
	
	protected function getTypes()
	{
		if (!$this->types) {
			$this->types = Collection::make(Arr::get($this->config, 'types', []))
				->map(function($className) {
					if (method_exists($className, 'getGraphQLType')) {
						return forward_static_call("{$className}::getGraphQLType");
					}
					
					if (is_subclass_of($className, Type::class)) {
						return new $className();
					}
					
					$exception = new ModelNotQueryable();
					$exception->setModel($className);
					
					return $exception;
				});
		}
		
		return $this->types;
	}
	
	protected function getQueries()
	{
		if (!$this->queries) {
			$this->queries = Collection::make(Arr::get($this->config, 'queries', []))
				->mapWithKeys(function($className) {
					$query = new $className();
					return [$query->getName() => $query];
				});
		}
		
		return $this->queries;
	}
	
	/**
	 * Build a custom Schema for a given request
	 *
	 * @param Authorizable|null $actor
	 * @return Schema
	 */
	protected function schemaForActor($actor = null)
	{
		$types = $this->getAuthorizedTypes($actor);
		
		$queries = array_merge(
			$types
				->flatMap(function($type) {
					return method_exists($type, 'associatedQueries')
						? $type->associatedQueries()
						: [];
				})
				->map(function($query) {
					return $query->toArray();
				})
				->toArray(),
			$this->getAuthorizedQueries($actor)->toArray()
		);
		
		return new Schema([
			'query' => new ObjectType([
				'name' => 'Query',
				'fields' => $queries,
			]),
			'mutation' => null,
			'types' => $types->toArray(),
		]);
	}
	
	/**
	 * Build an array of authorized Types for a given user
	 *
	 * @param Authorizable|null $actor
	 * @return Collection
	 */
	protected function getAuthorizedTypes($actor)
	{
		$default = (bool) Arr::get($this->config, 'expose_types', false);
		$public = Arr::get($this->config, 'public_types', []);
		
		return $this->getTypes()
			->filter(function($type) use ($actor, $public, $default) {
				if (method_exists($type, 'authorizeGraphQL')) {
					return call_user_func([$type, 'authorizeGraphQL'], $actor, 'expose');
				}
				
				if ($actor) {
					$gate = $this->gate->forUser($actor);
					$policy = $gate->getPolicyFor($type);
					if (($policy && method_exists($policy, 'expose')) || $gate->has('expose')) {
						return $gate->allows('expose', $type);
					}
				}
				
				if (in_array(get_class($type), $public)) {
					return true;
				}
				
				return $default;
			});
	}
	
	/**
	 * @param $actor
	 * @return Collection|Query[]
	 */
	protected function getAuthorizedQueries($actor)
	{
		$default = (bool) Arr::get($this->config, 'expose_queries', false);
		$public = Arr::get($this->config, 'public_queries', []);
		
		return $this->getQueries()
			->filter(function(Query $query) use ($actor, $public, $default) {
				if (method_exists($query, 'authorize')) {
					return call_user_func([$query, 'authorize'], $actor);
				}
				
				if (in_array(get_class($query), $public)) {
					return true;
				}
				
				return $default;
			});
	}
}
