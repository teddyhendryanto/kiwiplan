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

  Route::get('/markAsRead/{notification_id}','NotificationController@markAsRead')->name('notifications.markAsRead');
  Route::get('/markAllAsRead','NotificationController@markAllAsRead')->name('notifications.markAllAsRead');


  // Setup
  Route::prefix('setups')->group(function(){
    Route::resource('notifications','NotificationController',['only' => ['index','create','store','edit','update','destroy']]);
    Route::post('notifications_datatable','NotificationController@getNotificationsDatatable')->name('notifications.ajax.datatable');
    Route::post('notifications_assign_show','NotificationController@assignShow')->name('notifications.ajax.assignShow');
    Route::post('notifications_assign_store','NotificationController@assignStore')->name('notifications.assignStore');
  });

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

  Route::prefix('accounting')->group(function () {
    Route::resource('purchase_orders','Accounting\PurchaseOrderController');
    Route::post('purchase_orders/{site}','Accounting\PurchaseOrderController@getPO')->name('purchase_orders.ajax.getPO');
    Route::post('purchase_orders/po/getLastPONumber','Accounting\PurchaseOrderController@getLastPONumber')->name('purchase_orders.ajax.getLastPONumber');
    Route::post('purchase_orders/po/getLastPONumberBefore','Accounting\PurchaseOrderController@getLastPONumberBefore')->name('purchase_orders.ajax.getLastPONumberBefore');
    Route::post('purchase_orders/supplier/getSupplierDetail','Accounting\PurchaseOrderController@getSupplierDetail')->name('purchase_orders.ajax.getSupplierDetail');
    Route::post('purchase_orders/deleteDetail/single','Accounting\PurchaseOrderController@deleteDetailSingle')->name('purchase_orders.ajax.deleteDetailSingle');

    Route::resource('exchange_rates','Accounting\ExchangeRateController');
    Route::post('exchange_rates/getExchangeRateDatatable','Accounting\ExchangeRateController@getExchangeRateDatatable')->name('exchange_rates.ajax.getExchangeRateDatatable');

    Route::get('rollreceivesummary','Accounting\RollReceiveSummaryController@index')->name('accounting.rollreceivesummary.index');
    Route::post('rollreceivesummary','Accounting\RollReceiveSummaryController@submit')->name('accounting.rollreceivesummary.submit');

    Route::get('rollusagesummary','Accounting\RollUsageSummaryController@index')->name('accounting.rollusagesummary.index');
    Route::post('rollusagesummary','Accounting\RollUsageSummaryController@submit')->name('accounting.rollusagesummary.submit');

    Route::get('stocksummary','Accounting\RollStockSummaryController@index')->name('accounting.stocksummary.index');
    Route::post('stocksummary','Accounting\RollStockSummaryController@submit')->name('accounting.stocksummary.submit');
  });

  Route::prefix('rollstocks')->group(function () {
    Route::prefix('setup')->group(function(){
      Route::resource('suppliers','RollStock\PaperSupplierController');
      Route::post('suppliers_datatable','RollStock\PaperSupplierController@getSupplierListDatatable')->name('suppliers.ajax.getSupplierListDatatable');

      Route::resource('qualities','RollStock\PaperQualityController');
      Route::post('qualities_datatable','RollStock\PaperQualityController@getQualityListDatatable')->name('qualities.ajax.getQualityListDatatable');

      Route::resource('gramatures','RollStock\PaperGramaturController');
      Route::post('gramatures_datatable','RollStock\PaperGramaturController@getGramaturListDatatable')->name('gramatures.ajax.getGramaturListDatatable');

      Route::resource('widths','RollStock\PaperWidthController');
      Route::post('widths_datatable','RollStock\PaperWidthController@getWidthListDatatable')->name('widths.ajax.getWidthListDatatable');

      Route::resource('keys','RollStock\PaperKeyController');
      Route::post('keys_datatable','RollStock\PaperKeyController@getKeyListDatatable')->name('keys.ajax.getKeyListDatatable');
    });

    Route::prefix('setup')->group(function(){
      Route::get('rollreceive/{type?}/{value?}','RollStock\RollReceiveController@index')->name('rollstocks.rollreceive.index');
      Route::post('rollreceive','RollStock\RollReceiveController@submit')->name('rollstocks.rollreceive.submit');

      Route::get('rollusage','RollStock\RollUsageController@index')->name('rollstocks.rollusage.index');
      Route::post('rollusage','RollStock\RollUsageController@submit')->name('rollstocks.rollusage.submit');

      Route::get('stock','RollStock\RollStockController@index')->name('rollstocks.stock.index');
      Route::post('stock','RollStock\RollStockController@submit')->name('rollstocks.stock.submit');
    });

    
  });
});
