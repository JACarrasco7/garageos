<?php

namespace App\Modules\Documents\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class DocumentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . '/../Routes/documents.php');
        });

        // Listeners
        $this->app['events']->listen(
            \App\Modules\Documents\Events\DocumentUploaded::class,
            \App\Modules\Documents\Listeners\ParseDocumentListener::class
        );

        $this->app['events']->listen(
            \App\Modules\Documents\Events\DocumentProcessed::class,
            \App\Modules\Documents\Listeners\CreateMaintenanceFromDocument::class
        );

        $this->app['events']->listen(
            \App\Modules\Documents\Events\DocumentProcessed::class,
            \App\Modules\Documents\Listeners\CreateAlertFromDocument::class
        );
    }

    public function register(): void
    {
        //
    }

    protected function routeConfiguration(): array
    {
        return [
            'prefix' => 'api',
            'middleware' => ['web', 'auth'],
        ];
    }
}
