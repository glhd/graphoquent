<?php

namespace Galahad\Graphoquent\Type\Query;

use Galahad\Graphoquent\Type\Type;
use GraphQL\Type\Definition\EnumType;

class WhereClauseType extends EnumType
{
	public function __construct()
	{
		parent::__construct([
			'name' => 'QueryWhereClause',
			'fields' => [
				'field' => [
					'type' => Type::nonNull(Type::string()),
					'description' => 'The field to refine the query based on',
				],
				'operator' => [
					'type' => Type::queryOperator(),
					'description' => 'The field to refine the query based on',
				],
				'value' => [
					'type' => Type::nonNull(static::valueType()),
					'description' => 'The value to search for',
				],
			],
		]);
	}
	
	protected static function valueType()
	{
		return Type::string();
	}
}
