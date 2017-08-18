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
	| GraphQL Queries
	|--------------------------------------------------------------------------
	|
	| Models implementing Queryable will automatically register queries for
	| common Eloquent methods (find, all, etc). Register any additional
	| custom queries here.
	|
	*/
	
	'queries' => [
		//
	],
	
	/*
	|--------------------------------------------------------------------------
	| GraphQL Mutations
	|--------------------------------------------------------------------------
	|
	| Models implementing Queryable will automatically register mutations for
	| common Eloquent methods (create, destroy, etc). Register any additional
	| custom mutations here.
	|
	*/
	
	'mutations' => [
		//
	],
	
	/*
	|--------------------------------------------------------------------------
	| Visibility Settings
	|--------------------------------------------------------------------------
	|
	| When expose_* is set to true, all registered types/queries/mutations will
	| be exposed for introspection (http://graphql.org/learn/introspection/)
	|
	| When set to false, all they will be hidden unless explicitly overridden
	| via a Model's authorizeGraphQL() method (in the case of a Model-based
	| Type) or a Gate/Policy (for types, queries, and mutations).
	|
	| To make a type/query/mutation visible to unauthenticated requests, add it
	| to the public_* array. These will always be visible to introspection.
	|
	*/
	
	'expose_types' => false,
	
	'public_types' => [
		//
	],
	
	'expose_queries' => false,
	
	'public_queries' => [
		//
	],
	
	'expose_mutations' => false,
	
	'public_mutations' => [
		//
	],
	
	/*
	|--------------------------------------------------------------------------
	| Http Settings
	|--------------------------------------------------------------------------
	|
	| Choose what middleware or middleware group to run GraphQL requests thru.
	|
	*/
	
	'middleware' => [
		'api'
	],
	
	/*
	|--------------------------------------------------------------------------
	| GraphiQL (GUI) Settings
	|--------------------------------------------------------------------------
	|
	|
	*/
	
	'graphiql' => [
		'enabled' => true,
		'middleware' => [
			'web'
		]
	],
	
];
