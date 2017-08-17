<?php

namespace Galahad\Graphoquent\Type\Query;

use GraphQL\Type\Definition\ResolveInfo;

abstract class Query
{
	public function getName()
	{
		return $this->name();
	}
	
	abstract public function name();
	
	public function getType()
	{
		return $this->type();
	}
	
	abstract public function type();
	
	public function getArgs()
	{
		return $this->args();
	}
	
	abstract public function args();
	
	public function getResolver()
	{
		return function($value, $args, $context = null, ResolveInfo $info = null) {
			return $this->resolve($value, $args, $context, $info);
		};
	}
	
	abstract public function resolve($value, $args, $context = null, ResolveInfo $info = null);
	
	public function getDescription()
	{
		return $this->description();
	}
	
	public function description()
	{
		return null;
	}
	
	public function getDeprecationReason()
	{
		return $this->deprecationReason();
	}
	
	public function deprecationReason()
	{
		return null;
	}
	
	public function toArray()
	{
		$query = [
			'name' => $this->getName(),
			'type' => $this->getType(),
			'resolve' => $this->getResolver(),
		];
		
		if ($args = $this->getArgs()) {
			$query['args'] = $args;
		}
		
		if ($description = $this->getDescription()) {
			$query['description'] = $description;
		}
		
		if ($deprecationReason = $this->getDeprecationReason()) {
			$query['deprecationReason'] = $deprecationReason;
		}
		
		return $query;
	}
}
