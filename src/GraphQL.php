<?php

namespace Galahad\Graphoquent;

use Galahad\Graphoquent\Http\Request;
use GraphQL\Executor\ExecutionResult;
use GraphQL\GraphQL as BaseGraphQL;
use GraphQL\Schema;
use GraphQL\Type\Definition\Type;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Auth\Access\Gate;

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
		$this->types = new Collection(Arr::get($config, 'types', []));
		$this->config = Arr::except($config, ['types', 'policies']);
		$this->gate = $gate;
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
		return new Schema([
			'query' => null,
			'mutation' => null,
			'types' => $this->getAuthorizedTypes($actor),
		]);
	}
	
	/**
	 * Build an array of authorized Types for a given user
	 *
	 * @param Authorizable|null $actor
	 * @return array
	 */
	protected function getAuthorizedTypes($actor)
	{
		$default = (bool) Arr::get($this->config, 'expose_types', false);
		$gate = null === $actor
			? $this->gate
			: $this->gate->forUser($actor);
		
		return $this->types->filter(function($type) use ($actor, $gate, $default) {
			if (method_exists($type, 'authorizeGraphQL')) {
				return call_user_func([new $type(), 'authorizeGraphQL'], $actor, 'expose');
			}
			
			if ($gate->getPolicyFor($type) || $gate->has('expose')) {
				return $gate->allows('expose', $type);
			}
			
			return $default;
		})->toArray();
	}
}
