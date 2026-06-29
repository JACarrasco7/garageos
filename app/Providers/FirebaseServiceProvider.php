<?php

namespace App\Providers;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging;
use Illuminate\Support\ServiceProvider;

class FirebaseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Messaging::class, function () {
            $factory = (new Factory)->withServiceAccount(
                storage_path('app/firebase-credentials.json')
            );
            return $factory->createMessaging();
        });

        $this->app->alias(Messaging::class, 'firebase.messaging');
    }
}