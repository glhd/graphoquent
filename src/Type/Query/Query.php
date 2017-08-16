<?php

namespace Galahad\Graphoquent\Type\Query;

use GraphQL\Type\Definition\ResolveInfo;

abstract class Query
{
	abstract public function name();
	
	abstract public function type();
	
	abstract public function args();
	
	abstract public function resolve($value, $args, $context, ResolveInfo $info);
	
	public function description()
	{
		return null;
	}
}
