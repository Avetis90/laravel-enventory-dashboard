<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;

use App\Models\InvoiceOrders;
use App\Models\uploadedOrders;
use App\Models\Products;

use App\Services\UploadHelper;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Excel;

use \Exception;

class InvoiceOrdersController extends Controller
{
    
    public function index()
    {
        return view('invoice-orders.index');
    }

    public function upload(request $request)
    {
        $this->validate($request, [
            'file' => [
                'bail',
                'required',
            ],
        ]);
		
		if ($request->file('file')->getClientOriginalExtension() == "csv"){
            //$orderInfo = UploadHelper::parseInvoiceOrders($request->file('file'), $request->input('format'), $request);
			
			$UploadHelper = new UploadHelper();
            $orderInfo = $UploadHelper->parseInvoiceOrders($request->file('file'), $request->input('format'), $request);
            
            return view('invoice-orders.result', [
            'message' => $orderInfo['message'],
            'orders' => $orderInfo['orders'],
            'duplicateOrders' => $orderInfo['dupOrders'],
            ]);
            
		} else {
            return response("Wrong File Format", 200)->header('Content-Type', 'text/plain');
        }
		
		//return response(print_r($csvContent, true), 200)
		//				  ->header('Content-Type', 'text/plain');

	}
	
	/*
    
    private function parseInvoiceOrders($file, $format)
    {
        
        // Data for CSV building
        $csvContent = array();
        $orders = array();
        $duplicateOrders = array();
		$parse_prefix = array();
		$status = "unbilled";
		
		if(isset($request) && !empty($request->input('status'))){
			$status = $request->input('status');
		}
		
		if (isset($request) && !empty($request->input('prefix'))){
			$prefix = strtoupper(trim($request->input('prefix')));
			$parse_prefix = (stripos($request->input('parse_prefix'), ",") !== FALSE) ? explode(",", $request->input('parse_prefix')) : $request->input('parse_prefix');
		}
        if (empty($parse_prefix)){
            $parse_prefix = array("mz", "sa");
        }
        
        //order_id, sku, track_num, weight, shipping fee
        $formatFileOrder = array(
        'ow' => array(6, 30, 7, 28, 29),
        'pfc' => array(0, 11, 23, 16, 25),
        'ubi' => array(6, 30, 7, 28, 29),
        'wyt' => array(0, 100, 1, 5, 9)
        );
        
        $orderId_order = $formatFileOrder[$format][0];
        $sku_order = $formatFileOrder[$format][1];
        $track_num_order = $formatFileOrder[$format][2];
        $weight_order = $formatFileOrder[$format][3];
        $shipping_order = $formatFileOrder[$format][4];
        
        if (($handle = fopen($file, "r")) !== FALSE) {
            $header = null;
            while ($row = fgetcsv($handle)) {
                if ($header === null) {
                    $header = remove_utf8_bom($row);
                    continue;
                }
                
                //$csvContent[] = array_combine($header, $row);
                $saveOrder = false;
                
                $orderId = strtoupper($row[$orderId_order]);
                
                if (stripos($orderId, "#") === FALSE && (stripos($orderId, "-") === FALSE || stripos($orderId, "RMA") !== FALSE)){
                    $orderId = substr_replace($orderId, "#", 2, 0);
                }
                
                $orderExist = InvoiceOrders::where('order_id', $orderId)->first();
                $trackNumExist = InvoiceOrders::where('track_num', $row[$track_num_order])->first();
                if (in_array($orderId, array_column($orders, 'order_id'))){
                    $orderId = $orderId . "-DUP";
                }
                
                if (is_array($parse_prefix)){
                    foreach ($parse_prefix as $search_prefix){
                        if (stripos($orderId, trim($search_prefix)) !== FALSE){
                            $saveOrder = true;
                            $prefix = strtoupper($search_prefix);
                            break;
                        }
                    }
                } else {
                    $saveOrder = (stripos($orderId, trim($parse_prefix)) !== FALSE) ? true : false;
                }
                
                if ($saveOrder){
                    $weight = ($format == "ow") ? ($row[$weight_order] / 1000) : $row[$weight_order];
                    //$skus = ($sku_order > 50) ? uploadedOrders::where('order_id', $orderId)->first()->skus : $row[$sku_order];
                    if ($sku_order > 50){
                        $skus = (uploadedOrders::where('order_id', $orderId)->first()) ? uploadedOrders::where('order_id', $orderId)->first()->skus : "";
                    } else {
                        $skus = $row[$sku_order];
                    }
                    
                    if (stripos($skus, ")*1,") !== FALSE){
                        $skus = rtrim(ltrim(trim($skus), "("), ")*1,");
                    }
                    
                    $product_cost = $this->getProductCost($skus);
                    $error_message = (isset($product_cost['error'])) ? " " . $product_cost['error'] : "";
                    
                    if (($orderExist && $trackNumExist) || ($trackNumExist)){
                        $duplicateOrders[] = array(
                        'order_id' => $orderId,
                        'prefix' => $prefix,
                        'sku' => trim($skus),
                        'track_num' => $row[$track_num_order],
                        'weight' => $weight,
                        'product_cost' => $product_cost['subtotal'],
                        'shipping_cost' => $row[$shipping_order],
                        'ship_company' => $format,
                        'status' => trim($status) . $error_message,
                        );
                    } else {
                        if ($orderExist && !$trackNumExist){
                            $orderId = $orderId . "(RMA)";
                        }
                        $orders[] = array(
                        'order_id' => $orderId,
                        'prefix' => $prefix,
                        'sku' => trim($skus),
                        'track_num' => $row[$track_num_order],
                        'weight' => $weight,
                        'product_cost' => $product_cost['subtotal'],
                        'shipping_cost' => $row[$shipping_order],
                        'ship_company' => $format,
                        'status' => trim($status) . $error_message,
                        );
                    }
                    
                }
            }
        }
        
        if (InvoiceOrders::insert($orders)){
            $message = "Successfully Inserted " . count($orders) . " orders. Duplicate Orders: " . count($duplicateOrders);
        } else {
            $message = "Error";
        }
        
		$orderInfo['orders'] = $orders;
		$orderInfo['dupOrders'] = $duplicateOrders;
		$orderInfo['message'] = $message;
        
        return $orderInfo;
    }
	*/
    
    
    public function InvoicedIndex()
    {
        return view('invoice-orders.invoiced');
    }
    
    public function InvoicedUpload(request $request)
    {
        $this->validate($request, [
            'file' => [
            'bail',
            'required',
            ],
        ]);
        
        if ($request->hasFile('file')){
            $file_path = $request->file('file')->getRealPath();
            Excel::load($file_path, function($reader) use ($request) {
                $success_count = 0;
                $failed_count = 0;
                foreach ($reader->toArray() as $row) {
                    if ($row['order_id']){
                        $order_id = $request->input('prefix') . $row['order_id'];
                        $update_status = DB::table('invoice_orders')->where('order_id', $order_id)->update(['status' => 'invoiced']);
                        if ($update_status) {
                            $success_count++;
                        } else {
                            $failed_count++;
                            $failed_message .= $order_id . " Failed to Update";
                        }
                    }
                }
                echo $success_count . " Successfully Updated <br />";
                echo $failed_count . " Failed to Update";
                echo $failed_message;
            })->get();
        }
        
        //
    }
    
    public function InvoicingIndex()
    {
        return view('invoice-orders.invoicing');
    }
    
    // Download Orders
    public function InvoicingXls(request $request)
    {
        $prefix = $request->input('prefix');
		
		$orders = InvoiceOrders::select(['order_id', 'track_num', 'sku', 'weight', 'product_cost', 'shipping_cost'])
								->where([
									['status', '=' , 'unbilled'],
									['order_id', 'LIKE' , $prefix . "%"],
									//['id', '>' , 39901],
									//['id', '>' , 48884],
									['id', '>' , 54515],
								])
								->get();
		
		return Excel::create($prefix . '-FulfillmentOrders', function($excel) use ($orders) {

			$excel->sheet('Sheetname', function($sheet) use ($orders) {

				$sheet->fromModel($orders);

			});

		})->export('xls');
    }
    
    public function OrderSearchIndex()
    {
        return view('invoice-orders.ordersearch');
    }
    
    public function getOrderSearchData(request $request)
    {
		$orders = InvoiceOrders::select(['order_id', 'track_num', 'sku', 'weight', 'product_cost', 'shipping_cost', 'status']);

        //return datatables(InvoiceOrders::take(20)->get())->toJson();
		return datatables()->of($orders)
				->filter(function ($query) use ($request) {
					if ($request->has('order_id')) {
						$query->where('order_id', 'like', "%{$request->get('order_id')}%");
					}
				})
				->toJson();

    }
    
    public function datatablesIndex()
    {
        return view('invoice-orders.datatable');
    }
    
    public function datatables()
    {
        return datatables(InvoiceOrders::take(20)->get())->toJson();        
    }
    
    public function test(request $request)
    {
        
        //Log::info('File: ' . print_r($request->all(), true));
        //Storage::put('tmp/OwOrders.xlsx', $request->files('xlsx'));
        $request->file('xlsx')->storeAs('tmp', "OW-Testsave.xlsx");
        Excel::load($request->file('xlsx'))->setFilename('OW-Testsave')->store('csv', Storage::path('tmp/'));
        
        //if ($stored_file) {
            //Log::info("File: " . $stored_file);
            //Log::info("File Storage Path: " . Storage::path('tmp/') . "OW-Testsave.csv");
			$owSaveFile = Storage::path('tmp/') . "OW-Testsave.csv";
			$format = "ow";
			
			$UploadHelper = new UploadHelper();
            $orderInfo = $UploadHelper->parseInvoiceOrders($owSaveFile, $format, $request);
            /*
            return view('invoice-orders.result', [
				'message' => $orderInfo['message'],
				'orders' => $orderInfo['orders'],
				'duplicateOrders' => $orderInfo['dupOrders'],
            ]);
			*/
        //} else {
        //    $state = "NO File";
        //}
        
        return response()->json([
            'status' => 'success',
            'state' => $orderInfo['message']
        ]);
        
        /*
        $this->validate($request, [
            'file' => [
                'bail',
                'required',
            ],
        ]);
		
		//$userUpload = $request->file('file');
		if($request->file('file')->storeAs('files', "OW-Testsave.xlsx")){
            return "Success Uploaded";
        } else {
            return "Failed Nig";
        }
        */
		
        //$order_id = "SA#43604";
        //print_r(uploadedOrders::where('order_id', $order_id)->first());
		
      //echo uploadedOrders::where('order_id', 'SA36280')->first()->skus;
	  /*
        $orderId = "MZ3000";
        
        if (stripos($orderId, "#") === FALSE && stripos($orderId, "-") === FALSE){
            $orderId = substr_replace($orderId, "#", 2, 0);
        }
        
        echo $orderId;
	*/
        //print_r($this->getProductCost("001-149(1) , 001-180(1) , 001-163(1) , 001-22902(1) , 001-112(4)"));
	}
	
	public function fixOw()
	{
		$UploadHelper = new UploadHelper();
		
		$orders = InvoiceOrders::select(['id', 'order_id', 'track_num', 'sku', 'product_cost', 'status'])
								->where([
									['status', 'LIKE' , 'unbilled NF%'],
									['ship_company', '=' , 'ow']
								])
								->get();
		
		foreach ($orders as $order){
			
			$repalceStrings = array("塑料产品", "*1;", "美", "工具盒");
			$fSku = trim(str_replace($repalceStrings, "", $order->sku));
			
			$orderInfo = $UploadHelper->getProductCost($order->sku);
			$fOrderInfo = $UploadHelper->getProductCost($fSku);
			
			echo "Order number: " . $order->order_id . "<br/ >";
			echo "Order info: " . print_r($orderInfo, true) . "<br/ >";
			echo "F Order info: " . print_r($fOrderInfo, true) . "<br/ >";
			echo "<br />";
			echo "<br />";
			
			if (!isset($fOrderInfo['error'])){
				$updateDetails = array('status' => 'unbilled', 'product_cost' => $fOrderInfo['subtotal']);
				DB::table('invoice_orders')->where('id', $order->id)->update($updateDetails);
			} else {
				echo $order->id . ": " . $order->order_id . " not found. Error: " . $fOrderInfo['error'] . "<br /><br />";
			}
		}
		
		
	}
	
	public function fixProductCost()
	{
		$UploadHelper = new UploadHelper();
		InvoiceOrders::select(['id', 'order_id', 'track_num', 'sku', 'status'])
								->where([
									['order_id', 'LIKE' , 'SA%'],
									['product_cost', '=' , ''],
									['status', '=' , 'unbilled'],
									['id', '>' , '54515']
									//['id', '>' , '39902']
									//['id', '>' , '44900']
								])
								->chunk(100, function ($orders) use ($UploadHelper) {
									foreach ($orders as $order){
										
										if ($order->sku != ""){
											$fOrderInfo = $UploadHelper->getProductCost($order->sku);
											//$fOrderInfo = $UploadHelper->getProductCost($fSku);
											
											echo "Order number: " . $order->order_id . "<br/ >";
											echo "Track number: " . $order->track_num . "<br/ >";
											echo "fSku: " . $order->sku . "<br/ >";
											//echo "Order info: " . print_r($orderInfo, true) . "<br/ >";
											echo "F Order info: " . print_r($fOrderInfo, true) . "<br/ >";
											echo "<br />";
											echo "<br />";
											
											
											if (!isset($fOrderInfo['error'])){
												$updateDetails = array('product_cost' => $fOrderInfo['subtotal']);
												DB::table('invoice_orders')->where('id', $order->id)->update($updateDetails);
											} else {
												echo $order->id . ": " . $order->order_id . " not found. Error: " . $fOrderInfo['error'] . "<br /><br />";
											}
										}
									}
								});
		
	}
    
    public function alibabaUpload(request $request)
    {
        
        //Log::info('File: ' . print_r($request->all(), true));
        //Storage::put('tmp/OwOrders.xlsx', $request->files('xlsx'));
        $request->file('pdf')->storeAs('tmp', "1688.pdf");
            /*
            return view('invoice-orders.result', [
				'message' => $orderInfo['message'],
				'orders' => $orderInfo['orders'],
				'duplicateOrders' => $orderInfo['dupOrders'],
            ]);
			*/
        //} else {
        //    $state = "NO File";
        //}
        
        return response()->json([
            'status' => 'success',
            'state' => $orderInfo['message']
        ]);
        
        /*
        $this->validate($request, [
            'file' => [
                'bail',
                'required',
            ],
        ]);
		
		//$userUpload = $request->file('file');
		if($request->file('file')->storeAs('files', "OW-Testsave.xlsx")){
            return "Success Uploaded";
        } else {
            return "Failed Nig";
        }
        */
		
        //$order_id = "SA#43604";
        //print_r(uploadedOrders::where('order_id', $order_id)->first());
		
      //echo uploadedOrders::where('order_id', 'SA36280')->first()->skus;
	  /*
        $orderId = "MZ3000";
        
        if (stripos($orderId, "#") === FALSE && stripos($orderId, "-") === FALSE){
            $orderId = substr_replace($orderId, "#", 2, 0);
        }
        
        echo $orderId;
	*/
        //print_r($this->getProductCost("001-149(1) , 001-180(1) , 001-163(1) , 001-22902(1) , 001-112(4)"));
	}
    
    
}