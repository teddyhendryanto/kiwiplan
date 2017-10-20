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

Route::group(['middleware' => ['web']], function(){
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
    Route::prefix('setup')->group(function(){
      Route::resource('purchase_order_frequents','Accounting\PurchaseOrderFrequentController');
      Route::post('purchase_order_frequents_datatable','Accounting\PurchaseOrderFrequentController@getPurchaseOrderFrequentDatatable')->name('purchase_order_frequents.ajax.getPurchaseOrderFrequentDatatable');
    });
    Route::resource('purchase_orders','Accounting\PurchaseOrderController');
    Route::get('purchase_orders/print/{id}','Accounting\PurchaseOrderController@print_po')->name('purchase_orders.print');
    Route::post('purchase_orders/site','Accounting\PurchaseOrderController@getPO')->name('purchase_orders.ajax.getPO');
    Route::post('purchase_orders/po/getLastPONumber','Accounting\PurchaseOrderController@getLastPONumber')->name('purchase_orders.ajax.getLastPONumber');
    Route::post('purchase_orders/po/getLastPONumberBefore','Accounting\PurchaseOrderController@getLastPONumberBefore')->name('purchase_orders.ajax.getLastPONumberBefore');
    Route::post('purchase_orders/supplier/getSupplierDetail','Accounting\PurchaseOrderController@getSupplierDetail')->name('purchase_orders.ajax.getSupplierDetail');
    Route::post('purchase_orders/deleteDetail/single','Accounting\PurchaseOrderController@deleteDetailSingle')->name('purchase_orders.ajax.deleteDetailSingle');

    Route::resource('purchase_order_transfers','Accounting\PurchaseOrderTransferController',['only' => ['index','create','store','show']]);

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
      Route::post('keys/getPaperKeyByQuality','RollStock\PaperKeyController@getPaperKeyByQuality')->name('keys.ajax.getPaperKeyByQuality');
      Route::post('keys_datatable','RollStock\PaperKeyController@getKeyListDatatable')->name('keys.ajax.getKeyListDatatable');
    });

    Route::prefix('receiving')->group(function(){
      Route::resource('receiveroll','RollStock\ReceiveRollController',['only' => ['index','create','store','edit','update','destroy']]);
      Route::get('receiveroll/delete/{id}','RollStock\ReceiveRollController@delete')->name('receiveroll.delete');
      Route::get('receiveroll/editCustom','RollStock\ReceiveRollController@editCustom')->name('receiveroll.edit.custom');
      Route::post('receiveroll/getPODetail','RollStock\ReceiveRollController@getPODetail')->name('receiveroll.ajax.getPODetail');
      Route::post('receiveroll/getRollID/fox','RollStock\ReceiveRollController@getFoxRollID')->name('receiveroll.ajax.getFoxRollID');
      Route::post('receiveroll/showHistory','RollStock\ReceiveRollController@showHistory')->name('receiveroll.showHistory');
      Route::post('receiveroll/showHistory/custom','RollStock\ReceiveRollController@showHistoryCustom')->name('receiveroll.showHistory.custom');
    });

    Route::prefix('verification')->group(function(){
      Route::resource('verifyroll','RollStock\VerifyRollController',['only' => ['index', 'create', 'store']]);
      Route::get('verifyroll/unverified','RollStock\VerifyRollController@unverified')->name('verifyroll.unverified');
      Route::post('verifyroll/unverified','RollStock\VerifyRollController@unverified_store')->name('verifyroll.unverified.store');
      Route::get('verifyroll/delete/{id}','RollStock\VerifyRollController@delete')->name('verifyroll.delete');
      Route::post('verifyroll/showHistory','RollStock\VerifyRollController@showHistory')->name('verifyroll.showHistory');
      Route::post('verifyroll/showVerification','RollStock\VerifyRollController@showVerification')->name('verifyroll.showVerification');
    });

    Route::prefix('export')->group(function(){
      Route::resource('edi','RollStock\EdiExportController',['only' => ['index', 'show']]);
      Route::get('edi/export_process/{exec_type}','RollStock\EdiExportController@export_process')->name('edi.export_process');
      Route::post('edi/showHistory','RollStock\EdiExportController@showHistory')->name('edi.showHistory');
      Route::post('edi/showHistory/byUniqueRollId','RollStock\EdiExportController@showHistoryByUniqueRollId')->name('edi.showHistory.byUniqueRollId');
    });

    Route::prefix('realization')->group(function(){
      Route::resource('purchase_order_realizations','RollStock\PurchaseOrderRealizationController',['except' => ['show','destroy']]);
      Route::post('purchase_order_realizations/showHistory','RollStock\PurchaseOrderRealizationController@showHistory')->name('purchase_order_realizations.showHistory');
      Route::get('purchase_order_realizations/delete/{id}','RollStock\PurchaseOrderRealizationController@delete')->name('purchase_order_realizations.delete');
    });

    Route::prefix('reports')->group(function(){
      Route::get('rollreceive/{type?}/{value?}','RollStock\RollReceiveController@index')->name('rollstocks.rollreceive.index');
      Route::post('rollreceive','RollStock\RollReceiveController@submit')->name('rollstocks.rollreceive.submit');

      Route::get('rollusage','RollStock\RollUsageController@index')->name('rollstocks.rollusage.index');
      Route::post('rollusage','RollStock\RollUsageController@submit')->name('rollstocks.rollusage.submit');

      Route::get('stock','RollStock\RollStockController@index')->name('rollstocks.stock.index');
      Route::post('stock','RollStock\RollStockController@submit')->name('rollstocks.stock.submit');
    });

    Route::get('notif/{status}','NotifController@index')->name('notif.index');

  });
});
