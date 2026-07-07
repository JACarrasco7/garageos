<?php

use App\Models\User;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$user = new User;
$user->name = 'Test User';
$user->email = 'test@garageos.com';
$user->password = bcrypt('password123');
$user->save();

echo 'Usuario creado: '.$user->email." / password123\n";
