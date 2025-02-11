<?php

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
//     return view('welcome');
// });
Route::middleware('auth.by.token')->get('/', 'AuthController@signin2');
Route::get('/rapport/{compte}','ServiceController@afficheRapport');

Route::get('/rapDaf','ServiceController@afficheRapportDpaf');
Route::get('/rapportGraph','ServiceController@afficheRapportGraphe');
// Route::get('/rapinf','ServiceController@afficheRapportInf');
Route::get('/rapportPf','ServiceController@afficheRapportPf');

Route::get('relance_param','ServiceController@relanceArchierat');

