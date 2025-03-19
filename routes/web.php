<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PetController;

Route::get('/', [PetController::class, 'index'])->name('pets.index');

Route::resource('pets', PetController::class);
