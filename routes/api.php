<?php

use App\Http\Controllers\API\TaskController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
  Route::apiResource('tasks', TaskController::class)->except(['show'])->names('api.tasks');
});
