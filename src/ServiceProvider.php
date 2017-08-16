<?php

namespace Galahad\Graphoquent;

use Illuminate\Support\ServiceProvider as IlluminateProvider;

class ServiceProvider extends IlluminateProvider
{
	public function register()
	{
		$this->app->singleton('graphql', function ($app) {
			return new GraphQL();
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
}
