<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    UserController,
    MailConfigurationController,
    DebtorAttachmentController,
    DashboardController,
    ContactController,
    ActivityLogController
};
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group.
|
*/

// Public Routes
Route::get('/', fn() => view('welcome'))->name('index');
Route::get('/policy', fn() => view('policy'))->name('policy');

// Authenticated Routes
Route::middleware(['auth', 'userstatus'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('home');

    // User Management
    Route::resource('corporate-debtors', UserController::class)->middleware('permissioncheck');
    Route::post('corporate-debtors/changestatus', [UserController::class, 'changeStatus'])->name('corporate-debtors.changestatus');
    Route::get('user/change-password', [UserController::class, 'showChangePasswordForm'])->name('userpassword.change');
    Route::post('user/change-password', [UserController::class, 'updatePassword'])->name('userpassword.update');

    // Debtor Access Routes
    Route::middleware('debtor.access')->group(function () {

        // Contacts Management
        Route::resource('contacts', ContactController::class);
        Route::post('contacts/bulk-delete', [ContactController::class, 'bulkDelete'])->name('contacts.bulk-delete');
        Route::get('contacts-import', [ContactController::class, 'import'])->name('contacts.import');
        Route::get('contacts-import/sample', [ContactController::class, 'downloadSample'])->name('contacts.import.sample');
        Route::post('contacts-import/process', [ContactController::class, 'processImport'])->name('contacts.import.process');
        Route::get('contacts-by-debtor', [ContactController::class, 'getContactsByDebtor'])->name('contacts.by-debtor');

        // Mail Configuration
        Route::resource('mail-configurations', MailConfigurationController::class);
        Route::post('mail-configurations/bulk-delete', [MailConfigurationController::class, 'bulkDelete'])->name('mail-configurations.bulk-delete');
        Route::get('mail-configurations/{mailConfiguration}/report', [MailConfigurationController::class, 'report'])->name('mail-configurations.report');
        Route::post('mail-configurations/resend', [MailConfigurationController::class, 'resendMail'])->name('mail-configurations.resend');

        // Debtor Attachments
        Route::resource('debtor-attachments', DebtorAttachmentController::class);
        Route::get('debtor-attachments/download/{id}', [DebtorAttachmentController::class, 'download'])->name('debtor-attachments.download');
        Route::get('debtor-attachments-by-debtor', [DebtorAttachmentController::class, 'getByDebtor'])->name('debtor-attachments.by-debtor');

        // Activity Logs
        Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('activity-logs/export', [ActivityLogController::class, 'export'])->name('activity-logs.export');
    });
});

Route::get('/run-queue-secret-xyz123', function () {
    // Option A: Process queue (light version — only a few jobs)
    Artisan::call('queue:work', [
        '--stop-when-empty' => true,
        '--tries'           => 3,
        '--timeout'         => 60,
    ]);

    return 'Queue processed OK';
})->name('run-queue');

Route::get('/run-schedule-secret-xyz123', function () {
    Artisan::call('schedule:run');
    return 'Schedule processed OK';
})->name('run-schedule');

// Authentication Routes
Auth::routes();
