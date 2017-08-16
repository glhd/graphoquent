<?php

namespace Galahad\Graphoquent\Tests\Type\Query;

use Galahad\Graphoquent\GraphQL;
use Galahad\Graphoquent\Tests\Stubs\Model;
use Galahad\Graphoquent\Type\Query\FindQuery;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Mockery as m;
use Mockery\ExpectationInterface as Expectation;
use PHPUnit\Framework\TestCase;

class EloquentQueryTest extends TestCase
{
	public function testFindQuery()
	{
		$expected = $this->model();
		
		$model = $this->mockModel('find', function(Expectation $expectation) use ($expected) {
			$expectation->with(1)->andReturn($expected);
		});
		
		$find = new FindQuery($this->graphQL(), $model);
		$actual = $find->resolve(null, ['id' => $expected->id]);
		
		$this->assertSame($expected, $actual);
	}
	
	protected function graphQL()
	{
		return new GraphQL([
			'types' => [Model::class],
		]);
	}
	
	protected function model()
	{
		return new Model([
			'id' => 1,
			'foo' => 'bar',
		]);
	}
	
	protected function mockModel($builderMethod, \Closure $builderSetup)
	{
		$query = m::mock(QueryBuilder::class);
		$query->shouldReceive('from');
		$query->shouldReceive('where');
		
		$builder = m::mock(EloquentBuilder::class."[$builderMethod]", [$query]);
		$builder->setModel(new Model());
		$builderSetup($builder->shouldReceive($builderMethod));
		
		$model = m::mock('Galahad\Graphoquent\Tests\Stubs\Model[newQuery]');
		$model->shouldReceive('newQuery')->andReturn($builder);
		
		return $model;
	}
}
