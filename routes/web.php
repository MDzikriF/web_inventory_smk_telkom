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

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::post('/verify-password', [\App\Http\Controllers\Admin\UserController::class, 'verifyPassword'])->name('verify-password');
        
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/laporan', [\App\Http\Controllers\Admin\LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/create-barang', [\App\Http\Controllers\Admin\LaporanController::class, 'createBarang'])->name('laporan.create_barang');
        Route::post('/laporan/store-barang', [\App\Http\Controllers\Admin\LaporanController::class, 'storeBarang'])->name('laporan.store_barang');
        Route::get('/laporan/create-rusak', [\App\Http\Controllers\Admin\LaporanController::class, 'createRusak'])->name('laporan.create_rusak');
        Route::post('/laporan/store-rusak', [\App\Http\Controllers\Admin\LaporanController::class, 'storeRusak'])->name('laporan.store_rusak');
        Route::get('/laporan/create-kerusakan', [\App\Http\Controllers\Admin\LaporanController::class, 'createKerusakan'])->name('laporan.create_kerusakan');
        Route::post('/laporan/store-kerusakan', [\App\Http\Controllers\Admin\LaporanController::class, 'storeKerusakan'])->name('laporan.store_kerusakan');
        Route::get('/laporan/create-perbaikan', [\App\Http\Controllers\Admin\LaporanController::class, 'createPerbaikan'])->name('laporan.create_perbaikan');
        Route::post('/laporan/store-perbaikan', [\App\Http\Controllers\Admin\LaporanController::class, 'storePerbaikan'])->name('laporan.store_perbaikan');
        Route::get('/laporan/create-peminjaman', [\App\Http\Controllers\Admin\LaporanController::class, 'createPeminjaman'])->name('laporan.create_peminjaman');
        Route::post('/laporan/store-peminjaman', [\App\Http\Controllers\Admin\LaporanController::class, 'storePeminjaman'])->name('laporan.store_peminjaman');
        
        // Export Laporan
        Route::get('/laporan/export/peminjaman/pdf', [\App\Http\Controllers\Admin\LaporanController::class, 'exportPeminjamanPdf'])->name('laporan.export_peminjaman_pdf');
        Route::get('/laporan/export/peminjaman/word', [\App\Http\Controllers\Admin\LaporanController::class, 'exportPeminjamanWord'])->name('laporan.export_peminjaman_word');
        Route::get('/laporan/export/peminjaman/excel', [\App\Http\Controllers\Admin\LaporanController::class, 'exportPeminjamanExcel'])->name('laporan.export_peminjaman_excel');
        Route::get('/laporan/export/barang-masuk/pdf', [\App\Http\Controllers\Admin\LaporanController::class, 'exportBarangMasukPdf'])->name('laporan.export_barang_masuk_pdf');
        Route::get('/laporan/export/barang-masuk/word', [\App\Http\Controllers\Admin\LaporanController::class, 'exportBarangMasukWord'])->name('laporan.export_barang_masuk_word');
        Route::get('/laporan/export/barang-masuk/excel', [\App\Http\Controllers\Admin\LaporanController::class, 'exportBarangMasukExcel'])->name('laporan.export_barang_masuk_excel');
        Route::get('/laporan/export/barang-keluar/pdf', [\App\Http\Controllers\Admin\LaporanController::class, 'exportBarangKeluarPdf'])->name('laporan.export_barang_keluar_pdf');
        Route::get('/laporan/export/barang-keluar/word', [\App\Http\Controllers\Admin\LaporanController::class, 'exportBarangKeluarWord'])->name('laporan.export_barang_keluar_word');
        Route::get('/laporan/export/barang-keluar/excel', [\App\Http\Controllers\Admin\LaporanController::class, 'exportBarangKeluarExcel'])->name('laporan.export_barang_keluar_excel');
        Route::get('/laporan/export/kerusakan/pdf', [\App\Http\Controllers\Admin\LaporanController::class, 'exportKerusakanPdf'])->name('laporan.export_kerusakan_pdf');
        Route::get('/laporan/export/kerusakan/excel', [\App\Http\Controllers\Admin\LaporanController::class, 'exportKerusakanExcel'])->name('laporan.export_kerusakan_excel');
        Route::get('/laporan/export/perbaikan/pdf', [\App\Http\Controllers\Admin\LaporanController::class, 'exportPerbaikanPdf'])->name('laporan.export_perbaikan_pdf');
        Route::get('/laporan/export/perbaikan/word', [\App\Http\Controllers\Admin\LaporanController::class, 'exportPerbaikanWord'])->name('laporan.export_perbaikan_word');
        Route::get('/laporan/export/perbaikan/excel', [\App\Http\Controllers\Admin\LaporanController::class, 'exportPerbaikanExcel'])->name('laporan.export_perbaikan_excel');
        
        // Fitur Kerusakan (Terpisah)
        Route::get('/kerusakan', [\App\Http\Controllers\Admin\KerusakanController::class, 'index'])->name('kerusakan.index');
        Route::get('/kerusakan/create', [\App\Http\Controllers\Admin\KerusakanController::class, 'create'])->name('kerusakan.create');
        Route::post('/kerusakan', [\App\Http\Controllers\Admin\KerusakanController::class, 'store'])->name('kerusakan.store');
        Route::get('/kerusakan/{id}/edit', [\App\Http\Controllers\Admin\KerusakanController::class, 'edit'])->name('kerusakan.edit');
        Route::put('/kerusakan/{id}', [\App\Http\Controllers\Admin\KerusakanController::class, 'update'])->name('kerusakan.update');
        Route::delete('/kerusakan/{id}', [\App\Http\Controllers\Admin\KerusakanController::class, 'destroy'])->name('kerusakan.destroy');
        Route::get('/kerusakan/export/pdf', [\App\Http\Controllers\Admin\KerusakanController::class, 'exportPdf'])->name('kerusakan.export_pdf');
        Route::get('/kerusakan/export/excel', [\App\Http\Controllers\Admin\KerusakanController::class, 'exportExcel'])->name('kerusakan.export_excel');
        
        // Fitur Perbaikan (Terpisah)
        Route::get('/perbaikan', [\App\Http\Controllers\Admin\PerbaikanController::class, 'index'])->name('perbaikan.index');
        Route::get('/perbaikan/create', [\App\Http\Controllers\Admin\PerbaikanController::class, 'create'])->name('perbaikan.create');
        Route::post('/perbaikan', [\App\Http\Controllers\Admin\PerbaikanController::class, 'store'])->name('perbaikan.store');
        Route::get('/perbaikan/{id}/edit', [\App\Http\Controllers\Admin\PerbaikanController::class, 'edit'])->name('perbaikan.edit');
        Route::put('/perbaikan/{id}', [\App\Http\Controllers\Admin\PerbaikanController::class, 'update'])->name('perbaikan.update');
        Route::delete('/perbaikan/{id}', [\App\Http\Controllers\Admin\PerbaikanController::class, 'destroy'])->name('perbaikan.destroy');
        Route::get('/perbaikan/export/pdf', [\App\Http\Controllers\Admin\PerbaikanController::class, 'exportPdf'])->name('perbaikan.export_pdf');
        Route::get('/perbaikan/export/word', [\App\Http\Controllers\Admin\PerbaikanController::class, 'exportWord'])->name('perbaikan.export_word');
        Route::get('/perbaikan/export/excel', [\App\Http\Controllers\Admin\PerbaikanController::class, 'exportExcel'])->name('perbaikan.export_excel');
        
        // Fitur Peminjaman (Terpisah)
        Route::get('/peminjaman', [\App\Http\Controllers\Admin\PeminjamanController::class, 'index'])->name('peminjaman.index');
        Route::get('/peminjaman/create', [\App\Http\Controllers\Admin\PeminjamanController::class, 'create'])->name('peminjaman.create');
        Route::post('/peminjaman', [\App\Http\Controllers\Admin\PeminjamanController::class, 'store'])->name('peminjaman.store');
        Route::get('/peminjaman/{id}/edit', [\App\Http\Controllers\Admin\PeminjamanController::class, 'edit'])->name('peminjaman.edit');
        Route::put('/peminjaman/{id}', [\App\Http\Controllers\Admin\PeminjamanController::class, 'update'])->name('peminjaman.update');
        Route::delete('/peminjaman/{id}', [\App\Http\Controllers\Admin\PeminjamanController::class, 'destroy'])->name('peminjaman.destroy');
        Route::get('/peminjaman/export/pdf', [\App\Http\Controllers\Admin\PeminjamanController::class, 'exportPdf'])->name('peminjaman.export_pdf');
        Route::get('/peminjaman/export/word', [\App\Http\Controllers\Admin\PeminjamanController::class, 'exportWord'])->name('peminjaman.export_word');
        Route::get('/peminjaman/export/excel', [\App\Http\Controllers\Admin\PeminjamanController::class, 'exportExcel'])->name('peminjaman.export_excel');
        
        // Laporan Keseluruhan (Combined View)
        Route::get('/laporan-keseluruhan', [\App\Http\Controllers\Admin\LaporanKeseluruhanController::class, 'index'])->name('laporan_keseluruhan.index');
        Route::get('/laporan-keseluruhan/export/pdf', [\App\Http\Controllers\Admin\LaporanKeseluruhanController::class, 'exportPdf'])->name('laporan_keseluruhan.export_pdf');
        Route::get('/laporan-keseluruhan/export/excel', [\App\Http\Controllers\Admin\LaporanKeseluruhanController::class, 'exportExcel'])->name('laporan_keseluruhan.export_excel');
        
        // Settings
        Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
        Route::put('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
        Route::post('/chat/clear-all', [\App\Http\Controllers\Admin\SettingsController::class, 'clearAllChat'])->name('chat.clear_all');
        
        Route::get('/chat', [\App\Http\Controllers\Admin\ChatController::class, 'index'])->name('chat.index');
        Route::get('/chat/messages/{userId}', [\App\Http\Controllers\Admin\ChatController::class, 'getMessages'])->name('chat.messages');
        Route::post('/chat/send/{userId}', [\App\Http\Controllers\Admin\ChatController::class, 'sendMessage'])->name('chat.send');
        Route::post('/chat/clear/{userId}', [\App\Http\Controllers\Admin\ChatController::class, 'clearChat'])->name('chat.clear');
        Route::delete('/chat/message/{id}', [\App\Http\Controllers\Admin\ChatController::class, 'destroyMessage'])->name('chat.destroy_message');
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
        Route::post('/validations/damages/{id}/unrepairable', [\App\Http\Controllers\Admin\RequestValidationController::class, 'unrepairableDamage'])->name('validations.damage.unrepairable');
        
        // Export Reports
        Route::get('/history/report/pdf', [\App\Http\Controllers\Admin\HistoryController::class, 'exportPdf'])->name('history.report.pdf');
        Route::get('/history/report/excel', [\App\Http\Controllers\Admin\HistoryController::class, 'exportExcel'])->name('history.report.excel');
        Route::get('/history/report/peminjaman/pdf', [\App\Http\Controllers\Admin\HistoryController::class, 'exportPeminjamanPdf'])->name('history.report.peminjaman_pdf');
        Route::get('/history/report/peminjaman/word', [\App\Http\Controllers\Admin\HistoryController::class, 'exportPeminjamanWord'])->name('history.report.peminjaman_word');
        Route::get('/history/report/peminjaman/excel', [\App\Http\Controllers\Admin\HistoryController::class, 'exportPeminjamanExcel'])->name('history.report.peminjaman_excel');
        Route::get('/history/report/stok/pdf', [\App\Http\Controllers\Admin\HistoryController::class, 'exportStokPdf'])->name('history.report.stok_pdf');
        Route::get('/history/report/stok/word', [\App\Http\Controllers\Admin\HistoryController::class, 'exportStokWord'])->name('history.report.stok_word');
        Route::get('/history/report/stok/excel', [\App\Http\Controllers\Admin\HistoryController::class, 'exportStokExcel'])->name('history.report.stok_excel');
        Route::get('/history/report/perbaikan/pdf', [\App\Http\Controllers\Admin\HistoryController::class, 'exportPerbaikanPdf'])->name('history.report.perbaikan_pdf');
        Route::get('/history/report/perbaikan/word', [\App\Http\Controllers\Admin\HistoryController::class, 'exportPerbaikanWord'])->name('history.report.perbaikan_word');
        Route::get('/history/report/perbaikan/excel', [\App\Http\Controllers\Admin\HistoryController::class, 'exportPerbaikanExcel'])->name('history.report.perbaikan_excel');
        Route::post('/validations/damages/{id}/resolve', [\App\Http\Controllers\Admin\RequestValidationController::class, 'resolveDamage'])->name('validations.damage.resolve');
        
        // History & Laporan
        Route::get('/laporan-cetak', [\App\Http\Controllers\Admin\HistoryController::class, 'laporan'])->name('history.laporan');
        Route::get('/history', [\App\Http\Controllers\Admin\HistoryController::class, 'index'])->name('history.index');
        Route::post('/history/{id}/return', [\App\Http\Controllers\Admin\HistoryController::class, 'returnItem'])->name('history.return');
        
        Route::get('/dashboard', function() { return redirect()->route('admin.users.index'); })->name('dashboard');
    });
    Route::post('/laporan/store-kerusakan', [\App\Http\Controllers\Admin\LaporanController::class, 'storeKerusakan'])->name('laporan.store_kerusakan');
    Route::get('/laporan/create-perbaikan', [\App\Http\Controllers\Admin\LaporanController::class, 'createPerbaikan'])->name('laporan.create_perbaikan');
    Route::post('/laporan/store-perbaikan', [\App\Http\Controllers\Admin\LaporanController::class, 'storePerbaikan'])->name('laporan.store_perbaikan');
    Route::get('/laporan/create-peminjaman', [\App\Http\Controllers\Admin\LaporanController::class, 'createPeminjaman'])->name('laporan.create_peminjaman');
    Route::post('/laporan/store-peminjaman', [\App\Http\Controllers\Admin\LaporanController::class, 'storePeminjaman'])->name('laporan.store_peminjaman');
    
    // Export Laporan
    Route::get('/laporan/export/peminjaman/pdf', [\App\Http\Controllers\Admin\LaporanController::class, 'exportPeminjamanPdf'])->name('laporan.export_peminjaman_pdf');
    Route::get('/laporan/export/peminjaman/word', [\App\Http\Controllers\Admin\LaporanController::class, 'exportPeminjamanWord'])->name('laporan.export_peminjaman_word');
    Route::get('/laporan/export/peminjaman/excel', [\App\Http\Controllers\Admin\LaporanController::class, 'exportPeminjamanExcel'])->name('laporan.export_peminjaman_excel');
    Route::get('/laporan/export/barang-masuk/pdf', [\App\Http\Controllers\Admin\LaporanController::class, 'exportBarangMasukPdf'])->name('laporan.export_barang_masuk_pdf');
    Route::get('/laporan/export/barang-masuk/word', [\App\Http\Controllers\Admin\LaporanController::class, 'exportBarangMasukWord'])->name('laporan.export_barang_masuk_word');
    Route::get('/laporan/export/barang-masuk/excel', [\App\Http\Controllers\Admin\LaporanController::class, 'exportBarangMasukExcel'])->name('laporan.export_barang_masuk_excel');
    Route::get('/laporan/export/barang-keluar/pdf', [\App\Http\Controllers\Admin\LaporanController::class, 'exportBarangKeluarPdf'])->name('laporan.export_barang_keluar_pdf');
    Route::get('/laporan/export/barang-keluar/word', [\App\Http\Controllers\Admin\LaporanController::class, 'exportBarangKeluarWord'])->name('laporan.export_barang_keluar_word');
    Route::get('/laporan/export/barang-keluar/excel', [\App\Http\Controllers\Admin\LaporanController::class, 'exportBarangKeluarExcel'])->name('laporan.export_barang_keluar_excel');
    Route::get('/laporan/export/kerusakan/pdf', [\App\Http\Controllers\Admin\LaporanController::class, 'exportKerusakanPdf'])->name('laporan.export_kerusakan_pdf');
    Route::get('/laporan/export/kerusakan/excel', [\App\Http\Controllers\Admin\LaporanController::class, 'exportKerusakanExcel'])->name('laporan.export_kerusakan_excel');
    Route::get('/laporan/export/perbaikan/pdf', [\App\Http\Controllers\Admin\LaporanController::class, 'exportPerbaikanPdf'])->name('laporan.export_perbaikan_pdf');
    Route::get('/laporan/export/perbaikan/word', [\App\Http\Controllers\Admin\LaporanController::class, 'exportPerbaikanWord'])->name('laporan.export_perbaikan_word');
    Route::get('/laporan/export/perbaikan/excel', [\App\Http\Controllers\Admin\LaporanController::class, 'exportPerbaikanExcel'])->name('laporan.export_perbaikan_excel');
    
    // Fitur Kerusakan (Terpisah)
    Route::get('/kerusakan', [\App\Http\Controllers\Admin\KerusakanController::class, 'index'])->name('kerusakan.index');
    Route::get('/kerusakan/create', [\App\Http\Controllers\Admin\KerusakanController::class, 'create'])->name('kerusakan.create');
    Route::post('/kerusakan', [\App\Http\Controllers\Admin\KerusakanController::class, 'store'])->name('kerusakan.store');
    Route::get('/kerusakan/{id}/edit', [\App\Http\Controllers\Admin\KerusakanController::class, 'edit'])->name('kerusakan.edit');
    Route::put('/kerusakan/{id}', [\App\Http\Controllers\Admin\KerusakanController::class, 'update'])->name('kerusakan.update');
    Route::delete('/kerusakan/{id}', [\App\Http\Controllers\Admin\KerusakanController::class, 'destroy'])->name('kerusakan.destroy');
    Route::get('/kerusakan/export/pdf', [\App\Http\Controllers\Admin\KerusakanController::class, 'exportPdf'])->name('kerusakan.export_pdf');
    Route::get('/kerusakan/export/excel', [\App\Http\Controllers\Admin\KerusakanController::class, 'exportExcel'])->name('kerusakan.export_excel');
    
    // Fitur Perbaikan (Terpisah)
    Route::get('/perbaikan', [\App\Http\Controllers\Admin\PerbaikanController::class, 'index'])->name('perbaikan.index');
    Route::get('/perbaikan/create', [\App\Http\Controllers\Admin\PerbaikanController::class, 'create'])->name('perbaikan.create');
    Route::post('/perbaikan', [\App\Http\Controllers\Admin\PerbaikanController::class, 'store'])->name('perbaikan.store');
    Route::get('/perbaikan/{id}/edit', [\App\Http\Controllers\Admin\PerbaikanController::class, 'edit'])->name('perbaikan.edit');
    Route::put('/perbaikan/{id}', [\App\Http\Controllers\Admin\PerbaikanController::class, 'update'])->name('perbaikan.update');
    Route::delete('/perbaikan/{id}', [\App\Http\Controllers\Admin\PerbaikanController::class, 'destroy'])->name('perbaikan.destroy');
    Route::get('/perbaikan/export/pdf', [\App\Http\Controllers\Admin\PerbaikanController::class, 'exportPdf'])->name('perbaikan.export_pdf');
    Route::get('/perbaikan/export/word', [\App\Http\Controllers\Admin\PerbaikanController::class, 'exportWord'])->name('perbaikan.export_word');
    Route::get('/perbaikan/export/excel', [\App\Http\Controllers\Admin\PerbaikanController::class, 'exportExcel'])->name('perbaikan.export_excel');
    
    // Fitur Peminjaman (Terpisah)
    Route::get('/peminjaman', [\App\Http\Controllers\Admin\PeminjamanController::class, 'index'])->name('peminjaman.index');
    Route::get('/peminjaman/create', [\App\Http\Controllers\Admin\PeminjamanController::class, 'create'])->name('peminjaman.create');
    Route::post('/peminjaman', [\App\Http\Controllers\Admin\PeminjamanController::class, 'store'])->name('peminjaman.store');
    Route::get('/peminjaman/{id}/edit', [\App\Http\Controllers\Admin\PeminjamanController::class, 'edit'])->name('peminjaman.edit');
    Route::put('/peminjaman/{id}', [\App\Http\Controllers\Admin\PeminjamanController::class, 'update'])->name('peminjaman.update');
    Route::delete('/peminjaman/{id}', [\App\Http\Controllers\Admin\PeminjamanController::class, 'destroy'])->name('peminjaman.destroy');
    Route::get('/peminjaman/export/pdf', [\App\Http\Controllers\Admin\PeminjamanController::class, 'exportPdf'])->name('peminjaman.export_pdf');
    Route::get('/peminjaman/export/word', [\App\Http\Controllers\Admin\PeminjamanController::class, 'exportWord'])->name('peminjaman.export_word');
    Route::get('/peminjaman/export/excel', [\App\Http\Controllers\Admin\PeminjamanController::class, 'exportExcel'])->name('peminjaman.export_excel');
    
    // Laporan Keseluruhan (Combined View)
    Route::get('/laporan-keseluruhan', [\App\Http\Controllers\Admin\LaporanKeseluruhanController::class, 'index'])->name('laporan_keseluruhan.index');
    Route::get('/laporan-keseluruhan/export/pdf', [\App\Http\Controllers\Admin\LaporanKeseluruhanController::class, 'exportPdf'])->name('laporan_keseluruhan.export_pdf');
    Route::get('/laporan-keseluruhan/export/excel', [\App\Http\Controllers\Admin\LaporanKeseluruhanController::class, 'exportExcel'])->name('laporan_keseluruhan.export_excel');
    
    // Settings
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
    Route::post('/chat/clear-all', [\App\Http\Controllers\Admin\SettingsController::class, 'clearAllChat'])->name('chat.clear_all');
    
    Route::get('/chat', [\App\Http\Controllers\Admin\ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/messages/{userId}', [\App\Http\Controllers\Admin\ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/chat/send/{userId}', [\App\Http\Controllers\Admin\ChatController::class, 'sendMessage'])->name('chat.send');
    Route::post('/chat/clear/{userId}', [\App\Http\Controllers\Admin\ChatController::class, 'clearChat'])->name('chat.clear');
    Route::delete('/chat/message/{id}', [\App\Http\Controllers\Admin\ChatController::class, 'destroyMessage'])->name('chat.destroy_message');
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
    Route::post('/validations/damages/{id}/unrepairable', [\App\Http\Controllers\Admin\RequestValidationController::class, 'unrepairableDamage'])->name('validations.damage.unrepairable');
    
    // Export Reports
    Route::get('/history/report/pdf', [\App\Http\Controllers\Admin\HistoryController::class, 'exportPdf'])->name('history.report.pdf');
    Route::get('/history/report/excel', [\App\Http\Controllers\Admin\HistoryController::class, 'exportExcel'])->name('history.report.excel');
    Route::get('/history/report/peminjaman/pdf', [\App\Http\Controllers\Admin\HistoryController::class, 'exportPeminjamanPdf'])->name('history.report.peminjaman_pdf');
    Route::get('/history/report/peminjaman/word', [\App\Http\Controllers\Admin\HistoryController::class, 'exportPeminjamanWord'])->name('history.report.peminjaman_word');
    Route::get('/history/report/peminjaman/excel', [\App\Http\Controllers\Admin\HistoryController::class, 'exportPeminjamanExcel'])->name('history.report.peminjaman_excel');
    Route::get('/history/report/stok/pdf', [\App\Http\Controllers\Admin\HistoryController::class, 'exportStokPdf'])->name('history.report.stok_pdf');
    Route::get('/history/report/stok/word', [\App\Http\Controllers\Admin\HistoryController::class, 'exportStokWord'])->name('history.report.stok_word');
    Route::get('/history/report/stok/excel', [\App\Http\Controllers\Admin\HistoryController::class, 'exportStokExcel'])->name('history.report.stok_excel');
    Route::get('/history/report/perbaikan/pdf', [\App\Http\Controllers\Admin\HistoryController::class, 'exportPerbaikanPdf'])->name('history.report.perbaikan_pdf');
    Route::get('/history/report/perbaikan/word', [\App\Http\Controllers\Admin\HistoryController::class, 'exportPerbaikanWord'])->name('history.report.perbaikan_word');
    Route::get('/history/report/perbaikan/excel', [\App\Http\Controllers\Admin\HistoryController::class, 'exportPerbaikanExcel'])->name('history.report.perbaikan_excel');
    Route::post('/validations/damages/{id}/resolve', [\App\Http\Controllers\Admin\RequestValidationController::class, 'resolveDamage'])->name('validations.damage.resolve');
    
    // History & Laporan
    Route::get('/laporan-cetak', [\App\Http\Controllers\Admin\HistoryController::class, 'laporan'])->name('history.laporan');
    Route::get('/history', [\App\Http\Controllers\Admin\HistoryController::class, 'index'])->name('history.index');
    Route::post('/history/{id}/return', [\App\Http\Controllers\Admin\HistoryController::class, 'returnItem'])->name('history.return');
    
    Route::get('/dashboard', function() { return redirect()->route('admin.users.index'); })->name('dashboard');
});
