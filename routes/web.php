<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\PrivilegeController;
use App\Http\Controllers\PrivilegeGroupController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\OfferController;

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

Route::group(['middleware' => ['guest']], function() {
    Route::get('/auth', [LandingController::class, 'index'])->name('landing');
    Route::post('/auth', [LandingController::class, 'auth'])->name('auth');
});

Route::group(['middleware' => ['auth']], function() {
    //logout
    Route::get('/logout', [LandingController::class, 'logout'])->name('logout');

    //homeDashboard
    Route::get('/', [HomeController::class, 'index'])->name('home');

    //privilege
    Route::get('/privilege', [PrivilegeController::class, 'index'])->name('settings.privilege.index');
    Route::get('/privilege/list', [PrivilegeController::class, 'getList'])->name('settings.privilege.list');
    Route::get('/privilege/add', [PrivilegeController::class, 'add'])->name('settings.privilege.add');
    Route::get('/privilege/edit/{id}', [PrivilegeController::class, 'edit'])->name('settings.privilege.edit');
    Route::post('/privilege/post/{action}/{id}', [PrivilegeController::class, 'post'])->name('settings.privilege.post');

    //privilegeGroup
    Route::get('/privigroup', [PrivilegeGroupController::class, 'index'])->name('settings.privigroup.index');
    Route::get('/privigroup/list', [PrivilegeGroupController::class, 'getList'])->name('settings.privigroup.list');
    Route::get('/privigroup/add', [PrivilegeGroupController::class, 'add'])->name('settings.privigroup.add');
    Route::get('/privigroup/edit/{id}', [PrivilegeGroupController::class, 'edit'])->name('settings.privigroup.edit');
    Route::post('/privigroup/post/{action}/{id}', [PrivilegeGroupController::class, 'post'])->name('settings.privigroup.post');

    //user
    Route::get('/user', [UserController::class, 'index'])->name('settings.user.index');
    Route::get('/user/list', [UserController::class, 'getList'])->name('settings.user.list');
    Route::get('/user/view/{id}', [UserController::class, 'view'])->name('settings.user.view');
    Route::get('/user/add', [UserController::class, 'add'])->name('settings.user.add');
    Route::get('/user/edit/{id}', [UserController::class, 'edit'])->name('settings.user.edit');
    Route::post('/user/post/{action}/{id}', [UserController::class, 'post'])->name('settings.user.post');

    //logs
    Route::get('/logs/activity', [LogsController::class, 'index'])->name('logs.activity.index');
    Route::get('/logs/activity/list', [LogsController::class, 'getList'])->name('logs.activity.list');
    Route::get('/logs/activity/list/{id}', [LogsController::class, 'getUser'])->name('logs.activity.user');
    
    //data
    Route::get('/data/anggaran/{year?}', [DataController::class, 'index'])->name('master.data.index');
    Route::get('/data/list', [DataController::class, 'getList'])->name('master.data.list');
    Route::get('/data/view/{id}', [DataController::class, 'view'])->name('master.data.view');
    Route::get('/data/add', [DataController::class, 'add'])->name('master.data.add');
    Route::get('/data/edit/{id}', [DataController::class, 'edit'])->name('master.data.edit');
    Route::post('/data/upload', [DataController::class, 'upload'])->name('master.data.upload');
    Route::post('/data/post/{action}/{id}', [DataController::class, 'post'])->name('master.data.post');

    //offer
    Route::get('/offer/anggaran/{year?}', [OfferController::class, 'index'])->name('transaction.offer.index');
    Route::get('/offer/list', [OfferController::class, 'getList'])->name('transaction.offer.list');
    // Route::get('/data/view/{id}', [OfferController::class, 'view'])->name('master.data.view');
    Route::get('/offer/add', [OfferController::class, 'add'])->name('transaction.offer.add');
    Route::get('/offer/edit/{id}', [OfferController::class, 'edit'])->name('transaction.offer.edit');
    // Route::post('/data/post/{action}/{id}', [DataController::class, 'post'])->name('master.data.post');
});