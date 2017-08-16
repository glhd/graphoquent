<?php

namespace Galahad\Graphoquent\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * GraphQL Facade
 *
 * @package Galahad\Graphoquent\Facades
 */
class GraphQL extends Facade
{
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return 'graphql';
	}
}
