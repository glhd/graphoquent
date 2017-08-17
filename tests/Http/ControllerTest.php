<?php

namespace Galahad\Graphoquent\Tests\Http;

use Galahad\Graphoquent\Graphoquent;
use Galahad\Graphoquent\Http\Controller;
use Galahad\Graphoquent\Tests\Stubs\Model;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;
use Mockery as m;

class ControllerTest extends TestCase
{
	public function testValidQuery()
	{
		$gql = '
			query FindModelById($id: Int) {
				FindModel(id: $id) {
					id
                }
            }
		';
		$request = Request::create('/graphql', 'POST');
		$request->request->set('query', $gql);
		$request->request->set('variables', json_encode(['id' => 1]));
		
		$controller = new Controller($this->graphoquent());
		
		$result = $controller->handleRequest($request);
		$this->assertNotNull($result); // FIXME
	}
	
	protected function graphoquent()
	{
		$gate = m::mock(Gate::class);
		return new Graphoquent($gate, [
			'types' => [Model::class],
		]);
	}
}
