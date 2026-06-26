<?php

use App\Http\Controllers\API\DocumentController;
use App\Http\Controllers\API\GroupController;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'documents'], function (Router $documents) {
    $documents->post('', [DocumentController::class, 'store']);
    $documents->get('', [DocumentController::class, 'index']);

    $documents->group(['prefix' => '{document}'], function (Router $document) {
        $document->put('', [DocumentController::class, 'update']);
        $document->delete('', [DocumentController::class, 'destroy']);
        $document->get('', [DocumentController::class, 'show']);
    });
});

Route::group(['prefix' => 'groups'], function (Router $groups) {
    $groups->post('', [GroupController::class, 'store']);
    $groups->get('', [GroupController::class, 'index']);

    $groups->group(['prefix' => '{document}'], function (Router $group) {
        $group->put('', [GroupController::class, 'update']);
        $group->delete('', [GroupController::class, 'destroy']);
        $group->get('', [GroupController::class, 'show']);
    });
});

