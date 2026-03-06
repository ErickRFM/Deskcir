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

use Illuminate\Support\Facades\Hash;
use App\Models\User;

/*
ADMIN
*/
if(!User::where('email','admin@deskcir.com')->exists()){
User::create([
'name' => 'Admin',
'email' => 'admin@deskcir.com',
'password' => Hash::make('Admin12345'),
'role_id' => 1
]);
echo 'ADMIN CREATED\n';
}

/*
TECHNICIAN
*/
if(!User::where('email','tech@deskcir.com')->exists()){
User::create([
'name' => 'Technician',
'email' => 'tech@deskcir.com',
'password' => Hash::make('Tech12345'),
'role_id' => 2
]);
echo 'TECH CREATED\n';
}

/*
CLIENT
*/
if(!User::where('email','client@deskcir.com')->exists()){
User::create([
'name' => 'Client',
'email' => 'client@deskcir.com',
'password' => Hash::make('Client12345'),
'role_id' => 3
]);
echo 'CLIENT CREATED\n';
}
"

php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "===== STARTING APACHE ====="

apache2-foreground