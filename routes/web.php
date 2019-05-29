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
Route::middleware(['auth'])->group(function () {

    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    Route::get('/dashboard', 'Main\HomeController@dashboard')->name('dashboard');

    Route::get('/check-pdf', 'Main\CheckPdfController@index')->name('check-pdf');
    Route::post('/check-pdf-change', 'Main\CheckPdfController@change')->name('check-pdf-change');

    // converters
    Route::get('/convert-csv', 'Main\ConverterCsvController@index')->name('converter-csv');
    Route::get('/converters', 'Main\ConverterCsvController@converters')->name('converters');
    Route::match(['post', 'get'], '/converters/create', 'Main\ConverterCsvController@create')->name('converter-create');
    Route::match(['post', 'get'], '/converters/{converter}/update', 'Main\ConverterCsvController@update')->name('converter-update');
    Route::delete('/converters/{converter}/delete', 'Main\ConverterCsvController@delete')->name('converter-delete');

    // sets
    Route::get('/sets-rules', 'Main\SetRulesController@index')->name('sets-rules');
    Route::match(['post', 'get'], '/set-rules/create', 'Main\SetRulesController@create')->name('set-rules-create');
    Route::match(['post', 'get'], '/set-rules/{Set}/update', 'Main\SetRulesController@update')->name('set-rules-update');
    Route::delete('/set-rules/{Set}/delete', 'Main\SetRulesController@delete')->name
    ('rules-set-delete');

    // set rules
    Route::get('/sets-rules/{Set}/rules', 'Main\SetRulesController@rules')->name('rules-set');
    Route::match(['post', 'get'], '/set-rules/{Set}/create', 'Main\SetRulesController@ruleCreate')->name('rule-create');
    Route::match(['post', 'get'], '/set-rules/{Set}/update/{ConverterRule}', 'Main\SetRulesController@ruleUpdate')
        ->name('rule-update');
    Route::delete('/set-rules/{Set}/delete/{ConverterRule}', 'Main\SetRulesController@ruleDelete')->name
    ('rule-delete');
    Route::get('/set-rules/files/{ConverterRule}', 'Main\SetRulesController@downloadFile')
        ->name('get-file');
		
    Route::get('/set-rules/dl/{SetId}/{ConverterType}', 'Main\SetRulesController@downloadRuleFile')
        ->name('dl-file');
		
    //converting
    Route::post('/convert-csv-change', 'Main\ConverterCsvController@change')->name('converter-csv-change');
    Route::post('/set-rules-apply', 'Main\SetRulesController@change')->name('set-rules-apply');
	
	//UBI Upload
    Route::get('/ubi-upload', 'Main\UbiOrdersController@uploadindex')->name('ubi-upload');
    Route::get('/ubi-download-pdf', 'Main\UbiOrdersController@downloadpdf')->name('ubi-download-pdf');
    Route::match(['post', 'get'], '/ubi-forcast-orders', 'Main\UbiOrdersController@forcastorders')->name('ubi-forcast-orders');
    Route::post('/ubi-orders-api', 'Main\UbiOrdersController@uploadapi')->name('ubi-orders-api');

    // Tracking
    Route::match(['post', 'get'], '/tracking', 'Main\TrackingController@index')->name('tracking');
    
    //Invoice Orders
    Route::get('/invoice-orders', 'Main\InvoiceOrdersController@index')->name('invoice-orders');
    Route::post('/invoice-orders-upload', 'Main\InvoiceOrdersController@upload')->name('invoice-orders-upload');
    Route::get('/invoice-orders-datatables', 'Main\InvoiceOrdersController@datatables')->name('invoice-orders-datatables');
    Route::get('/invoice-orders-datatables-index', 'Main\InvoiceOrdersController@datatablesIndex')->name('invoice-orders-datatables-index');
	
	//Invoice Orders Search
    Route::get('/invoice-orders-search', 'Main\InvoiceOrdersController@OrderSearchIndex')->name('invoice-orders-search');
    Route::get('/invoice-orders-searchdata', 'Main\InvoiceOrdersController@getOrderSearchData')->name('invoice-orders-searchdata');
	

	//Products
    Route::get('/products', 'Main\ProductsController@index')->name('products-index');
    Route::get('/products-data', 'Main\ProductsController@getProductsData')->name('products-data');
    Route::match(['post', 'get'], '/products/create', 'Main\ProductsController@create')->name('products-create');
    Route::match(['post', 'get'], '/products/update/{product_id}', 'Main\ProductsController@update')->name('products-update');
    Route::match(['post', 'get'], '/products/delete/{product_id}', 'Main\ProductsController@delete')->name('products-delete');
	
	//Invoicing
    Route::get('/invoicing-index', 'Main\InvoiceOrdersController@InvoicingIndex')->name('invoicing-index');
    Route::get('/invoicing-xls', 'Main\InvoiceOrdersController@InvoicingXls')->name('invoicing-xls');
    
	//Upload Invoiced
    Route::get('/invoiced-index', 'Main\InvoiceOrdersController@InvoicedIndex')->name('invoiced-index');
    Route::post('/invoiced-upload', 'Main\InvoiceOrdersController@InvoicedUpload')->name('invoiced-upload');
	
    
    //Upload Orders
    Route::get('/uploaded-orders', 'Main\UploadedOrdersController@index')->name('uploaded-orders');
    Route::post('/uploaded-orders-upload', 'Main\UploadedOrdersController@upload')->name('uploaded-orders-upload');
    Route::get('/uploaded-orders-ubi', 'Main\UploadedOrdersController@ubiDownloadOrders')->name('uploaded-orders-ubi');
    Route::get('/upload-orders-mt', 'Main\UploadedOrdersController@downloadMTOrders')->name('upload-orders-mt');
    Route::get('/upload-orders-song', 'Main\UploadedOrdersController@downloadSongOrders')->name('upload-orders-song');
    Route::get('/ubi-orders-transfer', 'Main\UbiOrdersController@transferUbiOrders')->name('ubi-orders-transfer');
	
	Route::get('/importsong', 'Main\UploadedOrdersController@downloadSongOrders')->name('downloadSongOrders');
	Route::get('/importinvoiceorders', 'Main\UploadedOrdersController@updateNewInvoiceOrders')->name('updateNewInvoiceOrders');
    
    //Label Generator
    Route::get('/internal-generate', 'Main\LabelGeneratorController@index')->name('internal-generate');
    Route::post('/label-generate', 'Main\LabelGeneratorController@upload')->name('label-generate');
	
    //Invoice Generator
    Route::get('/commercial-invoice', 'Main\InvoiceGenerateController@index')->name('commercial-invoice');
    Route::post('/invoice-generate-upload', 'Main\InvoiceGenerateController@upload')->name('invoice-generate-upload');
    
});


Route::get('/uploaded-orders-download', 'Main\UploadedOrdersController@downloadWytOrders')->name('uploaded-orders-download');
Route::get('/testUpload', 'Main\UbiOrdersController@testUpload')->name('testUpload');
Route::match(['post', 'get'], '/uploaded-orders-test', 'Main\InvoiceOrdersController@test')->name('uploaded-orders-test');
Route::match(['post', 'get'], '/fixOw', 'Main\InvoiceOrdersController@fixOw')->name('fixOw');
Route::match(['post', 'get'], '/fixUbi', 'Main\UbiOrdersController@fix_ubi_cost')->name('fix_ubi_cost');
Route::match(['post', 'get'], '/fixProductCost', 'Main\InvoiceOrdersController@fixProductCost')->name('fixProductCost');
Route::match(['post', 'get'], '/1688-order-pdf', 'Main\InvoiceOrdersController@alibabaUpload')->name('1688-order-pdf');

Auth::routes();

