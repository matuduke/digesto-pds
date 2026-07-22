<?php

use App\Http\Controllers\Web\AdminDocumentController;
use App\Http\Controllers\Web\AdminDocumentTypeController;
use App\Http\Controllers\Web\AdminGroupController;
use App\Http\Controllers\Web\AdminUserController;
use App\Http\Middleware\IsAdministrator;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\UserDocumentController;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Hash::make('password');
});

Route::middleware(RedirectIfAuthenticated::class)->group(function () {
    Route::get('/login', [UserController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [UserController::class, 'login'])->name('login.attempt');

    Route::get('/reestablecer-contrasena', [UserController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/reestablecer-contrasena', [UserController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/restablecer-contrasena/{token}', [UserController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/restablecer-contrasena', [UserController::class, 'resetPassword'])->name('password.update');
});

Route::get('/logout', [UserController::class, 'logout'])->middleware(Authenticate::class)->name('logout');



Route::group(['prefix' => 'documentos'], function (Router $documents) {
    $documents->get('/', [UserDocumentController::class, 'index'])->name('documents.index');
    $documents->get('/listado/{documentType}', [UserDocumentController::class, 'index'])->name('documents.byType');
    $documents->post('/listado/{documentType}', [UserDocumentController::class, 'index'])->name('documents.search');
    $documents->group(['prefix' => '{document}'], function (Router $document) {
        $document->get('', [UserDocumentController::class, 'show'])->name('documents.show');
    });
});

Route::group(['prefix' => 'admin', 'middleware' => [IsAdministrator::class]], function (Router $admin) {
    // Documentos
    $admin->group(['prefix' => 'documentos'], function (Router $documents) {
        $documents->get('/', [AdminDocumentController::class, 'index'])->name('admin.documents.index');
        $documents->post('/', [AdminDocumentController::class, 'index'])->name('admin.documents.search');
        $documents->get('/nuevo', [AdminDocumentController::class, 'new'])->name('admin.documents.new');
        $documents->post('/guardar', [AdminDocumentController::class, 'update'])->name('admin.documents.save');
        $documents->group(['prefix' => '{document}'], function (Router $document) {
            $document->get('', [AdminDocumentController::class, 'show'])->name('admin.documents.show');
            $document->get('/editar', [AdminDocumentController::class, 'edit'])->name('admin.documents.edit');
            $document->delete('', [AdminDocumentController::class, 'destroy'])->name('admin.documents.destroy');
        });
    });

    // Usuarios
    $admin->group(['prefix' => 'usuarios'], function (Router $users) {
        $users->get('/', [AdminUserController::class, 'index'])->name('admin.users.index');
        $users->get('/nuevo', [AdminUserController::class, 'new'])->name('admin.users.new');
        $users->post('/guardar', [AdminUserController::class, 'update'])->name('admin.users.save');
        $users->group(['prefix' => '{user}'], function (Router $user) {
            $user->get('/editar', [AdminUserController::class, 'edit'])->name('admin.users.edit');
            $user->delete('', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
        });
    });

    // Grupos
    $admin->group(['prefix' => 'grupos'], function (Router $groups) {
        $groups->get('/', [AdminGroupController::class, 'index'])->name('admin.groups.index');
        $groups->get('/nuevo', [AdminGroupController::class, 'new'])->name('admin.groups.new');
        $groups->post('/guardar', [AdminGroupController::class, 'update'])->name('admin.groups.save');
        $groups->group(['prefix' => '{group}'], function (Router $group) {
            $group->get('/editar', [AdminGroupController::class, 'edit'])->name('admin.groups.edit');
            $group->delete('', [AdminGroupController::class, 'destroy'])->name('admin.groups.destroy');
        });
    });

    // Tipos de documento
    $admin->group(['prefix' => 'tipos-de-documento'], function (Router $documentTypes) {
        $documentTypes->get('/', [AdminDocumentTypeController::class, 'index'])->name('admin.document-types.index');
        $documentTypes->get('/nuevo', [AdminDocumentTypeController::class, 'new'])->name('admin.document-types.new');
        $documentTypes->post('/guardar', [AdminDocumentTypeController::class, 'update'])->name('admin.document-types.save');
        $documentTypes->group(['prefix' => '{documentType}'], function (Router $documentType) {
            $documentType->get('/editar', [AdminDocumentTypeController::class, 'edit'])->name('admin.document-types.edit');
            $documentType->delete('', [AdminDocumentTypeController::class, 'destroy'])->name('admin.document-types.destroy');
        });
    });
});
