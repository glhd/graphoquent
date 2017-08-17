<?php

namespace Galahad\Graphoquent\Tests\Http;

use Galahad\Graphoquent\GraphQL;
use Galahad\Graphoquent\Http\Controller;
use Galahad\Graphoquent\Tests\Stubs\Model;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

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
		
		$controller = new Controller($this->graphQL());
		
		$result = $controller->handleRequest($request);
		dd($result);
	}
	
	protected function graphQL()
	{
		return new GraphQL([
			'types' => [Model::class],
		]);
	}
}
