<?php

use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

use App\Http\Controllers\{
    ProfileController,
    StoreController,
    TicketController,
    AppointmentController,
    ReportController,
    CartController,
    CheckoutController,
    CardController
};

use App\Http\Controllers\Auth\PasswordController;

// 🔥 PRODUCTOS ADMIN
use App\Http\Controllers\Admin\ProductController;

use App\Http\Controllers\Admin\{
    SalesController,
    ReportController as AdminReportController
};

use App\Http\Controllers\Technician\{
    TechnicianController,
    TechnicianTicketController
};

// ===================================
// 🔐 LOGIN CON GOOGLE
// ===================================
Route::get('/auth/google', function () {
    return Socialite::driver('google')->redirect();
})->name('google.login');

Route::get('/auth/google/callback', function () {

    $googleUser = Socialite::driver('google')->user();

    $user = User::firstOrCreate(
        ['email' => $googleUser->email],
        [
            'name' => $googleUser->name,
            'password' => bcrypt(str()->random(16)),
            'email_verified_at' => now(),
        ]
    );

    auth()->login($user);

    return redirect('/store');

});

// 🔥 AVATAR
Route::post('/profile/avatar',
    [ProfileController::class,'avatar'])
    ->name('profile.avatar');

// ===================================
// 🏠 HOME → TIENDA
// ===================================
Route::get('/', function () {
    return redirect('/store');
});

// ===================================
// 👑 PRODUCTOS ADMIN
// ===================================
Route::resource('admin/products', ProductController::class);

Route::delete('/products/image/{id}',
    [ProductController::class,'deleteImage'])
    ->name('admin.products.image.delete');

// ===================================
// 💳 MERCADO PAGO
// ===================================
Route::post('/cards/save', [CardController::class, 'save'])
    ->middleware('auth');

Route::delete('/cards/{id}', [CardController::class, 'delete'])
    ->middleware('auth');

// ===================================
// 🛒 TIENDA
// ===================================
Route::get('/store', [StoreController::class, 'index']);
Route::get('/store/category/{slug}', [StoreController::class, 'category']);
Route::get('/store/product/{id}', [StoreController::class, 'show']);

Route::post('/cart/add/{id}', [StoreController::class, 'addToCart']);

// ===================================
// 🛒 CARRITO
// ===================================
Route::get('/cart', [CartController::class, 'index']);
Route::post('/cart/remove/{id}', [CartController::class, 'remove']);

Route::get('/checkout', [CheckoutController::class, 'index']);
Route::post('/checkout', [CheckoutController::class, 'store']);

// ===================================
// 🔐 DASHBOARD
// ===================================
Route::get('/dashboard', function () {
    return redirect('/store');
})->middleware(['auth']);

// ===================================
// 🔐 AUTENTICADAS
// ===================================
Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    Route::put('/password/update',
        [PasswordController::class,'update'])
        ->name('password.update');

    // SOPORTE CLIENTE
    Route::get('/support', [TicketController::class, 'index']);
    Route::get('/support/create', [TicketController::class, 'create']);
    Route::post('/support', [TicketController::class, 'store']);
    Route::get('/support/{id}', [TicketController::class, 'show']);
    Route::post('/support/{id}/message',
        [TicketController::class,'addMessage']);

    // TICKETS
    Route::resource('tickets', TicketController::class);

    Route::post('/tickets/{id}/message',
        [TicketController::class,'addMessage']);

    // CITAS
    Route::resource('appointments', AppointmentController::class);

    // REPORTES
    Route::get('/reports/{ticket}',
        [ReportController::class, 'show']);

    Route::get('/reports/{ticket}/pdf',
        [ReportController::class, 'pdf']);
});

// ===================================
// 👑 ADMIN
// ===================================
Route::middleware(['auth'])->prefix('admin')->group(function(){

    Route::get('/sales', [SalesController::class, 'index']);

    Route::post('/sales/{id}/status',
        [SalesController::class, 'updateStatus']);

    Route::post('/tickets/{id}/reply',
        [App\Http\Controllers\Admin\TicketController::class, 'reply']
    )->name('admin.tickets.reply');
    
    Route::prefix('reports')->group(function(){

        Route::get('/',
            [AdminReportController::class,'dashboard']);

        Route::get('/sales',
            [AdminReportController::class,'sales']);

        Route::get('/products',
            [AdminReportController::class,'products']);

        Route::get('/clients',
            [AdminReportController::class,'clients']);

        Route::get('/finance',
            [AdminReportController::class,'finance']);

        Route::get('/export/excel',
            [AdminReportController::class,'excel']);

        Route::get('/export/pdf',
            [AdminReportController::class,'pdf']);
    });
});

// ===================================
// 👨‍🔧 TÉCNICO
// ===================================
Route::middleware(['auth'])
->prefix('technician')
->group(function(){

    Route::get('/',
        [TechnicianController::class,'dashboard'])
        ->name('technician.dashboard');

    Route::get('/tickets',
        [TechnicianTicketController::class,'index'])
        ->name('technician.tickets');

    Route::get('/tickets/{id}',
        [TechnicianTicketController::class,'show'])
        ->name('technician.tickets.show');

    Route::post('/tickets/{id}/reply',
        [TechnicianTicketController::class,'reply'])
        ->name('technician.tickets.reply');

    Route::get('/calendar',
        [TechnicianController::class,'calendar'])
        ->name('technician.calendar');
});

// ===================================
// 🎥 WEBRTC SIGNALING (LO QUE PEDISTE)
// ===================================
Route::post('/webrtc/offer',
    [App\Http\Controllers\WebRTCController::class,'offer']);

Route::post('/webrtc/answer',
    [App\Http\Controllers\WebRTCController::class,'answer']);

Route::post('/webrtc/ice',
    [App\Http\Controllers\WebRTCController::class,'ice']);

// ===================================
// ARCHIVOS EXTRA
// ===================================
require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/client.php';
require __DIR__.'/technician.php';
require __DIR__.'/store.php';