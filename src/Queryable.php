<?php

namespace Galahad\Graphoquent;

use Galahad\Graphoquent\Type\ModelType;
use Galahad\Graphoquent\Type\Query\AllQuery;
use Galahad\Graphoquent\Type\Query\FindQuery;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Collection;

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
	
	/**
	 * Create an array of queries associated with this model
	 *
	 * @return array
	 */
	public function toGraphQLQueries()
	{
		$queries = [
			FindQuery::class,
			AllQuery::class,
		];
		
		return (new Collection($queries))
			->mapWithKeys(function($className) {
				$query = new $className(static::class);
				return [$query->getName() => $query];
			})
			->toArray();
	}
}
