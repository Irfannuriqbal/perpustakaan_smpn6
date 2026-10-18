<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\MemberAuthController;
use App\Http\Controllers\MemberCardController;
use App\Http\Controllers\MemberPasswordController;
use App\Http\Controllers\MemberProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\VisitController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::get('/anggota/login', [MemberAuthController::class, 'index'])->name('members.login');
Route::post('/anggota/login', [MemberAuthController::class, 'store'])->name('members.login.store');
Route::middleware('member.auth')->prefix('anggota')->name('members.')->group(function (): void {
    Route::get('/kartu', [MemberCardController::class, 'show'])->name('card');
    Route::get('/profil', [MemberProfileController::class, 'show'])->name('profile');
    Route::get('/profil/edit', [MemberProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [MemberProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [MemberPasswordController::class, 'update'])->name('password.update');
    Route::post('/logout', [MemberAuthController::class, 'destroy'])->name('logout');
});

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/students/cards/print', [StudentController::class, 'printCards'])->name('students.cards.print');
    Route::get('/students/{student}/card/print', [StudentController::class, 'printCard'])->name('students.card.print');
    Route::get('/books/barcodes/print', [BookController::class, 'printBarcodes'])->name('books.barcodes.print');
    Route::get('/books/{book}/barcode/print', [BookController::class, 'printBarcode'])->name('books.barcode.print');
    Route::resource('students', StudentController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('books', BookController::class);
    Route::post('/loans/student', [LoanController::class, 'selectStudent'])->name('loans.student');
    Route::post('/loans/books', [LoanController::class, 'addBook'])->name('loans.books.add');
    Route::delete('/loans/books/{book}', [LoanController::class, 'removeBook'])->name('loans.books.remove');
    Route::delete('/loans/draft', [LoanController::class, 'resetDraft'])->name('loans.draft.reset');
    Route::post('/loans/{loan}/extend', [LoanController::class, 'extend'])->name('loans.extend');
    Route::resource('loans', LoanController::class);
    Route::post('/returns/search', [ReturnController::class, 'search'])->name('returns.search');
    Route::post('/returns/{loanItem}', [ReturnController::class, 'store'])->name('returns.store');
    Route::get('/returns', [ReturnController::class, 'index'])->name('returns.index');
    Route::get('/visits/kiosk', [VisitController::class, 'kiosk'])->name('visits.kiosk');
    Route::post('/visits/kiosk', [VisitController::class, 'storeKiosk'])->name('visits.kiosk.store');
    Route::resource('visits', VisitController::class);
    Route::get('/reports/{type}/pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');
    Route::get('/reports/{type}/excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::middleware('super.admin')->group(function (): void {
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::resource('users', UserManagementController::class)->except('show');
        Route::patch('/users/{user}/status', [UserManagementController::class, 'status'])->name('users.status');
        Route::get('/backups', [BackupController::class, 'index'])->name('backups.index');
        Route::post('/backups', [BackupController::class, 'store'])->name('backups.store');
        Route::get('/backups/{filename}/download', [BackupController::class, 'download'])->where('filename', 'library-\d{8}-\d{6}\.sql')->name('backups.download');
    });
});
