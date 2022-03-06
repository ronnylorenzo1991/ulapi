<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\SpaController;
use \App\Http\Controllers\UserController;
use \App\Http\Controllers\RoleController;
use \App\Http\Controllers\DefaultController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth:sanctum', 'verified'])
    ->get('/', SpaController::class)->name('home');

Route::middleware(['auth:sanctum', 'verified'])
    ->get('/dashboard', SpaController::class)->name('dashboard');

Route::middleware(['auth:sanctum', 'verified'])
    ->get('/settings', SpaController::class)->name('settings');

Route::group(['middleware' => 'auth', 'prefix' => 'users'], function () {
    Route::middleware(['auth:sanctum', 'verified'])
        ->get('/', SpaController::class)->name('users');
    Route::get('/all', [UserController::class, 'getAll'])
        ->name('users.all');
    Route::post('/create', [UserController::class, 'store'])
        ->name('users.create');
    Route::post('{id}/edit', [UserController::class, 'update'])
        ->name('users.edit');
    Route::delete('{id}/remove', [UserController::class, 'delete'])
        ->name('users.remove');
});

Route::group(['middleware' => 'auth'], function () {
	Route::resource('user', 'App\Http\Controllers\UserController', ['except' => ['show']]);
	Route::get('profile', ['as' => 'profile.edit', 'uses' => 'App\Http\Controllers\ProfileController@edit']);
	Route::put('profile', ['as' => 'profile.update', 'uses' => 'App\Http\Controllers\ProfileController@update']);
	Route::get('upgrade', function () {return view('pages.upgrade');})->name('upgrade');
	 Route::get('map', function () {return view('pages.maps');})->name('map');
	 Route::get('icons', function () {return view('pages.icons');})->name('icons');
	 Route::get('table-list', function () {return view('pages.tables');})->name('table');
	Route::put('profile/password', ['as' => 'profile.password', 'uses' => 'App\Http\Controllers\ProfileController@password']);
});

Route::group(['middleware' => 'auth', 'prefix' => 'roles'], function () {
    Route::middleware(['auth:sanctum', 'verified'])
        ->get('/', SpaController::class)->name('roles');
    Route::get('/all', [RoleController::class, 'getAll'])
        ->name('roles.all');
    Route::post('/create', [RoleController::class, 'store'])
        ->name('roles.create');
    Route::post('{id}/edit', [RoleController::class, 'update'])
        ->name('roles.edit');
    Route::delete('{id}/remove', [RoleController::class, 'delete'])
        ->name('roles.remove');
    Route::post('{id}/addPermission', [RoleController::class, 'addPermission'])
        ->name('roles.add_permission');
});

Route::group(['middleware' => 'auth', 'prefix' => 'defaults'], function () {
    Route::get('/lists', [DefaultController::class, 'getLists'])
        ->name('defaults.lists');
});
