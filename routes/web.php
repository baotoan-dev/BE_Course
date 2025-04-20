<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\PagesController;

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

// Route::get('/', function () {
//     // return env('APP_NAME');
//     return view('welcome'); 
// });

// Route::get('/users', function () {
//     $users = [
//         '0' => [
//             'name' => 'John Doe',
//             'email' => 'join@gmail.com',
//             'phone' => '1234567890'
//         ],
//         '1' => [
//             'name' => 'Jane Doe',
//             'email' => 'jane@gmail.com',
//             'phone' => '0987654321'
//         ]
//     ];

//     $users = json_decode(json_encode($users));

//     return view('users', compact('users'));
// });

// Route::get('/products', [ProductsController::class, 'index']);

// :id
// Route::get('/products/{id}', [ProductsController::class, 'detail'])->where('id', '[0-9]+');

Route::get('/', [PagesController::class, 'index']);
Route::get('/about', [PagesController::class, 'about']);
