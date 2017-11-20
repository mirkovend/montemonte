<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/','CashinvoiceController@index');

/*CASH INVOICE*/
Route::resource('cashinvoice','CashinvoiceController');
Route::get('cashinvoice/delete/{id}','CashinvoiceController@delete_item');
Route::get('cashinvoice/cancel/{id}','CashinvoiceController@cancel_invoice');

Route::post('cashinvoice/customer/create','CashinvoiceController@create_customer');
/*CHARGE INVOICE*/
Route::resource('chargeinvoice','ChargeinvoiceController');
Route::get('chargeinvoice/delete_item/{id}','ChargeinvoiceController@delete_item');
Route::get('chargeinvoice/edit_item/{id}','ChargeinvoiceController@edit_item');
Route::get('chargeinvoice/cancel/{id}','ChargeinvoiceController@cancel_invoice');

Route::post('chargeinvoice/customer/create','ChargeinvoiceController@create_customer');
Route::patch('chargeinvoice/edit_item/{id}','ChargeinvoiceController@update_item');

/*DELIVERY RECEIPT*/
Route::resource('deliveryreceipt','DeliveryreceiptController');
Route::get('deliveryreceipt/delete/{id}','DeliveryreceiptController@delete_item');
Route::get('deliveryreceipt/delete_house/{id}','DeliveryreceiptController@delete_house_item');

/*CHART OF ACCOUNTS*/
Route::resource('chartofaccount','ChartofaccountController');
Route::get('chartofaccount/ap/{id}','ChartofaccountController@ap_show');
Route::get('chartofaccount/ap/{id}','ChartofaccountController@ar_show');
Route::get('chartofaccount/ap/{id}','ChartofaccountController@asset_show');
Route::get('chartofaccount/ap/{id}','ChartofaccountController@cash_equivalent_show');
Route::post('chartofaccount_data','ChartofaccountController@manageCoa');
Route::delete('chartofaccount_data/api/delete/{id}','ChartofaccountController@deleteCoa');
Route::get('chartofaccount/delete/{id}','ChartofaccountController@delete_item');
Route::get('chartofaccount/general_journal/create','ChartofaccountController@create_journal');
Route::post('chartofaccount/general_journal/create','ChartofaccountController@journal_store');

/*DETAIL TYPES (CHART OF ACCOUNTS TYPES)*/
Route::resource('detailtype','DetailtypeController');
Route::get('detailtype/delete/{id}','DetailtypeController@delete_item');

/*CHART OF ACCOUNT SUB ACCOUNT LEVEL 1*/
Route::resource('coaitem','CoaitemController');
Route::get('coaitem/delete/{id}','CoaitemController@delete_item');

/*CHART OF ACCOUNT SUB ACCOUNT LEVEL 2*/
Route::resource('coasubitem','CoasubitemController');
Route::get('coasubitem/delete/{id}','CoasubitemController@delete_item');

/*CHART OF ACCOUNT SUB ACCOUNT LEVEL 3*/
Route::resource('coasubitemthree','Coasubitem_threeController');
Route::get('coasubitemthree/delete/{id}','Coasubitem_threeController@delete_item');

/*CHART OF ACCOUNT SUB ACCOUNT LEVEL 4*/
Route::resource('coasubitemfour','Coasubitem_fourController');
Route::get('coasubitemfour/delete/{id}','Coasubitem_fourController@delete_item');

/*PURCHASE ORDER*/
Route::resource('purchaseorder','PurchaseorderController');
Route::get('purchaseorder/delete/{id}','PurchaseorderController@delete_item');

Route::post('purchaseorder/supplier/create','PurchaseorderController@create_supplier');

/*PETTY CASH*/
Route::resource('pettycash','PettycashController');
Route::get('pettycash/delete/{id}','PettycashController@delete_item');

Route::post('pettycash/supplier/create','PettycashController@create_supplier');
/*JOB ORDER*/
Route::resource('joborder','JoborderController');
Route::get('joborder/delete/{id}','JoborderController@delete_item');

Route::post('joborder/supplier/create','JoborderController@create_supplier');

/*VOUCHER*/
Route::resource('voucher','VoucherController');
Route::get('voucher/delete/{id}','VoucherController@delete_item');
Route::get('voucher/print/{id}','VoucherController@print_voucher');

Route::post('voucher/create','VoucherController@search_po_list');

/*SUPPLIERS*/
Route::resource('supplier','SupplierController');
Route::get('supplier/delete/{id}','SupplierController@delete_item');

/*CUSTOMER*/
Route::resource('customer','CustomerController');
Route::get('customer/view_jacket/{id}','CustomerController@view_jacket');

/*TERMS*/
Route::resource('term','TermController');
Route::get('term/delete/{id}','TermController@delete_item');

/*ITEM TYPE*/
Route::resource('itemtype','ItemtypeController');
Route::get('itemtype/delete/{id}','ItemtypeController@delete_item');

/*ITEMS*/
Route::resource('item','ItemController');
Route::get('item/delete/{id}','ItemController@delete_item');
Route::get('loadSubaccount','ItemController@loadSubaccount');
Route::get('managecat','ItemController@manage_cat');
Route::get('managecat/edit','ItemController@cat_edit');
Route::post('managecat/delete','ItemController@cat_delete');

Route::post('item/{id}/search','ItemController@search');
Route::resource('items/categories','CategoryController');
Route::get('items/categories/{id}/delete','CategoryController@delete');
/*INVENTORY*/
Route::resource('inventory','InventoryController');
Route::get('inv_item','InventoryController@items');

/*PAYMENT*/
Route::resource('payment','PaymentController');

Route::resource('vouchers-payment','VouchersPaymentController');

Route::post('payment/create','PaymentController@search_invoice');
Route::post('payment/create/voucher_payments','PaymentController@store_voucher_payment');
Route::get('payment/receive/{invoice}','PaymentController@receive_pay');
Route::post('payment/receive/{invoice}','PaymentController@receive_payment');
Route::post('payments/test','PaymentController@test');
/*DEPOSIT*/
Route::resource('deposit','DepositController');
Route::get('deposit/delete/{id}','DepositController@delete_item');

/*BANK RECONCILATION*/
Route::resource('bankreconcilation','BankreconcilationController');
Route::get('bank_deposit','BankreconcilationController@bank_deposit');
Route::get('bank_payment','BankreconcilationController@bank_payment');
Route::get('bank_account','BankreconcilationController@bank_account');
Route::get('verify_bank_recon','BankreconcilationController@verify_bank_recon');

/*AMORTIZATION*/
Route::resource('amortization','AmortizationController');
Route::get('amortization/delete/{id}','AmortizationController@delete_item');
Route::get('amortization/print/{id}','AmortizationController@print_report');

Route::get('amortization/mortality/{id}','AmortizationController@mortality_index');
Route::get('amortization/mortality/create/{id}','AmortizationController@create_mortality');

Route::post('amortization/mortality/create/{id}','AmortizationController@store_mortality');

/*REPORTING*/
Route::get('print/report/sales_report','ReportsController@view_sales_report');
Route::post('print/report/sales_report','ReportsController@print_sales_report');

Route::get('report/inventory_report','ReportsController@inventory_report');
Route::post('report/inventory_report','ReportsController@inventory_report_store');

Route::get('report/purchases_report','ReportsController@purchases_report_view');
Route::post('report/purchases_report','ReportsController@purchases_type');
Route::get('report/purchases_report/data','ReportsController@purchases_report_data');

Route::get('report/purchases_report/item_summary','ReportsController@item_summary');
Route::get('report/purchases_report/vendor_details','ReportsController@purchase_vendor_detail');
Route::get('report/purchases_report/vendor_summary','ReportsController@vendor_summary');

Route::get('report/sales_report','ReportsController@sales_report');
Route::get('report/sales_report/data','ReportsController@sales_report_detail');
Route::get('report/sales_report/data_cust','ReportsController@sales_report_cust');
Route::get('report/sales_report/data_cust_summary','ReportsController@sales_report_cust_summary');


Route::get('report/payable/','ReportsController@ap_vendor_view');
Route::get('report/payable/ap_aging_summary_data','ReportsController@ap_aging_summary');
Route::get('report/payable/vendor_balance_detail_data','ReportsController@vendor_balance_detail');
Route::get('report/payable/vendor_balance_summary_data','ReportsController@vendor_balance_summary');




Route::post('report/sales_report','ReportsController@sales_report_type');

Route::get('report/recievable','ReportsController@recievable_report');
Route::get('report/recievable/customer_balance_detail','ReportsController@customer_balance_detail');
Route::get('report/recievable/customer_balance_summary','ReportsController@customer_balance_summary');
Route::get('report/recievable/customer_aging_detail','ReportsController@customer_aging_detail');
Route::get('report/recievable/customer_aging_summary','ReportsController@customer_aging_summary');

Route::get('report/balance_sheet','ReportsController@balance_sheet_view');
Route::post('report/balance_sheet/balance_sheet_data','ReportsController@balance_sheet_data');
Route::post('report/balance_sheet/bs_collapse','ReportsController@bs_collapse');

Route::get('report/balance_sheet/detail','ReportsController@balance_sheet_detail_view');
Route::get('report/balance_sheet/detail/balance_sheet_detail_data','ReportsController@balance_sheet_detail_data');

Route::get('report/trial_balance','ReportsController@trialbalance_view');
Route::get('report/trial_balance/data','ReportsController@trial_balance');
Route::get('report/profitloss','ReportsController@profitloss_view');
Route::get('report/profitloss/data','ReportsController@profitloss');
Route::get('report/profitloss/profitloss_collapse','ReportsController@profitloss_collapse');
Route::get('report/collection','ReportsFormController@collection_report');
Route::post('report/collection','ReportsFormController@collection_data');

Route::get('generate/cash_sales','ReportsFormController@cash_sales_index');
Route::get('generate/cash_sales/create','ReportsFormController@cash_sales');
Route::get('generate/cash_sales/data','ReportsFormController@sr_data');
Route::post('generate/cash_sales/create','ReportsFormController@cash_sales_store');

Route::get('generate/daily_sales','ReportsFormController@index');
Route::get('generate/daily_sales/create','ReportsFormController@daily_sales');
Route::get('generate/daily_sales/type','ReportsFormController@type');
Route::post('generate/daily_sales/type','ReportsFormController@store');

Route::get('report/egg_production','Reports2Controller@egg_production');
Route::get('report/egg_production/header','Reports2Controller@egg_production_header');
Route::get('report/egg_production/data','Reports2Controller@egg_production_data');
/*USER*/
Route::resource('user','UseraccessController');
Route::get('user/resetpassword/{id}','UseraccessController@reset_password');
Route::get('user/status/{id}','UseraccessController@user_status');

/*USER AUTHENTICATION*/
Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');

Route::get('auth/register', 'Auth\AuthController@getRegister');
Route::post('auth/register', 'Auth\AuthController@postRegister');

Route::get('password/email', 'Auth\PasswordController@getEmail');
Route::post('password/email', 'Auth\PasswordController@postEmail');

Route::get('password/reset/{token}', 'Auth\PasswordController@getReset');
Route::post('password/reset', 'Auth\PasswordController@postReset');


Route::get('test','ReportsController@anydata');
Route::get('search','ReportsController@search');

Route::get('select','ItemController@selectItem');
Route::get('select/{id}','ItemController@selectedItem');

Route::get('customer_supplier','ChartofaccountController@customer_supplier');
