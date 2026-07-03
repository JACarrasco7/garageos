<?php

namespace App\Modules\Documents\Providers;

use App\Modules\Documents\Events\DocumentProcessed;
use App\Modules\Documents\Events\DocumentUploaded;
use App\Modules\Documents\Listeners\CreateAlertFromDocument;
use App\Modules\Documents\Listeners\CreateMaintenanceFromDocument;
use App\Modules\Documents\Listeners\ParseDocumentListener;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class DocumentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Migrations are in the root database/migrations directory
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__.'/../Routes/documents.php');
        });

        // Listeners
        $this->app['events']->listen(
            DocumentUploaded::class,
            ParseDocumentListener::class
        );

        $this->app['events']->listen(
            DocumentProcessed::class,
            CreateMaintenanceFromDocument::class
        );

        $this->app['events']->listen(
            DocumentProcessed::class,
            CreateAlertFromDocument::class
        );
    }

    public function register(): void
    {
        //
    }

    protected function routeConfiguration(): array
    {
        return [
            'middleware' => ['web', 'auth'],
        ];
    }
}
