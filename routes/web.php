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

Auth::routes();

Route::group(['middleware' => ['auth','web']], function(){
  Route::get('/', 'HomeController@index')->name('home');
  Route::get('/home', 'HomeController@index')->name('home');

  // Admin Index
  Route::get('users','UserController@index')->name('users.index'); //->middleware('permission:role-list,role-create,role-edit,role-delete');
  Route::post('users_datatable','UserController@getUsersDatatable')->name('users.ajax.datatable');
  Route::get('users/create','UserController@create')->name('users.create'); //->middleware('permission:role-create');
  Route::post('users/create','UserController@store')->name('users.store'); //->middleware('permission:role-create');
  Route::get('users/{id}','UserController@show')->name('users.show');
  Route::get('users/{id}/edit','UserController@edit')->name('users.edit'); //->middleware('permission:role-edit');
  Route::put('users/{id}','UserController@update')->name('users.update'); //->middleware('permission:role-edit');
  Route::delete('users/{id}','UserController@destroy')->name('users.destroy'); //->middleware('permission:role-delete');

  // Role
  Route::get('roles','RoleController@index')->name('roles.index');//->middleware('permission:role-list,role-create,role-edit,role-delete');
  Route::post('roles_datatable','RoleController@getRolesDatatable')->name('roles.ajax.datatable');
  Route::get('roles/create','RoleController@create')->name('roles.create');//->middleware('permission:role-create');
  Route::post('roles/create','RoleController@store')->name('roles.store');//->middleware('permission:role-create');
  Route::get('roles/{id}','RoleController@show')->name('roles.show');
  Route::get('roles/{id}/edit','RoleController@edit')->name('roles.edit');//->middleware('permission:role-edit');
  Route::put('roles/{id}','RoleController@update')->name('roles.update');//->middleware('permission:role-edit');
  Route::delete('roles/{id}','RoleController@destroy')->name('roles.destroy');//->middleware('permission:role-delete');

  // Permission
  Route::get('permissions','PermissionController@index')->name('permissions.index'); //->middleware('permission:role-list,role-create,role-edit,role-delete');
  Route::post('permissions_datatable','PermissionController@getPermissionsDatatable')->name('permissions.ajax.datatable');
  Route::get('permissions/create','PermissionController@create')->name('permissions.create'); //->middleware('permission:role-create');
  Route::post('permissions/create','PermissionController@store')->name('permissions.store'); //->middleware('permission:role-create');
  Route::get('permissions/{id}','PermissionController@show')->name('permissions.show');
  Route::get('permissions/{id}/edit','PermissionController@edit')->name('permissions.edit'); //->middleware('permission:role-edit');
  Route::put('permissions/{id}','PermissionController@update')->name('permissions.update'); //->middleware('permission:role-edit');
  Route::delete('permissions/{id}','PermissionController@destroy')->name('permissions.destroy'); //->middleware('permission:role-delete');

  Route::prefix('rollstocks')->group(function () {
    Route::get('rollreceive/{type?}/{value?}','RollStock\RollReceiveController@index')->name('rollstocks.rollreceive.index');
    Route::post('rollreceive','RollStock\RollReceiveController@submit')->name('rollstocks.rollreceive.submit');

    Route::get('rollusage','RollStock\RollUsageController@index')->name('rollstocks.rollusage.index');
    Route::post('rollusage','RollStock\RollUsageController@submit')->name('rollstocks.rollusage.submit');

    Route::get('stock','RollStock\RollStockController@index')->name('rollstocks.stock.index');
    Route::post('stock','RollStock\RollStockController@submit')->name('rollstocks.stock.submit');
  });
});
