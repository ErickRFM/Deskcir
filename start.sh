#!/bin/sh

echo "===== LARAVEL START ====="

cd /var/www/html

php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

php artisan storage:link || true

php artisan migrate --force --no-interaction || true

echo "===== CREATE ADMIN USER ====="

php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);
\$kernel->bootstrap();

if(!App\Models\User::where('email','admin@deskcir.com')->exists()){
    App\Models\User::create([
        'name' => 'Admin',
        'email' => 'admin@deskcir.com',
        'password' => Illuminate\Support\Facades\Hash::make('Admin12345'),
        'role_id' => 1
    ]);
    echo 'ADMIN CREATED';
}else{
    echo 'ADMIN EXISTS';
}
"

php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "===== STARTING APACHE ====="

apache2-foreground