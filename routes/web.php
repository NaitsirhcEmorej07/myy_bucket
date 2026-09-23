<?php

use App\Http\Controllers\BucketController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard with dynamic metrics
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Bucket File Management
    Route::get('/bucket', [BucketController::class, 'index'])->name('bucket');
    Route::get('/bucket/data', [BucketController::class, 'data'])->name('bucket.data');
    Route::post('/bucket/folders', [BucketController::class, 'storeFolder'])->name('bucket.folder.store');
    Route::patch('/bucket/folders/{folder}', [BucketController::class, 'updateFolder'])->name('bucket.folder.update');
    Route::delete('/bucket/folders/{folder}', [BucketController::class, 'destroyFolder'])->name('bucket.folder.destroy');

    Route::post('/bucket/upload', [BucketController::class, 'upload'])->name('bucket.upload');
    Route::patch('/bucket/files/{file}/rename', [BucketController::class, 'renameFile'])->name('bucket.file.rename');
    Route::post('/bucket/files/{file}/star', [BucketController::class, 'toggleStar'])->name('bucket.file.star');
    Route::delete('/bucket/files/{file}', [BucketController::class, 'destroyFile'])->name('bucket.file.destroy');
    Route::get('/bucket/files/{file}/download', [BucketController::class, 'download'])->name('bucket.file.download');
    Route::get('/bucket/files/{file}/preview', [BucketController::class, 'preview'])->name('bucket.file.preview');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
