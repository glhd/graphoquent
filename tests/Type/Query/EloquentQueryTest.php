<?php

namespace Galahad\Graphoquent\Tests\Type\Query;

use Galahad\Graphoquent\GraphQL;
use Galahad\Graphoquent\Tests\Stubs\Model;
use Galahad\Graphoquent\Type\Query\FindQuery;
use Galahad\Graphoquent\Type\Query\GenericQuery;
use GraphQL\Language\Parser;
use GraphQL\Language\Source;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Mockery as m;
use Mockery\MockInterface as Mock;
use PHPUnit\Framework\TestCase;

class EloquentQueryTest extends TestCase
{
	public function testFindQuery()
	{
		$expected = $this->model();
		
		$model = $this->mockModel('find', function(Mock $mock) use ($expected) {
			$mock->shouldReceive('find')->with(1)->andReturn($expected);
		});
		
		$find = new FindQuery($this->graphQL(), $model);
		$actual = $find->resolve(null, ['id' => $expected->id]);
		
		$this->assertSame($expected, $actual);
	}
	
	public function testGenericQuery()
	{
		$expected = new Collection([$this->model()]);
		
		$model = $this->mockModel('get', function(Mock $mock) use ($expected) {
			$mock->shouldReceive('where')->with('foo', '=', 'bar')->once()->andReturnSelf();
			$mock->shouldReceive('get')->once()->andReturn($expected);
		});
		
		$query = new GenericQuery($this->graphQL(), $model);
		$actual = $query->resolve(null, [
			'where' => [[
				'field' => 'foo',
				'value' => 'bar',
			]]
		]);
		
		$this->assertSame($expected, $actual);
	}
	
	protected function query($query)
	{
		// TODO:
		$source = new Source($query);
		$documentNode = Parser::parse($source);
		return $documentNode;
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
		$builderSetup($builder);
		
		$model = m::mock('Galahad\Graphoquent\Tests\Stubs\Model[newQuery]');
		$model->shouldReceive('newQuery')->andReturn($builder);
		
		return $model;
	}
}
