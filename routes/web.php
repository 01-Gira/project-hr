<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

// dashboard.view is seeded to every default role, so this gate excludes exactly
// one case: a user holding no role at all. Deny-by-default is deliberate -- a
// user with no role has been granted nothing, and the dashboard is the entry
// point to everything else.
Route::middleware(['auth', 'verified', 'can:dashboard.view'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
