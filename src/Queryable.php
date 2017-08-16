<?php

namespace Galahad\Graphoquent;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/**
 * Trait Queryable
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait Queryable
{
	/**
	 * GraphQL Type that represents this model
	 *
	 * @var ModelType|ObjectType|Type
	 */
	protected static $graphQLType;
	
	/**
	 * Get the GraphQL Type for this model
	 *
	 * @return ModelType|ObjectType|Type
	 */
	public static function getGraphQLType()
	{
		if (null === static::$graphQLType) {
			static::$graphQLType = static::toGraphQLType();
		}
		
		return static::$graphQLType;
	}
	
	/**
	 * Create a GraphQL Type that represents this model
	 *
	 * @return ModelType|ObjectType|Type
	 */
	public static function toGraphQLType()
	{
		return new ModelType(new static());
	}
}
