<?php

namespace Galahad\Graphoquent\Http;

use Galahad\Graphoquent\Graphoquent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Routing\Controller as IlluminateController;

class Controller extends IlluminateController
{
	/**
	 * @var Graphoquent
	 */
	protected $graphoquent;
	
	/**
	 * Constructor
	 *
	 * @param Graphoquent $graphoquent
	 */
	public function __construct(Graphoquent $graphoquent)
	{
		$this->graphoquent = $graphoquent;
	}
	
	public function handleRequest(HttpRequest $request)
	{
		$result = $this->graphoquent->executeForRequest(new Request($request));
		
		return new JsonResponse($result);
	}
	
	public function graphiql()
	{
		return view('graphoquent::graphiql');
	}
}
