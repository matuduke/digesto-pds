<?php

use App\Http\Controllers\Web\AdminDocumentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/documents', [AdminDocumentController::class, 'index'])->name('documents.index');
Route::post('/documents', [AdminDocumentController::class, 'index'])->name('documents.search');
Route::get('/documents/new', [AdminDocumentController::class, 'new'])->name('documents.new');
Route::post('/documents/save', [AdminDocumentController::class, 'update'])->name('documents.save');
Route::get('/documents/{document}', [AdminDocumentController::class, 'show'])->name('documents.show');
Route::get('/documents/{document}/edit', [AdminDocumentController::class, 'edit'])->name('documents.edit');
