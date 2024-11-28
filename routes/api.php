<?php

use Illuminate\Support\Facades\Route;
use JscorpTech\Atmospay\Views\AtmospayController;

Route::prefix("payment/atmospay/")->group(function () {
    Route::post("create", [AtmospayController::class, "create"]);
    Route::post("apply", [AtmospayController::class, "apply"]);
    Route::post("pre-apply", [AtmospayController::class, "pre_apply"]);
});
