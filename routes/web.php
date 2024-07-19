<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ReviewController;

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

Route::get('/', function () {
    return redirect()->route('books.index');
});

// this well get all the routes in the BookBontroller class
// only we use it to define what the routes that we are need it from this class 
// other routes will be disabled
Route::resource('books', BookController::class)
    ->only('index', 'show');

// this is example of Scoping Resource Routes
// the url will be like this /photos/{photo}/comments/{comment:slug}
// we used it to use the relation with create form
Route::resource('books.reviews', ReviewController::class)
    ->scoped(['review' => 'book'])
    ->only('create','store');
