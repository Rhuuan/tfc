<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin/login');
});

// Rota para página de conta excluída
Route::get('/account/deleted', function () {
    return view('account.deleted');
})->name('account.deleted');
