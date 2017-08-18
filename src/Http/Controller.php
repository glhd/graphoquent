<?php

namespace Galahad\Graphoquent\Http;

use Galahad\Graphoquent\Graphoquent;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Routing\Controller as IlluminateController;
use Illuminate\Support\Facades\Auth;

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
	
	public function graphiql(Session $session)
	{
		return view('graphoquent::graphiql', [
			'token' => $session->token(),
		]);
	}
}
