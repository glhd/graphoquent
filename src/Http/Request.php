<?php

namespace Galahad\Graphoquent\Http;

use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Arr;

class Request
{
	/**
	 * @var HttpRequest
	 */
	protected $httpRequest;
	
	/**
	 * @var string
	 */
	protected $requestString;
	
	/**
	 * @var string
	 */
	protected $operation;
	
	/**
	 * @var array
	 */
	protected $variables;
	
	/**
	 * @var mixed
	 */
	protected $root;
	
	/**
	 * @var mixed
	 */
	protected $context;
	
	/**
	 * @var Authorizable|mixed
	 */
	protected $actor;
	
	/**
	 * Constructor
	 *
	 * @param HttpRequest $request
	 */
	public function __construct(HttpRequest $request)
	{
		$this->setHttpRequest($request);
		
		$input = $request->input();
		
		$this->setRequestString(Arr::get($input, 'query'));
		$this->setOperation(Arr::get($input, 'operation'));
		$this->setVariables(Arr::get($input, 'variables'));
		
		try {
			if ($actor = $request->user('api')) {
				$this->setActor($actor);
			}
		} catch (\Exception $exception) {
		}
		
		$this->setActor($request->user());
	}
	
	/**
	 * @return HttpRequest
	 */
	public function getHttpRequest()
	{
		return $this->httpRequest;
	}
	
	/**
	 * @param HttpRequest $request
	 * @return $this
	 */
	public function setHttpRequest($request)
	{
		$this->httpRequest = $request;
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getRequestString()
	{
		return $this->requestString;
	}
	
	/**
	 * @param string $requestString
	 * @return $this
	 */
	public function setRequestString($requestString)
	{
		$this->requestString = $requestString;
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getOperation()
	{
		return $this->operation;
	}
	
	/**
	 * @param string $operation
	 * @return $this
	 */
	public function setOperation($operation)
	{
		$this->operation = $operation;
		return $this;
	}
	
	/**
	 * @return array
	 */
	public function getVariables()
	{
		return $this->variables;
	}
	
	/**
	 * @param array|string $variables
	 * @return $this
	 */
	public function setVariables($variables)
	{
		if (is_string($variables)) {
			$decoded = json_decode($variables, true);
			if (null !== $decoded) {
				$variables = $decoded;
			}
		}
		
		$this->variables = $variables;
		return $this;
	}
	
	/**
	 * @return mixed
	 */
	public function getRoot()
	{
		return $this->root;
	}
	
	/**
	 * @param mixed $root
	 * @return $this
	 */
	public function setRoot($root)
	{
		$this->root = $root;
		return $this;
	}
	
	/**
	 * @return mixed
	 */
	public function getContext()
	{
		if (!$this->context) {
			$this->context = [
				'actor' => $this->getActor(),
			];
		}
		
		return $this->context;
	}
	
	/**
	 * @param mixed $context
	 * @return $this
	 */
	public function setContext($context)
	{
		$this->context = $context;
		return $this;
	}
	
	/**
	 * @return Authorizable|mixed
	 */
	public function getActor()
	{
		return $this->actor;
	}
	
	/**
	 * @param Authorizable|mixed $actor
	 * @return $this
	 */
	public function setActor($actor)
	{
		$this->actor = $actor;
		return $this;
	}
	
}
