<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
  return $request->user();
});

//// Unauthenticated routes
// to get the info relevant to the current user without compromising info
Route::get('users/current', 'UserController@getCurrent');

// clear temporal cacheable data
Route::get('/clear-cache', function () {
  $result = '';
  Artisan::call('cache:clear');
  $result .= 'cache:clear<br>';
  Artisan::call('route:clear');
  $result .= 'route:clear<br>';
  Artisan::call('config:clear');
  $result .= 'config:clear<br>';
  Artisan::call('view:clear');
  $result .= 'view:clear<br>';
  Artisan::call('session:flush');
  $result .= 'session:flush<br>';
  Artisan::call('key:generate');
  $result .= 'key:generate<br>';
  return $result;
});

//// Authenticated routes
Route::middleware(['auth', 'auth:api'])->group(function () {
  // USERS
  Route::get('users', 'UserController@index')
    ->name('index users')->middleware('permission:index users');
  Route::post('users', 'UserController@store')
    ->name('create users')->middleware('permission:create users');
  Route::put('users/{id_user}', 'UserController@update')
    ->name('update users')->middleware('permission:update users');
  Route::delete('users/{id_user}', 'UserController@destroy')
    ->name('delete users')->middleware('permission:delete users');

  Route::post('users/current', 'UserController@storeCurrent');
  Route::get('users/roles', 'UserController@getRoles');
  Route::put('users/password/{email}', 'UserController@updatePassword');

  // ACTIONS
  Route::get('actions', 'ActionController@index')
    ->name('index actions')->middleware('permission:index actions'); // index
  Route::post('actions', 'ActionController@store')
    ->name('create actions')->middleware('permission:create actions');
  Route::put('actions/{id_action}', 'ActionController@update')
    ->name('update actions')->middleware('permission:update actions');
  Route::delete('actions/{id_action}', 'ActionController@destroy')
    ->name('delete actions')->middleware('permission:delete actions');

  // RESOURCES
  Route::get('resources', 'ResourceController@index')
    ->name('index resources')->middleware('permission:index resources');
  Route::post('resources', 'ResourceController@store')
    ->name('create resources')->middleware('permission:create resources');
  Route::put('resources/{id_resource}', 'ResourceController@update')
    ->name('update resources')->middleware('permission:update resources');
  Route::delete('resources/{id_resource}', 'ResourceController@destroy')
    ->name('delete resources')->middleware('permission:delete resources');

  // PERMISSIONS
  Route::get('permissions', 'PermissionController@index')
    ->name('index permissions')->middleware('permission:index permissions');

  // ROLES
  Route::get('roles', 'RoleController@index')
    ->name('index roles')->middleware('permission:index roles');
  Route::post('roles', 'RoleController@store')
    ->name('create roles')->middleware('permission:create roles');
  Route::put('roles/{id_role}', 'RoleController@update')
    ->name('update roles')->middleware('permission:update roles');
  Route::delete('roles/{id_role}', 'RoleController@destroy')
    ->name('delete roles')->middleware('permission:delete roles');

  // PARAMETERS
  Route::get('parameters', 'ParameterController@index')
    ->name('index parameters')->middleware('permission:index parameters');
  Route::post('parameters', 'ParameterController@store')
    ->name('create parameters')->middleware('permission:create parameters');
  Route::put('parameters/{id_account}', 'ParameterController@update')
    ->name('update parameters')->middleware('permission:update parameters');
  Route::delete('parameters/{id_account}', 'ParameterController@destroy')
    ->name('delete parameters')->middleware('permission:delete parameters');

  Route::get('tasks', 'TaskController@index')
    ->name('index tasks')->middleware('permission:index tasks');
  Route::post('tasks', 'TaskController@store')
    ->name('create tasks')->middleware('permission:create tasks');
  Route::put('tasks/{id}', 'TaskController@update')
    ->name('update tasks')->middleware('permission:update tasks');
  Route::delete('tasks/{id}', 'TaskController@destroy')
    ->name('delete tasks')->middleware('permission:delete tasks');
  Route::post('tasks/printpdf', 'TaskController@printpdf')
    ->name('printpdf tasks')->middleware('permission:index tasks');

  Route::get('milestones', 'MilestoneController@index')
    ->name('index milestones')->middleware('permission:index milestones');
  Route::post('milestones', 'MilestoneController@store')
    ->name('create milestones')->middleware('permission:create milestones');
  Route::put('milestones/{id}', 'MilestoneController@update')
    ->name('update milestones')->middleware('permission:update milestones');
  Route::delete('milestones/{id}', 'MilestoneController@destroy')
    ->name('delete milestones')->middleware('permission:delete milestones');
  Route::post('milestones/printpdf', 'MilestoneController@printpdf')
    ->name('printpdf milestones')->middleware('permission:index milestones');


});
