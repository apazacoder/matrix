<?php
// Web Routes

use Illuminate\Support\Facades\Log;

// inicio
Route::get( '/', function () {
	return redirect()->route('login');
} );

// autenticación
Route::get( 'connect', 'Auth\LoginController@showLoginForm' )->name( 'login' );
Route::post( 'connect', 'Auth\LoginController@login' );
Route::post( 'disconnect', 'Auth\LoginController@logout' )->name( 'logout' );

// home SPA
Route::get( '/home', 'HomeController@index' )->name( 'home' );

// not found
Route::get( 'notfound', 'ErrorController@handleNotFound' )->name('notfound');

// test 419
Route::get( 'test', function () {
	return view( 'errors.419' );
} )->name('test');


// testing routes
//Route::get('test/exercises/index', 'TestExerciseController@index');

Route::get( 'pdftest', function () {
  return view( 'pdf.entry_form' );
} )->name('test');

// for any route that isn't handled before
Route::get( '/{any?}', 'HomeController@index' )->name( 'home' )->where( 'any', '.*' );
