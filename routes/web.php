<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\OptionVoteController;
use App\Http\Controllers\OptionVotingController;
use App\Http\Controllers\ResolutionController;
use App\Http\Controllers\UserCompanyMapController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserLogController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\VotingReportController;
use App\Models\UserLog;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


/* <============================  CompanyController  ============================> */


Route::get('/policy', function () {
    return view('policy');
})->name('policy');

/* <============================  CompanyController  ============================> */
Route::get('/', function () {
    return view('welcome');
})->name('index');

Route::group(['middleware' => ['auth', 'userstatus']], function () {
    /* <============================  The Start  ============================> */

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('home');

    /* <============================  UserController  ============================> */

    Route::resource('users', UserController::class)->middleware(['permissioncheck']);
    Route::post('users/changestatus', [UserController::class, "changeStatus"])->name('users.changestatus');
    Route::get('user/change-password', [UserController::class, 'showChangePasswordForm'])->name('userpassword.change');
    Route::post('user/change-password', [UserController::class, 'updatePassword'])->name('userpassword.update');
});
Auth::routes();

/* <============================  Routes End  ============================> */