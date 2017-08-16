<?php

namespace Galahad\Graphoquent\Tests;

use Galahad\Graphoquent\Tests\Stubs\Model;
use Galahad\Graphoquent\Type\ModelType;
use GraphQL\Type\Definition as Defs;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use PHPUnit\Framework\TestCase;

class TypeInferenceTest extends TestCase
{
	public function testCastsAreConvertedToTypes()
	{
		$this->assertTypeDefinitions(
			new ModelType(new Model()),
			[
				'cast_int' => Defs\IntType::class,
				'cast_integer' => Defs\IntType::class,
				'cast_real' => Defs\FloatType::class,
				'cast_float' => Defs\FloatType::class,
				'cast_double' => Defs\FloatType::class,
				'cast_string' => Defs\StringType::class,
				'cast_bool' => Defs\BooleanType::class,
				'cast_boolean' => Defs\BooleanType::class,
				'cast_date' => Defs\StringType::class,
				'cast_datetime' => Defs\StringType::class,
				'cast_timestamp' => Defs\IntType::class,
			]
		);
	}
	
	public function testDatesAreConvertedToTypes()
	{
		$this->assertTypeDefinitions(
			new ModelType(new Model()),
			[
				EloquentModel::CREATED_AT => Defs\StringType::class,
				EloquentModel::UPDATED_AT => Defs\StringType::class,
			]
		);
	}
	
	public function testAnnotationsAreConvertedToTypes()
	{
		$this->assertTypeDefinitions(
			new ModelType(new Model()),
			[
				'property_int' => Defs\IntType::class,
				'property_integer' => Defs\IntType::class,
				'property_float' => Defs\FloatType::class,
				'property_double' => Defs\FloatType::class,
				'property_string' => Defs\StringType::class,
				'property_bool' => Defs\BooleanType::class,
				'property_boolean' => Defs\BooleanType::class,
				'property_read_int' => Defs\IntType::class,
				'property_read_integer' => Defs\IntType::class,
				'property_read_float' => Defs\FloatType::class,
				'property_read_double' => Defs\FloatType::class,
				'property_read_string' => Defs\StringType::class,
				'property_read_bool' => Defs\BooleanType::class,
				'property_read_boolean' => Defs\BooleanType::class,
			]
		);
	}
	
	protected function assertTypeDefinitions(ModelType $modelType, array $expected) {
		foreach ($expected as $key => $expectedClass) {
			$field = $modelType->getField($key);
			$this->assertInstanceOf($expectedClass, $field->getType());
		}
	}
}
