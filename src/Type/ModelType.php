<?php

namespace Galahad\Graphoquent\Type;

use Galahad\Graphoquent\Queryable;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use phpDocumentor\Reflection\DocBlock\Tags\Property;
use phpDocumentor\Reflection\DocBlock\Tags\PropertyRead;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionClass;

/**
 * GraphQL Type representing an Eloquent Model
 *
 * @package Galahad\Graphoquent
 */
class ModelType extends ObjectType
{
	/**
	 * @var Model
	 */
	protected $model;
	
	/**
	 * @var array
	 */
	protected $queries;
	
	/**
	 * Map a phpDocumentor Tag to a GraphQL tag
	 *
	 * @param Property|PropertyRead $tag
	 * @return array
	 */
	public static function mapTagToGraphQL($tag)
	{
		$name = $tag->getVariableName();
		$type = static::mapTypeToGraphQL($tag->getType());
		
		return [$name => $type];
	}
	
	/**
	 * Map a type string to a GraphQL Type
	 *
	 * @param string $type
	 * @return Type
	 */
	public static function mapTypeToGraphQL($type)
	{
		$type = trim(strtolower($type));
		switch ($type) {
			case 'int':
			case 'integer':
				return Type::int();
			case 'real':
			case 'float':
			case 'double':
				return Type::float();
			case 'string':
				return Type::string();
			case 'bool':
			case 'boolean':
				return Type::boolean();
			case 'object':
				return null; // FIXME
			case 'array':
			case 'json':
				return null; // FIXME
			case 'collection':
				return null; // FIXME
			case 'date':
				return Type::string(); // TODO: Will this work?
			case 'datetime':
				return Type::string(); // TODO
			case 'timestamp':
				return Type::int();
			default:
				return null;
		}
	}
	
	/**
	 * Build GraphQL fields from a Model instance
	 *
	 * @param Model $model
	 * @return array
	 */
	protected static function buildFields(Model $model)
	{
		$fields = array_merge(
			static::buildFieldsFromAnnotations($model),
			static::buildFieldsFromCasts($model),
			static::buildFieldsFromDates($model)
		);
		
		if (!empty($visible = $model->getVisible())) {
			return Arr::only($fields, $visible);
		}
		
		if (!empty($hidden = $model->getHidden())) {
			return Arr::except($fields, $hidden);
		}
		
		return $fields;
	}
	
	/**
	 * Build array of fields from Model::$casts
	 *
	 * @param Model $model
	 * @return array
	 */
	protected static function buildFieldsFromCasts(Model $model)
	{
		if (empty($casts = $model->getCasts())) {
			return [];
		}
		
		return (new Collection($casts))
			->map([static::class, 'mapTypeToGraphQL'])
			->filter()
			->toArray();
	}
	
	/**
	 * Build array of fields from Model::$dates
	 *
	 * @param Model $model
	 * @return array
	 */
	protected static function buildFieldsFromDates(Model $model)
	{
		return array_fill_keys($model->getDates(), Type::string());
	}
	
	/**
	 * Build array of fields from property and property-ready annotations
	 *
	 * @param Model $model
	 * @return array
	 */
	protected static function buildFieldsFromAnnotations(Model $model)
	{
		$reflection = new ReflectionClass($model);
		$docblock = $reflection->getDocComment();
		if (empty($docblock) || false === stripos($docblock, 'property')) {
			return [];
		}
		
		$parsed = DocBlockFactory::createInstance()->create($docblock);
		$properties = new Collection(array_merge(
			$parsed->getTagsByName('property'),
			$parsed->getTagsByName('property-read')
		));
		
		return $properties
			->mapWithKeys([static::class, 'mapTagToGraphQL'])
			->filter()
			->toArray();
	}
	
	/**
	 * Get list of hidden fields
	 *
	 * @param Model $model
	 * @param ReflectionClass $reflection
	 * @return array
	 */
	protected static function getHiddenFields(Model $model, ReflectionClass $reflection)
	{
		$property = $reflection->getProperty('hidden');
		$property->setAccessible(true);
		
		return (array) $property->getValue($model);
	}
	
	/**
	 * Constructor
	 *
	 * @param Model|Queryable $model
	 */
	public function __construct(Model $model)
	{
		$this->model = $model;
		$config = [
			'fields' => static::buildFields($model),
		];
		
		parent::__construct($config);
		
		$this->queries = method_exists($model, 'toGraphQLQueries')
			? $model->toGraphQLQueries()
			: [];
	}
	
	public function getModel()
	{
		return $this->model;
	}
	
	/**
	 * Get queries associated with this type
	 *
	 * @return array
	 */
	public function associatedQueries()
	{
		return $this->queries;
	}
	
	/**
	 * Infer Type name from Model
	 *
	 * @return string
	 */
	protected function tryInferName()
	{
		return class_basename(get_class($this->model));
	}
}
