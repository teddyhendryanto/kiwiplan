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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['middleware' => ['web']], function(){
  // Admin Index
   Route::get('admins','AdminController@index')->name('admins.index'); //->middleware('permission:role-list,role-create,role-edit,role-delete');
   Route::post('admins_datatable','AdminController@getAdminsDatatable')->name('admins.ajax.datatable');
   Route::get('admins/create','AdminController@create')->name('admins.create'); //->middleware('permission:role-create');
   Route::post('admins/create','AdminController@store')->name('admins.store'); //->middleware('permission:role-create');
   Route::get('admins/{id}','AdminController@show')->name('admins.show');
   Route::get('admins/{id}/edit','AdminController@edit')->name('admins.edit'); //->middleware('permission:role-edit');
   Route::put('admins/{id}','AdminController@update')->name('admins.update'); //->middleware('permission:role-edit');
   Route::delete('admins/{id}','AdminController@destroy')->name('admins.destroy'); //->middleware('permission:role-delete');

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
});
