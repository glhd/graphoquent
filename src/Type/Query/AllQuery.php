<?php

namespace Galahad\Graphoquent\Type\Query;

use Galahad\Graphoquent\Type\Type;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Str;

class AllQuery extends PaginatedQuery
{
	public function name()
	{
		return 'all'.Str::plural($this->getModelName());
	}
	
	public function getReturnName()
	{
		return 'All'.Str::plural($this->getModelName());
	}
	
	public function type()
	{
		return Type::listOf(parent::type());
	}
	
	public function args()
	{
		return [];
	}
	
	public function resolve($value, $args, $context = null, ResolveInfo $info = null)
	{
		$query = forward_static_call([$this->className, 'query']);
		$result = $query->paginate((int) $args['perPage'], ['*'], 'page', (int) $args['page']);
		return $result;
	}
	
	public function description()
	{
		return 'Find all '.Str::plural($this->getModelNameForDescription());
	}
}
