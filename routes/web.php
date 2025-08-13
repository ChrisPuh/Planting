<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

Route::middleware('auth')->group(function () {
    Route::redirect('plants', 'plants/dashboard');

    Volt::route('plants/dashboard', 'plant.dashboard')->name('plants.dashboard');
    Volt::route('plants/index', 'plant.index')->name('plants.index');
    Volt::route('plants/create', 'plant.create')->name('plants.create');
    Volt::route('plants/show/{uuid}', 'plant.show')->name('plants.show');
    Volt::route('plants/edit/{uuid}', 'plant.edit')->name('plants.edit');
});

require __DIR__.'/auth.php';
