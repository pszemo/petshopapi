<?php
// routes/web.php

use App\Http\Controllers\PetController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PetController::class, 'index'])->name('home');

Route::resource('pets', PetController::class);
