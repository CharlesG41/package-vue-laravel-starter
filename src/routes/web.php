<?php

//use Cyvian\Src\App\Http\Controllers\AuthController;
//use Cyvian\Src\App\Http\Controllers\BaseController;
//use Cyvian\Src\App\Http\Controllers\FormsController;
//use Cyvian\Src\App\Http\Controllers\ViewsController;
//use Cyvian\Src\App\Http\Controllers\ListsController;
//use Cyvian\Src\App\Http\Controllers\ActionsController;
//use Cyvian\Src\App\Http\Controllers\FileManagerController;
//use Cyvian\Src\App\Models\User;
use Illuminate\Support\Facades\Route;

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
Route::get('cms', function() {
    return view('cms');
});

//
//Route::get('login', [ViewsController::class, 'login'])->name('login');
//Route::get('logout', [AuthController::class, 'logout'])->name('cyvian.logout');
//Route::post('login', [AuthController::class, 'login']);
//Route::get('migrate', [BaseController::class, 'migrate']);
//Route::get('seed', function() {
//    User::create([
//        'name' => 'Charles Giguere',
//        'email' => 'junk141702@gmail.com',
//        'info' => [
//            'password' => 'allo',
//            'api_token' => null,
//            'roles' => [1],
//            'preferred_locale' => 'fr'
//        ]
//    ]);
//});
//
//Route::middleware(['auth'])->group(function () {
//    Route::get('{entryType}/create', [ViewsController::class, 'create'])->name('manager.create');
//    Route::get('{entryType}/edit/{id}', [ViewsController::class, 'edit'])->name('manager.edit');
//
//    Route::post('/{entryType}/store', [FormsController::class, 'store'])->name('manager.store');
//    Route::post('{entryType}/update/{id}', [FormsController::class, 'update'])->name('manager.update');
//
////    Route::get('{entryType}/base_fields', [BaseController::class, 'baseFields']);
//    Route::get('{entryTypeId}/entries', [BaseController::class, 'entries'])->name('entries.all');
//    Route::get('{entryTypeId}/rows', [BaseController::class, 'rows'])->name('entries.rows');
//    Route::get('{entryType}/rows/{id}', [ListsController::class, 'rowById']);
//    Route::get('many_entries/{ids}', [BaseController::class, 'entriesForFields'])->name('base.entries_for_fields');
//    Route::get('many_entries_entry_type_only/{ids}', [BaseController::class, 'entriesForFieldsEntryTypeOnly'])->name('base.entries_for_fields_entry_type_only');
//    Route::get('entry_types', [BaseController::class, 'entryTypes'])->name('entry_types.all');
//    Route::get('entry_types/{entryTypeId}/getListData', [BaseController::class, 'getListData'])->name('entry_types.get_list_data');
//
//    Route::post('{entryType}/actions/{actionName}', [ActionsController::class, 'execute'])->name('manager.action');
//
//    Route::get('switch_locale/{locale}', [BaseController::class, 'switchLocale'])->name('base.switch_locale');
//
//    Route::get('file_manager', [FileManagerController::class, 'index'])->name('file_manager.index');
//    Route::get('file_manager/files/edit/{id}', [FileManagerController::class, 'editFile'])->name('file_manager.files.edit');
//    Route::get('file_manager/folders/edit/{id}', [FileManagerController::class, 'editFolder'])->name('file_manager.folders.edit');
//    Route::get('file_manager/items/{folderId?}', [FileManagerController::class, 'getItems'])->name('file_manager.items.get');
//    Route::post('file_manager/files/store', [FileManagerController::class, 'storeFile'])->name('file_manager.file.store');
//    Route::post('file_manager/files/update/{id}', [FileManagerController::class, 'updateFile'])->name('file_manager.file.update');
//    Route::delete('file_manager/files/delete/{id}', [FileManagerController::class, 'deleteFile'])->name('file_manager.file.delete');
//    Route::post('file_manager/folders/store', [FileManagerController::class, 'storeFolder'])->name('file_manager.folder.store');
//    Route::post('file_manager/folders/update/{id}', [FileManagerController::class, 'updateFolder'])->name('file_manager.folder.update');
//    Route::delete('file_manager/folders/delete/{id}', [FileManagerController::class, 'deleteFolder'])->name('file_manager.folder.delete');
//    // Route::get('/page/{slug}', [BaseController::class, 'page']);
//    // Route::get('/news/{slug}', [BaseController::class, 'news']);
//    Route::get('/{entry_type}', [ViewsController::class, 'index'])->name('manager.list');
//});
