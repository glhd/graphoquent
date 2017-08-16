<?php

namespace Galahad\Graphoquent\Tests\Stubs;

use Illuminate\Database\Eloquent\Model as EloquentModel;

/**
 * Class Model
 *
 * @property int $property_int
 * @property integer $property_integer
 * @property float $property_float
 * @property double $property_double
 * @property string $property_string
 * @property bool $property_bool
 * @property boolean $property_boolean
 * @property-read int $property_read_int
 * @property-read integer $property_read_integer
 * @property-read float $property_read_float
 * @property-read double $property_read_double
 * @property-read string $property_read_string
 * @property-read bool $property_read_bool
 * @property-read boolean $property_read_boolean
 */
class Model extends EloquentModel
{
	protected $casts = [
		'cast_int' => 'int',
		'cast_integer' => 'integer',
		'cast_real' => 'real',
		'cast_float' => 'float',
		'cast_double' => 'double',
		'cast_string' => 'string',
		'cast_bool' => 'bool',
		'cast_boolean' => 'boolean',
		'cast_date' => 'date',
		'cast_datetime' => 'datetime',
		'cast_timestamp' => 'timestamp',
	];
}
