<?php

return [
	
	/*
	|--------------------------------------------------------------------------
	| GraphQL Types
	|--------------------------------------------------------------------------
	|
	| List all GraphQL types, either Models implementing Queryable or explicitly
	| defined GraphQL types here.
	|
	*/
	
	'types' => [
		//
	],
	
	/*
	|--------------------------------------------------------------------------
	| Default Type Visibility
	|--------------------------------------------------------------------------
	|
	| When set to true, all registered types will be exposed to
	| introspection requests (see http://graphql.org/learn/introspection/).
	|
	| When set to false, all types will be hidden unless explicitly overridden.
	|
	| Can be overridden with a Policy's expose() method or with the
	| Model's authorizeGraphQL() method.
	|
	*/
	
	'expose_types' => false,
	
];
