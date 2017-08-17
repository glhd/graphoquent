<?php

namespace Galahad\Graphoquent\Type\Query;

use Galahad\Graphoquent\Type\Type;
use GraphQL\Type\Definition\ObjectType;

class PaginationType extends ObjectType
{
	public function __construct()
	{
		parent::__construct([
			'name' => 'Pagination',
			'description' => 'Pagination data',
			'fields' => [
				'total' => [
					'type' => Type::nonNull(Type::int()),
					'name' => 'total',
					'description' => 'Total number of results',
				],
				'per_page' => [
					'type' => Type::nonNull(Type::int()),
					'name' => 'per_page',
					'description' => 'Total number of results',
				],
				'current_page' => [
					'type' => Type::nonNull(Type::int()),
					'name' => 'current_page',
					'description' => 'Total number of results',
				],
				'last_page' => [
					'type' => Type::nonNull(Type::int()),
					'name' => 'last_page',
					'description' => 'Total number of results',
				],
			],
		]);
	}
}
