#!/bin/sh

echo "===== LARAVEL START ====="

cd /var/www/html

php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

php artisan storage:link || true

php artisan migrate --force --no-interaction || true

echo "===== CREATE DEFAULT USERS ====="

php -r "
require 'vendor/autoload.php';

\$app = require 'bootstrap/app.php';
\$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);
\$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

/*
|--------------------------------
| ADMIN
|--------------------------------
*/
if(!User::where('email','admin@deskcir.com')->exists()){
    User::create([
        'name' => 'Administrador',
        'email' => 'admin@deskcir.com',
        'password' => Hash::make('Admin12345'),
        'role_id' => 1
    ]);
    echo 'ADMIN CREATED\n';
}else{
    echo 'ADMIN EXISTS\n';
}

/*
|--------------------------------
| TECNICO
|--------------------------------
*/
if(!User::where('email','tec@deskcir.com')->exists()){
    User::create([
        'name' => 'Tecnico',
        'email' => 'tec@deskcir.com',
        'password' => Hash::make('12345678'),
        'role_id' => 2
    ]);
    echo 'TECH CREATED\n';
}else{
    echo 'TECH EXISTS\n';
}

/*
|--------------------------------
| CLIENTE
|--------------------------------
*/
if(!User::where('email','cliente@deskcir.com')->exists()){
    User::create([
        'name' => 'Cliente',
        'email' => 'cliente@deskcir.com',
        'password' => Hash::make('12345678'),
        'role_id' => 3
    ]);
    echo 'CLIENT CREATED\n';
}else{
    echo 'CLIENT EXISTS\n';
}
"

php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "===== STARTING APACHE ====="

apache2-foreground