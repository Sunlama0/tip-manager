<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\CompletionController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\Admin\SystemSettingsController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'role:admin'])->get('/admin', [DashboardController::class, 'admin'])->name('dashboard.admin');
Route::middleware(['auth', 'role:directeur'])->get('/directeur', [DashboardController::class, 'directeur'])->name('dashboard.directeur');
Route::middleware(['auth', 'role:responsable'])->get('/responsable', [DashboardController::class, 'responsable'])->name('dashboard.responsable');
Route::middleware(['auth', 'role:collaborateur'])->get('/collaborateur', [DashboardController::class, 'collaborateur'])->name('dashboard.collaborateur');

Route::middleware(['auth', 'role:directeur|responsable'])->group(function () {
    Route::get('/import', [ImportController::class, 'index'])->name('import.index');
    Route::post('/import/preview', [ImportController::class, 'preview'])->name('import.preview');
    Route::post('/import/confirm', [ImportController::class, 'confirm'])->name('import.confirm');
    Route::post('/import/assign', [ImportController::class, 'assignPack'])->name('import.assignPack');
    Route::get('/import/pack/{id}', [ImportController::class, 'showPackPreview'])->name('import.previewPack');
    Route::post('/preview/delete-row', [ImportController::class, 'deletePreviewRow'])->name('import.preview.deleteRow');
    Route::post('/import/cancel', [ImportController::class, 'cancelPreview'])->name('import.cancel');
    Route::put('/packs/lines/{line}', [ImportController::class, 'updateLine'])->name('packs.lines.update');
    Route::delete('/packs/lines/{line}', [ImportController::class, 'destroyLine'])->name('packs.lines.destroy');
});

Route::get('/import/packs', [ImportController::class, 'list'])
    ->middleware(['auth', 'role:directeur|responsable'])
    ->name('import.packs');


Route::middleware(['auth', 'role:collaborateur'])->prefix('completion')->group(function () {
    Route::get('/', [CompletionController::class, 'index'])->name('completion.index');
    Route::get('/pack/{pack}', [CompletionController::class, 'show'])->name('completion.pack.show');
    Route::post('/line/{line}/update', [CompletionController::class, 'update'])->name('completion.line.update');
    Route::post('/completion/line/{line}/complete', [CompletionController::class, 'saveLineCompletion'])->name('completion.line.complete');
    Route::delete('/completion/line/{line}', [CompletionController::class, 'destroy'])->name('completion.line.delete');
});

Route::prefix('exports')->name('exports.')->middleware('auth')->group(function () {
    Route::get('/', [ExportController::class, 'index'])->name('index');
    Route::post('/preview', [ExportController::class, 'preview'])->name('preview');
    Route::post('/export', [ExportController::class, 'export'])->name('export');
});

Route::middleware(['role:admin'])->group(function () {
    Route::get('/admin/parametres-systeme', [SystemSettingsController::class, 'index'])->name('admin.settings');
    Route::post('/admin/users/{user}/roles', [SystemSettingsController::class, 'updateUserRole'])->name('admin.users.updateRole');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';