<?php

namespace Galahad\Graphoquent;

use Galahad\Graphoquent\Exception\ModelNotQueryable;
use Galahad\Graphoquent\Http\Request;
use GraphQL\Executor\ExecutionResult;
use GraphQL\GraphQL as BaseGraphQL;
use GraphQL\Schema;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class GraphQL
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
	 * @var Type[]
	 */
	protected $types;
	
	/**
	 * Constructor
	 *
	 * @param Gate $gate
	 * @param array $config
	 */
	public function __construct(Gate $gate, array $config)
	{
		$this->gate = $gate;
		$this->types = new Collection(Arr::get($config, 'types', []));
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
	
	/**
	 * Build a custom Schema for a given request
	 *
	 * @param Authorizable|null $actor
	 * @return Schema
	 */
	protected function schemaForActor($actor = null)
	{
		$types = $this->getAuthorizedTypes($actor);
		
		$queries = $types
			->flatMap(function($type) {
				return method_exists($type, 'associatedQueries')
					? $type->associatedQueries()
					: [];
			})
			->map(function($query) {
				return $query->toArray();
			})
			->toArray();
		
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
		
		return $this->types
			->filter(function($type) use ($actor, $public, $default) {
				if (method_exists($type, 'authorizeGraphQL')) {
					return call_user_func([new $type(), 'authorizeGraphQL'], $actor, 'expose');
				}
				
				if ($actor) {
					$gate = $this->gate->forUser($actor);
					$policy = $gate->getPolicyFor($type);
					if (($policy && method_exists($policy, 'expose')) || $gate->has('expose')) {
						return $gate->allows('expose', $type);
					}
				}
				
				if (in_array($type, $public)) {
					return true;
				}
				
				return $default;
			})
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
}
