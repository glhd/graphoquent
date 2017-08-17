<?php

namespace Galahad\Graphoquent;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider as IlluminateProvider;
use Illuminate\Contracts\Auth\Access\Gate;

class ServiceProvider extends IlluminateProvider
{
	/**
	 * Register GraphQL to the service container
	 */
	public function register()
	{
		$this->app->singleton('graphql', function(Container $app) {
			return new GraphQL(
				$app->make(Gate::class),
				$app->make('config')->get('graphoquent')
			);
		});
		
		$this->app->alias('graphql', GraphQL::class);
	}
	
	/**
	 * Boot config
	 */
	public function boot()
	{
		$this->mergeConfigFrom(
			__DIR__.'/../config/graphoquent.php',
			'graphoquent'
		);
	}
	
	/**
	 * Get the services provided by the provider
	 *
	 * @return array
	 */
	public function provides()
	{
		return [
			'graphql',
			GraphQL::class,
		];
	}
}
