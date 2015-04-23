<?php namespace Suroviy\Xcore;

use Illuminate\Support\ServiceProvider;

class XcoreServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
            $this->publishes([
                __DIR__.'/../migrations/' => base_path('/database/migrations'),
                __DIR__.'/../app/' => base_path('/app')
            ]);
	}
        // ---
	/**
	 * Register the application services. 
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}
        

}
