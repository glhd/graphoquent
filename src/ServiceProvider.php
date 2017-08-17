<?php

namespace Galahad\Graphoquent;

use Galahad\Graphoquent\Http\Controller;
use Illuminate\Contracts\Container\Container;
use Illuminate\Routing\Router;
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
		$this->bootConfig();
		$this->bootViews();
		$this->bootRoutes();
	}
	
	/**
	 * Boot config file
	 */
	protected function bootConfig()
	{
		$configFile = __DIR__.'/../config/graphoquent.php';
		
		$this->mergeConfigFrom($configFile, 'graphoquent');
		$this->publishes([$configFile => config_path('graphoquent.php')], 'config');
	}
	
	/**
	 * Boot views
	 */
	protected function bootViews()
	{
		$viewPath = __DIR__.'/../views/';
		$publishPath = $this->app['config']->get('view.paths', ['resources/views'])[0];
		
		$this->loadViewsFrom($viewPath, 'graphoquent');
		$this->publishes([$viewPath => $publishPath.'/vendor/graphoquent'], 'views');
	}
	
	/**
	 * Boot routes
	 */
	protected function bootRoutes()
	{
		/** @var Router $router */
		$router = $this->app->make('router');
		
		$router->any('/graphql', Controller::class.'@handleRequest');
		$router->get('/graphiql', Controller::class.'@graphiql');
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
