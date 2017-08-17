<?php

namespace Galahad\Graphoquent\Type\Query;

use Galahad\Graphoquent\Type\Type;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class GenericQuery extends EloquentQuery
{
	public function name()
	{
		return $this->getModelName().'Query';
	}
	
	public function args()
	{
		return [
			'where' => [
				'description' => 'Add a basic where clause to the query',
				'type' => Type::listOf(Type::nonNull(Type::queryWhereClause())),
			],
			'where_int' => [
				'description' => 'Add a where clause to the query, searching by an integer value',
				'type' => Type::listOf(Type::nonNull(Type::queryWhereIntClause())),
			],
		];
	}
	
	public function type()
	{
		$modelType = parent::type();
		return Type::listOf($modelType);
	}
	
	public function resolve($value, $args, $context = null, ResolveInfo $info = null)
	{
		$query = $this->model->newQuery();
		
		foreach (Arr::get($args, 'where', []) as $where) {
			$operator = isset($where['operator']) ? $where['operator'] : '=';
			$query->where($where['field'], $operator, $where['value']);
		}
		
		foreach (Arr::get($args, 'where_int', []) as $where) {
			$operator = isset($where['operator']) ? $where['operator'] : '=';
			$query->where($where['field'], $operator, (int) $where['value']);
		}
		
		return $query->get();
	}
	
	public function description()
	{
		return 'Search for '.Str::plural($this->getModelNameForDescription());
	}
}
