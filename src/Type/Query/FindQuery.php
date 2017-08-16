<?php

namespace Galahad\Graphoquent\Type\Query;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;

class FindQuery extends EloquentQuery
{
	public function name()
	{
		return 'Find'.$this->getModelName().'Query';
	}
	
	public function args()
	{
		$id = $this->model->getKeyName();
		return [
			$id => [
				'description' => 'The identifier for a given '.$this->getModelNameForDescription(),
				'type' => Type::nonNull(Type::int()),
			],
		];
	}
	
	public function resolve($value, $args, $context = null, ResolveInfo $info = null)
	{
		$id = $args[$this->model->getKeyName()];
		return $this->model->newQuery()->find($id);
	}
	
	public function description()
	{
		return 'Find a specific '.$this->getModelNameForDescription();
	}
}
