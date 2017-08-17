<?php

namespace Galahad\Graphoquent\Type\Query;

use GraphQL\Type\Definition\ResolveInfo;

abstract class Query
{
	abstract public function name();
	
	abstract public function type();
	
	abstract public function args();
	
	abstract public function resolve($value, $args, $context = null, ResolveInfo $info = null);
	
	public function description()
	{
		return null;
	}
	
	public function deprecationReason()
	{
		return null;
	}
	
	public function toArray()
	{
		$query = [
			'name' => $this->name(),
			'type' => $this->type(),
			'resolve' => function($value, $args, $context = null, ResolveInfo $info = null) {
				return $this->resolve($value, $args, $context, $info);
			}
		];
		
		if ($args = $this->args()) {
			$query['args'] = $args;
		}
		
		if ($description = $this->description()) {
			$query['description'] = $description;
		}
		
		if ($deprecationReason = $this->deprecationReason()) {
			$query['deprecationReason'] = $deprecationReason;
		}
		
		return $query;
	}
}
