<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        Validator::extend('extension', 'App\Validators\ExtensionValidator@extension');
		
		\Response::macro('attachment', function ($content) {

			$headers = [
				'Content-type'        => 'application/pdf',
				'Content-Disposition' => 'attachment; filename="' . date('Ymd') . '-UBI-.pdf"',
			];

			return \Response::make($content, 200, $headers);

		});
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
