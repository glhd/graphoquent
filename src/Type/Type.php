<?php

namespace Galahad\Graphoquent\Type;

use Galahad\Graphoquent\Type\Query\OperatorType;
use Galahad\Graphoquent\Type\Query\WhereClauseType;
use Galahad\Graphoquent\Type\Query\WhereIntClauseType;
use GraphQL\Type\Definition\Type as BaseType;

class Type extends BaseType
{
	/**
	 * @var array
	 */
	protected static $graphoquentTypes = [];
	
	/**
	 * @return WhereClauseType
	 */
	public static function queryWhereClause()
	{
		return static::graphoquentType(WhereClauseType::class);
	}
	
	/**
	 * @return WhereIntClauseType
	 */
	public static function queryWhereIntClause()
	{
		return static::graphoquentType(WhereIntClauseType::class);
	}
	
	/**
	 * @return OperatorType
	 */
	public static function queryOperator()
	{
		return static::graphoquentType(OperatorType::class);
	}
	
	/**
	 * Load a singleton instance of a type
	 *
	 * @param $className
	 * @return mixed
	 */
	protected static function graphoquentType($className)
	{
		if (!isset(static::$graphoquentTypes[$className])) {
			static::$graphoquentTypes[$className] = new $className();
		}
		
		return static::$graphoquentTypes[$className];
	}
}
