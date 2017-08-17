<?php

namespace Galahad\Graphoquent\Type\Query;

use Galahad\Graphoquent\Type\Type;

class WhereIntClauseType extends WhereClauseType
{
	protected static function valueType()
	{
		return Type::int();
	}
}
