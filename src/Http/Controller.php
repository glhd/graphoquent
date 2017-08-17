<?php

namespace Galahad\Graphoquent\Http;

use Galahad\Graphoquent\GraphQL;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Routing\Controller as IlluminateController;

class Controller extends IlluminateController
{
	/**
	 * @var GraphQL
	 */
	protected $graphQL;
	
	/**
	 * Constructor
	 *
	 * @param GraphQL $graphQL
	 */
	public function __construct(GraphQL $graphQL)
	{
		$this->graphQL = $graphQL;
	}
	
	public function handleRequest(HttpRequest $request)
	{
		$result = $this->graphQL->executeForRequest(new Request($request));
		
		return new JsonResponse($result);
	}
}
