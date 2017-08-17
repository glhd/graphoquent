<?php

namespace Galahad\Graphoquent\Type\Query;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;

class FindQuery extends EloquentQuery
{
	public function name()
	{
		return 'find'.$this->getModelName();
	}
	
	public function args()
	{
		$id = (new $this->className())->getKeyName();
		return [
			$id => [
				'description' => 'The identifier for a given '.$this->getModelNameForDescription(),
				'type' => Type::nonNull(Type::int()),
			],
		];
	}
	
	public function resolve($value, $args, $context = null, ResolveInfo $info = null)
	{
		$model = new $this->className();
		$id = $args[$model->getKeyName()];
		return ($found = $model->newQuery()->find($id))
			? $found->toArray()
			: null;
	}
	
	public function description()
	{
		return 'Find a specific '.$this->getModelNameForDescription();
	}
}
