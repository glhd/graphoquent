<?php

namespace Galahad\Graphoquent;

use GraphQL\Schema;
use GraphQL\Type\Definition\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class GraphQL
{
	/**
	 * @var array
	 */
	protected $config;
	
	/**
	 * @var Type[]
	 */
	protected $types;
	
	/**
	 * @var array
	 */
	protected $policies;
	
	/**
	 * Constructor
	 *
	 * @param array $config
	 */
	public function __construct(array $config)
	{
		$this->types = new Collection(Arr::get($config, 'types', []));
		$this->policies = Arr::get($config, 'policies', []);
		
		$this->config = Arr::except($config, ['types', 'policies']);
	}
	
	/**
	 * Build a custom Schema for a given request
	 *
	 * @param Request $request
	 * @return Schema
	 */
	public function schemaForRequest(Request $request)
	{
		return new Schema([
			'query' => null,
			'mutation' => null,
			'types' => $this->getAuthorizedTypes($request->user()),
		]);
	}
	
	/**
	 * Build an array of authorized Types for a given user
	 *
	 * @param mixed $actor
	 * @return array
	 */
	protected function getAuthorizedTypes($actor)
	{
		return $this->types->filter(function($type) use ($actor) {
			if ($policy = $this->getPolicy($type)) {
				if (!method_exists($policy, 'view')) {
					return false;
				}
				
				return $policy->view($actor, $type);
			}
			
			if (method_exists($type, 'authorizeGraphQL')) {
				return $type->authorizeGraphQL($actor, 'view');
			}
			
			return false;
		})->toArray();
	}
	
	/**
	 * Load a Policy for a given Type
	 *
	 * @param $type
	 * @return mixed|null
	 */
	protected function getPolicy($type)
	{
		$className = get_class($type);
		
		if (isset($this->policies[$className])) {
			return $this->policies[$className];
		}
		
		return null;
	}
}
