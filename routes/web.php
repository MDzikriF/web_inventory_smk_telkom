<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;

Route::get('/', function () {
    return redirect('/login');
});

// Debug route untuk testing
Route::get('/debug-add-item', function () {
    try {
        $item = new \App\Models\Item();
        $item->name = 'Debug Test Item ' . time();
        $item->kode_barang = 'DBG-' . time();
        $item->category_id = 1;
        $item->unit_id = 1;
        $item->stock = 5;
        $item->sub_kategori = 'KBM';
        $item->save();
        
        return "Item created successfully! ID: " . $item->id . " | Name: " . $item->name . " | Kode: " . $item->kode_barang;
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
});

Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'role:user'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
    Route::get('/catalog', [\App\Http\Controllers\User\CatalogController::class, 'index'])->name('catalog.index');
    
    Route::post('/requests', [\App\Http\Controllers\User\ItemRequestController::class, 'store'])->name('requests.store');
    
    Route::get('/complaints', [\App\Http\Controllers\User\DamageReportController::class, 'index'])->name('complaints.index');
    Route::post('/complaints', [\App\Http\Controllers\User\DamageReportController::class, 'store'])->name('complaints.store');
    
    Route::get('/history', [\App\Http\Controllers\User\HistoryController::class, 'index'])->name('history.index');
    
    Route::get('/profile', [\App\Http\Controllers\User\ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [\App\Http\Controllers\User\ProfileController::class, 'update'])->name('profile.update');
    
    Route::get('/notifications', [\App\Http\Controllers\User\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\User\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/{id}/return-request', [\App\Http\Controllers\User\NotificationController::class, 'requestReturn'])->name('notifications.returnRequest');
    
    Route::get('/chat', [\App\Http\Controllers\User\ChatAdminController::class, 'index'])->name('chat.index');
    Route::get('/chat/messages', [\App\Http\Controllers\User\ChatAdminController::class, 'getMessages'])->name('chat.messages');
    Route::post('/chat/send', [\App\Http\Controllers\User\ChatAdminController::class, 'sendMessage'])->name('chat.send');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/laporan', [\App\Http\Controllers\Admin\LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/create-barang', [\App\Http\Controllers\Admin\LaporanController::class, 'createBarang'])->name('laporan.create_barang');
    Route::post('/laporan/store-barang', [\App\Http\Controllers\Admin\LaporanController::class, 'storeBarang'])->name('laporan.store_barang');
    Route::get('/laporan/create-rusak', [\App\Http\Controllers\Admin\LaporanController::class, 'createRusak'])->name('laporan.create_rusak');
    Route::post('/laporan/store-rusak', [\App\Http\Controllers\Admin\LaporanController::class, 'storeRusak'])->name('laporan.store_rusak');
    
    // Settings
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
    Route::post('/chat/clear-all', [\App\Http\Controllers\Admin\SettingsController::class, 'clearAllChat'])->name('chat.clear_all');
    
    Route::get('/chat', [\App\Http\Controllers\Admin\ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/messages/{userId}', [\App\Http\Controllers\Admin\ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/chat/send/{userId}', [\App\Http\Controllers\Admin\ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::resource('/users', AdminUserController::class);
    Route::resource('/items', \App\Http\Controllers\Admin\ItemController::class);
    
    // Categories & Units
    Route::post('/categories', [\App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('categories.store');
    Route::delete('/categories/{id}', [\App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::post('/units', [\App\Http\Controllers\Admin\UnitController::class, 'store'])->name('units.store');
    Route::delete('/units/{id}', [\App\Http\Controllers\Admin\UnitController::class, 'destroy'])->name('units.destroy');
    
    // Validasi
    Route::get('/validations', [\App\Http\Controllers\Admin\RequestValidationController::class, 'index'])->name('validations.index');
    Route::post('/validations/{id}/approve', [\App\Http\Controllers\Admin\RequestValidationController::class, 'approve'])->name('validations.approve');
    Route::post('/validations/{id}/reject', [\App\Http\Controllers\Admin\RequestValidationController::class, 'reject'])->name('validations.reject');
    Route::post('/validations/damages/{id}/review', [\App\Http\Controllers\Admin\RequestValidationController::class, 'reviewDamage'])->name('validations.damage.review');
    
    // Export Reports
    Route::get('/history/report/pdf', [\App\Http\Controllers\Admin\HistoryController::class, 'exportPdf'])->name('history.report.pdf');
    Route::get('/history/report/excel', [\App\Http\Controllers\Admin\HistoryController::class, 'exportExcel'])->name('history.report.excel');
    Route::post('/validations/damages/{id}/resolve', [\App\Http\Controllers\Admin\RequestValidationController::class, 'resolveDamage'])->name('validations.damage.resolve');
    
    // History
    Route::get('/history', [\App\Http\Controllers\Admin\HistoryController::class, 'index'])->name('history.index');
    Route::post('/history/{id}/return', [\App\Http\Controllers\Admin\HistoryController::class, 'returnItem'])->name('history.return');
    
    Route::get('/dashboard', function() { return redirect()->route('admin.users.index'); })->name('dashboard');
});
