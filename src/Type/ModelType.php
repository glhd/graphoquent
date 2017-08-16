<?php

namespace Galahad\Graphoquent;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Model;
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
	 * Build GraphQL fields from a Model instance
	 *
	 * @param Model $model
	 * @return array
	 */
	protected static function buildFields(Model $model)
	{
		// TODO: id and dates and docblock
		
		$reflection = new ReflectionClass($model);
		
		return array_merge(
			static::buildFieldsFromAnnotations($model, $reflection),
			static::buildFieldsFromCasts($model, $reflection),
			static::buildFieldsFromDates($model, $reflection)
		);
	}
	
	/**
	 * Build array of fields from Model::$casts
	 *
	 * @param Model $model
	 * @param ReflectionClass $reflection
	 * @return array
	 */
	protected static function buildFieldsFromCasts(Model $model, ReflectionClass $reflection)
	{
		$property = $reflection->getProperty('casts');
		$property->setAccessible(true);
		
		if (empty($casts = $property->getValue($model))) {
			return [];
		}
		
		return (new Collection($casts))
			->map(static::class.'::mapTypeToGraphQL')
			->filter()
			->toArray();
	}
	
	/**
	 * Build array of fields from Model::$dates
	 *
	 * @param Model $model
	 * @param ReflectionClass $reflection
	 * @return array
	 */
	protected static function buildFieldsFromDates(Model $model, ReflectionClass $reflection)
	{
		$property = $reflection->getProperty('dates');
		$property->setAccessible(true);
		
		if (empty($dates = $property->getValue($model))) {
			return [];
		}
		
		return array_fill_keys($dates, Type::string());
	}
	
	/**
	 * Build array of fields from property and property-ready annotations
	 *
	 * @param Model $model
	 * @param ReflectionClass $reflection
	 * @return array
	 */
	protected static function buildFieldsFromAnnotations(Model $model, ReflectionClass $reflection)
	{
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
			->mapWithKeys(static::class.'::mapTagToGraphQL')
			->filter()
			->toArray();
	}
	
	/**
	 * Map a phpDocumentor Tag to a GraphQL tag
	 *
	 * @param Property|PropertyRead $tag
	 * @return array
	 */
	protected static function mapTagToGraphQL($tag)
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
	protected static function mapTypeToGraphQL($type)
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
	 * Constructor
	 *
	 * @param Model $model
	 */
	public function __construct(Model $model)
	{
		$this->model = $model;
		$config = [
			'fields' => static::buildFields($model),
		];
		
		parent::__construct($config);
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
