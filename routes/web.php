<?php

use App\Http\Controllers\AccountabilityController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\BalanceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\PrivilegeController;
use App\Http\Controllers\PrivilegeGroupController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\ReceptionController;
use App\Http\Controllers\YearController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\DailyController;
use App\Http\Controllers\RecapitulationController;
use Illuminate\Support\Facades\Artisan;

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
    Route::get('/password/{action}/{id}', [PasswordController::class, 'index'])->name('password.guest');
    Route::post('/password/{action}/{id}', [PasswordController::class, 'post'])->name('password.guest.post');

    Route::get('/migrate', function(){
        // Artisan::call('migrate:refresh --seed');
        Artisan::call('optimize:clear');
    });
});

Route::group(['middleware' => ['auth']], function() {
    //logout
    Route::get('/logout', [LandingController::class, 'logout'])->name('logout');

    //home
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/home/realization', [HomeController::class, 'getRealization'])->name('home.realization');
    Route::get('/home/pending', [HomeController::class, 'getPending'])->name('home.pending');

    //myaccount
    Route::get('/myaccount', [AccountController::class, 'index'])->name('myaccount');

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
    Route::post('/user/reset/{id}', [UserController::class, 'reset'])->name('settings.user.reset');

    //logs
    Route::get('/logs/activity', [LogsController::class, 'index'])->name('logs.activity.index');
    Route::get('/logs/activity/list', [LogsController::class, 'getList'])->name('logs.activity.list');
    Route::get('/logs/activity/list/{id}', [LogsController::class, 'getUser'])->name('logs.activity.user');
    
    //data
    Route::get('/data/reff/{year?}', [DataController::class, 'index'])->name('master.data.index');
    Route::get('/data/list', [DataController::class, 'getList'])->name('master.data.list');
    Route::get('/data/view/{id}', [DataController::class, 'view'])->name('master.data.view');
    Route::get('/data/add', [DataController::class, 'add'])->name('master.data.add');
    Route::get('/data/edit/{id}', [DataController::class, 'edit'])->name('master.data.edit');
    Route::post('/data/upload', [DataController::class, 'upload'])->name('master.data.upload');
    Route::post('/data/post/{action}/{id}', [DataController::class, 'post'])->name('master.data.post');

    //balance
    Route::get('/balance', [BalanceController::class, 'index'])->name('master.balance.index');
    Route::get('/balance/list', [BalanceController::class, 'getList'])->name('master.balance.list');
    Route::get('/balance/view/{id}', [BalanceController::class, 'view'])->name('master.balance.view');
    Route::get('/balance/add', [BalanceController::class, 'add'])->name('master.balance.add');
    Route::get('/balance/edit/{id}', [BalanceController::class, 'edit'])->name('master.balance.edit');
    Route::post('/balance/post/{action}/{id}', [BalanceController::class, 'post'])->name('master.balance.post');

    //offer
    Route::get('/offer/anggaran/{year?}', [OfferController::class, 'index'])->name('transaction.offer.index');
    Route::get('/offer/list', [OfferController::class, 'getList'])->name('transaction.offer.list');
    // Route::get('/data/view/{id}', [OfferController::class, 'view'])->name('master.data.view');
    Route::get('/offer/add', [OfferController::class, 'add'])->name('transaction.offer.add');
    Route::get('/offer/edit/{id}', [OfferController::class, 'edit'])->name('transaction.offer.edit');
    Route::post('/offer/generate', [OfferController::class, 'generate'])->name('transaction.offer.generate');

    //reception
    Route::get('/reception/reff/{year?}', [ReceptionController::class, 'index'])->name('transaction.reception.index');
    Route::get('/reception/list', [ReceptionController::class, 'getList'])->name('transaction.reception.list');
    Route::get('/reception/data', [ReceptionController::class, 'getData'])->name('transaction.reception.data');
    Route::get('/reception/pic', [ReceptionController::class, 'getPic'])->name('transaction.reception.pic');
    Route::get('/reception/view/{id}', [ReceptionController::class, 'view'])->name('transaction.reception.view');
    Route::get('/reception/add', [ReceptionController::class, 'add'])->name('transaction.reception.add');
    Route::get('/reception/edit/{id}', [ReceptionController::class, 'edit'])->name('transaction.reception.edit');
    Route::post('/reception/post/{action}/{id}', [ReceptionController::class, 'post'])->name('transaction.reception.post');

    //expense
    Route::get('/expense/reff/{year?}', [ExpenseController::class, 'index'])->name('transaction.expense.index');
    Route::get('/expense/list', [ExpenseController::class, 'getList'])->name('transaction.expense.list');
    Route::get('/expense/data', [ExpenseController::class, 'getData'])->name('transaction.expense.data');
    Route::get('/expense/pic', [ExpenseController::class, 'getPic'])->name('transaction.expense.pic');
    Route::get('/expense/view/{id}', [ExpenseController::class, 'view'])->name('transaction.expense.view');
    Route::get('/expense/add/{type}', [ExpenseController::class, 'add'])->name('transaction.expense.add');
    Route::get('/expense/edit/{id}', [ExpenseController::class, 'edit'])->name('transaction.expense.edit');
    Route::get('/expense/download/{id}', [ExpenseController::class, 'download'])->name('transaction.expense.download');
    Route::post('/expense/print/{id}', [ExpenseController::class, 'print'])->name('transaction.expense.print');
    Route::post('/expense/post/{action}/{id}', [ExpenseController::class, 'post'])->name('transaction.expense.post');

    //years
    Route::get('/years', [YearController::class, 'index'])->name('master.years.index');
    Route::get('/years/list', [YearController::class, 'getList'])->name('master.years.list');
    Route::post('/years/post/{action}/{id}', [YearController::class, 'post'])->name('master.years.post');

    //accountability
    Route::get('/accountability', [AccountabilityController::class, 'index'])->name('report.accountability.index');
    Route::get('/accountability/reception', [AccountabilityController::class, 'getReception'])->name('report.accountability.reception');
    Route::get('/accountability/expense', [AccountabilityController::class, 'getExpense'])->name('report.accountability.expense');
    Route::post('/accountability/post', [AccountabilityController::class, 'post'])->name('report.accountability.post');
    Route::get('/accountability/download/{id}', [AccountabilityController::class, 'download'])->name('report.accountability.download');

    //employee
    Route::get('/employee', [EmployeeController::class, 'index'])->name('master.employee.index');
    Route::get('/employee/list', [EmployeeController::class, 'getList'])->name('master.employee.list');
    // Route::get('/employee/view/{id}', [BalanceController::class, 'view'])->name('master.employee.view');
    Route::get('/employee/upload', [EmployeeController::class, 'upload'])->name('master.employee.upload');
    Route::get('/employee/add', [EmployeeController::class, 'add'])->name('master.employee.add');
    Route::get('/employee/edit/{id}', [EmployeeController::class, 'edit'])->name('master.employee.edit');
    Route::post('/employee/post/{action}/{id}', [EmployeeController::class, 'post'])->name('master.employee.post');
    Route::post('/employee/upload', [EmployeeController::class, 'postUpload'])->name('master.employee.postupload');

    //daily
    Route::get('/daily', [DailyController::class, 'index'])->name('report.daily.index');
    Route::post('/daily/post', [DailyController::class, 'post'])->name('report.daily.post');
    Route::get('/daily/download/{id}', [DailyController::class, 'download'])->name('report.daily.download');

    //recapitulation
    Route::get('/recapitulation/reff/{year?}', [RecapitulationController::class, 'index'])->name('report.recapitulation.index');
    Route::get('/recapitulation/list/division', [RecapitulationController::class, 'getListDivision'])->name('report.recapitulation.list.division');
    Route::get('/recapitulation/list/pic', [RecapitulationController::class, 'getListPic'])->name('report.recapitulation.list.pic');
    Route::get('/recapitulation/division/{id}', [RecapitulationController::class, 'division'])->name('report.recapitulation.division');
    Route::get('/recapitulation/data/{id}', [RecapitulationController::class, 'data'])->name('report.recapitulation.data');
    Route::get('/recapitulation/detail/division/', [RecapitulationController::class, 'getDetailDivision'])->name('report.recapitulation.division.detail');
    Route::get('/recapitulation/detail/data/', [RecapitulationController::class, 'getDetailData'])->name('report.recapitulation.data.detail');
});

