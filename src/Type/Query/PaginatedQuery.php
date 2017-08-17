<?php

namespace Galahad\Graphoquent\Type\Query;

use Galahad\Graphoquent\Exception\NotPaginated;
use Galahad\Graphoquent\Type\Type;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

abstract class PaginatedQuery extends EloquentQuery
{
	protected $perPage = 100;
	
	public function getReturnName()
	{
		return $this->getName();
	}
	
	public function getType()
	{
		return new ObjectType([
			'name' => $this->getReturnName(),
			'description' => $this->getDescription(),
			'fields' => [
				'data' => [
					'name' => 'data', //Str::plural($this->getModelName()),
					'type' => $this->type(),
				],
				'pagination' => [
					'name' => 'pagination',
					'type' => Type::pagination(),
				],
			],
		]);
	}
	
	public function getArgs()
	{
		return array_merge([
			'page' => [
				'name' => 'page',
				'description' => 'Page to load',
				'type' => Type::int(),
			],
			'perPage' => [
				'name' => 'perPage',
				'description' => "Number or records to load per page (default: {$this->perPage})",
				'type' => Type::int(),
			],
		], $this->args());
	}
	
	public function getResolver()
	{
		return function($value, $args, $context = null, ResolveInfo $info = null) {
			// Set argument defaults
			$args = array_merge([
				'page' => 1,
				'perPage' => $this->perPage,
			], $args);
			
			$resolved = $this->resolve($value, $args, $context, $info);
			
			if (!$resolved instanceof LengthAwarePaginator) {
				throw new NotPaginated(static::class.'::resolve must return a LengthAwarePaginator');
			}
			
			return [
				'data' => $resolved->items(),
				'pagination' => [
					'total' => $resolved->total(),
					'per_page' => $resolved->perPage(),
					'current_page' => $resolved->currentPage(),
					'last_page' => $resolved->lastPage(),
				],
			];
		};
	}
}
