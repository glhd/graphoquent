<?php

namespace Galahad\Graphoquent\Type\Query;

use GraphQL\Type\Definition\EnumType;

class OperatorType extends EnumType
{
	public function __construct()
	{
		parent::__construct([
			'name' => 'QueryOperator',
			'description' => 'One of the films in the Star Wars Trilogy',
			'values' => [
				'NEWHOPE' => [
					'value' => 4,
					'description' => 'Released in 1977.'
				],
				'EMPIRE' => [
					'value' => 5,
					'description' => 'Released in 1980.'
				],
				'JEDI' => [
					'value' => 6,
					'description' => 'Released in 1983.'
				],
			]
		]);
	}
}
