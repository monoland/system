<?php

use Illuminate\Support\Facades\Route;
use Module\System\Http\Controllers\SystemModuleController;

Route::get('module/{systemModule}/debug', [SystemModuleController::class, 'debug']);
