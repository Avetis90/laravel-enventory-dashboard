<?php
namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Services\Parsers\ParserFabric;
use App\Services\UploadHelper;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Models\Converter;
use App\Models\UbiOrders;
use App\Models\InvoiceOrders;
use App\Models\uploadedOrders;
use App\Models\Products;
use Excel;

use Illuminate\Support\Facades\DB;

use App\Components\Exceptions\DbException;

class UbiOrdersController extends Controller
{
	const WALLTECH_SERVER = 'http://cn.eTowertech.com';
	const ACCESS_TOKEN = 'pcluaC3Lixo-qiVRPN9tQ0';
	const SECRET_KEY = 'NrD2wYwBvY2fTD6XgbWkwg';
	
    public function index()
    {
		return view('ubi-upload.index');
    }
	
    public function uploadindex()
    {
		return view('ubi-upload.index');
    }

    public function uploadapi(request $request)
    {
        $this->validate($request, [
            'file' => [
                'bail',
                'required',
            ],
        ]);
		
        //$fileName = $request->file('file')->getClientOriginalName();
        //$request->file('file')->storeAs('files', $fileName);
        //$pathToFile = storage_path('app/files/' . $fileName);
		$pathToFile = $request->file('file')->getPathName();
		
        // Data for CSV building
        $csvContent = [];

        // Get raw data
        $csvData = $this->readCsvFile($pathToFile);
		
		$header = array();
		
		foreach ($csvData as $index => $row){
		  $header = remove_utf8_bom($row);
		  break;
		}
		
		$value = array();
		
		$i = 0;
		foreach ($csvData as $index => $row){
			if ($i != 0){
			  array_push($value, $row);
			  $indexNo = $i - 1;
			  $data[] = array_combine($header, $row);
			  $data[$indexNo]['orderItems'] = array(array("itemNo" => 1, "sku" => $data[$indexNo]['sku'], "description" => $data[$indexNo]['description'], "hsCode"  => "630629", "originCountry" => "CN", "unitValue" => $data[$indexNo]['invoiceValue'], "itemCount" => 1, "nativeDescription" => $data[$indexNo]['nativeDescription'], "weight" => $data[$indexNo]['weight'], "productURL" => "", "warehouseNo" => ""));
			}
			$i++;
		}
		
		$submit_data = $this->json_encode_unicode($data);
		
		//print_r($test_data);
		//echo "<br />";
		$reponses = $this->ubi_create_order($submit_data);
		$decodedReponses = json_decode($reponses);
		
		$i = 0;
		$packlist = [];
		$orderList = [];
		$orderNumList = [];
		$allOrderNumList = [];
		foreach ($decodedReponses as $response)
		{
			if ($response->status != "Failed"){
				$packlist[$i]['referenceNo'] = $response->referenceNo;
				$packlist[$i]['trackingNo'] = ($response->trackingNo === null) ? "N/A" : $response->trackingNo;
				$packlist[$i]['sku'] = $data[$i]['sku'];
				
				$orderNumList[] = $response->referenceNo;
			}
			
			$allOrderNumList[] = $response->referenceNo;
			
			$i++;
		}
		
		$separatedOrderNumList = (count($orderNumList) > 0) ? implode(",", $orderNumList) : false;
		$separatedAllOrderNumList = (count($allOrderNumList) > 0) ? implode(",", $allOrderNumList) : false;
		
		UploadHelper::parseOrderCsv($pathToFile, "ubigen");
		
		//return response()->json($test_data);
        return view('ubi-upload.result', [
            'responses' => $decodedReponses,
            'packlist' => $packlist,
            'orderNumList' => $separatedOrderNumList,
            'allOrderNumList' => $separatedAllOrderNumList,
        ]);
	}
	
	public function downloadpdf (request $request){
		$orderNumbers = '"' . str_replace(",", '","', $request['orders']) . '"';
		$pdfContent = $this->ubi_print_label($orderNumbers);
		
		return response()->attachment($pdfContent);
	}
	
	public function forcastorders (request $request){
		$orderNumbers = '"' . str_replace(",", '","', $request['orders']) . '"';
		
		$message = $this->ubi_forecast_orders($orderNumbers);
		return response()->json([
			'status' => 'success',
			'message' => $message,
		]);
	}
		
	public function ubi_build_headers($method, $path, $acceptType='application/json'){
		$walltech_date=date(DATE_RSS);
		$auth = $method."\n".$walltech_date."\n".$path;
		$hash=base64_encode(hash_hmac('sha1', $auth, self::SECRET_KEY, true));
		
		//echo $walltech_date."<br>".$auth."<br>".$hash."<br>";
		return array(	'Content-Type: application/json',
		'Accept: '.$acceptType,
		'X-WallTech-Date: '.$walltech_date,
		'Authorization: WallTech '.self::ACCESS_TOKEN.':'.$hash);
	}

	public function ubi_send_request($method,$headers,$body){
		$ch = curl_init(self::WALLTECH_SERVER.$method);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);   
		curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
		return curl_exec($ch);
	}

	public function ubi_send_request_get($method,$headers,$body){
		$ch = curl_init(self::WALLTECH_SERVER.$method);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);   
		curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
		return curl_exec($ch);
	}
	
	public function ubi_create_order($json_data){
		$method = '/services/integration/shipper/orders';
		$headers = $this->ubi_build_headers('POST', self::WALLTECH_SERVER.$method);
		$body = $json_data;
		
		return $this->ubi_send_request($method,$headers,$body);
	}
	
	public function ubi_print_label($orderNumbers){
		$method='/services/integration/shipper/labels';
		$headers=$this->ubi_build_headers('POST', self::WALLTECH_SERVER.$method,'application/octet-stream');
		$body='[' . $orderNumbers . ']';
		
		return $this->ubi_send_request($method,$headers,$body);
	}
	
	public function ubi_forecast_orders($orderNumbers){
		$method='/services/integration/shipper/manifests';
		$headers=$this->ubi_build_headers('POST', self::WALLTECH_SERVER.$method);//$body='["SA#41612","SA#41622","SA#41624","SA#41635","SA#41636","SA#41638","SA#41641","SA#41643","SA#41644","SA#41653","SA#41666","SA#41673","SA#41748","SA#41828","SA#41897","SA#41950","SA#41952","SA#41985","SA#41988","SA#42013","SA#42024","SA#42031","SA#42037","SA#42084","SA#42118","SA#42126","SA#42141","SA#42185","SA#42192","SA#42193","SA#42217","SA#42220","SA#42225","SA#42232","SA#42235","SA#35858","SA#35484","SA#37732","SA#38470","SA#42041"]';
		$body='[' . $orderNumbers . ']';
		
		return $this->ubi_send_request($method,$headers,$body);
	}
	
	public function ubi_query_orders($order_track_num) {
		$method='/services/shipper/queryorders';
		$headers=$this->ubi_build_headers('POST', self::WALLTECH_SERVER.$method);
		$body='["' . $order_track_num . '"]';
		
		return $this->ubi_send_request($method,$headers,$body);
	}
	
	public function fix_ubi_cost(){
		$UploadHelper = new UploadHelper();
		
		/*
		$orders = InvoiceOrders::select(['id', 'order_id', 'track_num', 'sku', 'product_cost', 'status'])
								->where([
									['sku', '=' , 'None'],
									['ship_company', '=' , 'ubi'],
									['id', '>' , '39902'],
									//['id', '>' , '44900']
								])
								->get();
		*/
		InvoiceOrders::select(['id', 'order_id', 'track_num', 'sku', 'product_cost', 'status'])
								->where([
									['order_id', 'LIKE' , 'SA%'],
									['sku', '=' , ''],
									['status', '=' , 'unbilled'],
									['id', '>' , '45251']
									//['id', '>' , '39902']
									//['id', '>' , '44900']
								])
								->chunk(100, function ($orders) use ($UploadHelper) {
									foreach ($orders as $order){
										
										$replaceStrings = array("塑料产品", "*1;", "美", "工具盒", "工具集", "商品品名:");
										$search_findStrings = array("（", "）");
										$replace_findStrings = array("(", ")");
										
										$order_info = json_decode($this->ubi_query_orders($order->track_num), true);
										$skus = str_replace("商品品名:", "", $order_info['data'][0]['order']['nativeDescription']);
										
										$fSku = (stripos($order_info['data'][0]['order']['packingList'], "001") !== FALSE) ? $order_info['data'][0]['order']['packingList'] : $skus;
										$fSku = trim(str_replace($replaceStrings, "", str_replace($search_findStrings, $replace_findStrings, $fSku)));
										
										if (empty($fSku) && !empty($order_info['data'][0]['order']['orderItems'][0]['sku'])){
											$fSku = $order_info['data'][0]['order']['orderItems'][0]['sku'];
										}
										
										$fOrderInfo = $UploadHelper->getProductCost($fSku);
										//$fOrderInfo = $UploadHelper->getProductCost($fSku);
										
										echo "Order number: " . $order->order_id . "<br/ >";
										echo "Track number: " . $order->track_num . "<br/ >";
										echo "Packing List: " . $order_info['data'][0]['order']['packingList'] . "<br/ >";
										echo "Got SKU: " . $order_info['data'][0]['order']['sku'] . "<br/ >";
										echo "Got SKU 2: " . $order_info['data'][0]['order']['orderItems'][0]['sku'] . "<br/ >";
										echo "fSku: " . $fSku . "<br/ >";
										//echo "Order info: " . print_r($orderInfo, true) . "<br/ >";
										echo "F Order info: " . print_r($fOrderInfo, true) . "<br/ >";
										echo "<br />";
										echo "UBI Order info: " . print_r($order_info, true) . "<br/ >";
										echo "<br />";
										echo "<br />";
										
										
										if (!isset($fOrderInfo['error'])){
											$updateDetails = array('sku' => $fSku, 'product_cost' => $fOrderInfo['subtotal']);
											DB::table('invoice_orders')->where('id', $order->id)->update($updateDetails);
										} else {
											echo $order->id . ": " . $order->order_id . " not found. Error: " . $fOrderInfo['error'] . "<br /><br />";
										}
										
									}
								});
		/*
		foreach ($orders as $order){
			
			$replaceStrings = array("塑料产品", "*1;", "美", "工具盒");
			$order_info = json_decode($this->ubi_query_orders($order->track_num), true);
			$skus = str_replace("商品品名:", "", $order_info['data'][0]['order']['nativeDescription']);
			
			$fSku = trim(str_replace($replaceStrings, "", $skus));
			
			//$orderInfo = $UploadHelper->getProductCost($order->sku);
			$fOrderInfo = $UploadHelper->getProductCost($fSku);
			
			echo "Order number: " . $order->order_id . "<br/ >";
			//echo "Order info: " . print_r($orderInfo, true) . "<br/ >";
			echo "F Order info: " . print_r($fOrderInfo, true) . "<br/ >";
			echo "<br />";
			echo "<br />";
			
			
			if (!isset($fOrderInfo['error'])){
				$updateDetails = array('sku' => $fSku, 'product_cost' => $fOrderInfo['subtotal']);
				DB::table('invoice_orders')->where('id', $order->id)->update($updateDetails);
			} else {
				echo $order->id . ": " . $order->order_id . " not found. Error: " . $fOrderInfo['error'] . "<br /><br />";
			}
			
		}
		*/
	}
	
	public function fix_ubi_cost2(){
		$UploadHelper = new UploadHelper();
		
		/*
		$orders = InvoiceOrders::select(['id', 'order_id', 'track_num', 'sku', 'product_cost', 'status'])
								->where([
									['sku', '=' , 'None'],
									['ship_company', '=' , 'ubi'],
									['id', '>' , '39902'],
									//['id', '>' , '44900']
								])
								->get();
		*/
		InvoiceOrders::select(['id', 'order_id', 'track_num', 'sku', 'product_cost', 'status'])
								->where([
									['order_id', 'LIKE' , 'SA%'],
									['sku', '=' , 'None'],
									['ship_company', '=' , 'ubi'],
									['status', '=' , 'unbilled'],
									['id', '>' , '45251']
									//['id', '>' , '39902']
									//['id', '>' , '44900']
								])
								->chunk(100, function ($orders) use ($UploadHelper) {
									foreach ($orders as $order){
										
										$replaceStrings = array("塑料产品", "*1;", "美", "工具盒", "工具集", "商品品名:");
										$search_findStrings = array("（", "）");
										$replace_findStrings = array("(", ")");
										
										$order_info = json_decode($this->ubi_query_orders($order->track_num), true);
										$skus = str_replace("商品品名:", "", $order_info['data'][0]['order']['nativeDescription']);
										
										$fSku = (stripos($order_info['data'][0]['order']['packingList'], "001") !== FALSE) ? $order_info['data'][0]['order']['packingList'] : $skus;
										$fSku = trim(str_replace($replaceStrings, "", str_replace($search_findStrings, $replace_findStrings, $fSku)));
										
										if (empty($fSku) && !empty($order_info['data'][0]['order']['orderItems'][0]['sku'])){
											$fSku = $order_info['data'][0]['order']['orderItems'][0]['sku'];
										}
										
										$fOrderInfo = $UploadHelper->getProductCost($fSku);
										//$fOrderInfo = $UploadHelper->getProductCost($fSku);
										
										echo "Order number: " . $order->order_id . "<br/ >";
										echo "Track number: " . $order->track_num . "<br/ >";
										echo "Packing List: " . $order_info['data'][0]['order']['packingList'] . "<br/ >";
										echo "Got SKU: " . $order_info['data'][0]['order']['sku'] . "<br/ >";
										echo "Got SKU 2: " . $order_info['data'][0]['order']['orderItems'][0]['sku'] . "<br/ >";
										echo "fSku: " . $fSku . "<br/ >";
										//echo "Order info: " . print_r($orderInfo, true) . "<br/ >";
										echo "F Order info: " . print_r($fOrderInfo, true) . "<br/ >";
										echo "<br />";
										echo "<br />";
										
										
										if (!isset($fOrderInfo['error'])){
											$updateDetails = array('sku' => $fSku, 'product_cost' => $fOrderInfo['subtotal']);
											DB::table('invoice_orders')->where('id', $order->id)->update($updateDetails);
										} else {
											echo $order->id . ": " . $order->order_id . " not found. Error: " . $fOrderInfo['error'] . "<br /><br />";
										}
										
									}
								});
		/*
		foreach ($orders as $order){
			
			$replaceStrings = array("塑料产品", "*1;", "美", "工具盒");
			$order_info = json_decode($this->ubi_query_orders($order->track_num), true);
			$skus = str_replace("商品品名:", "", $order_info['data'][0]['order']['nativeDescription']);
			
			$fSku = trim(str_replace($replaceStrings, "", $skus));
			
			//$orderInfo = $UploadHelper->getProductCost($order->sku);
			$fOrderInfo = $UploadHelper->getProductCost($fSku);
			
			echo "Order number: " . $order->order_id . "<br/ >";
			//echo "Order info: " . print_r($orderInfo, true) . "<br/ >";
			echo "F Order info: " . print_r($fOrderInfo, true) . "<br/ >";
			echo "<br />";
			echo "<br />";
			
			
			if (!isset($fOrderInfo['error'])){
				$updateDetails = array('sku' => $fSku, 'product_cost' => $fOrderInfo['subtotal']);
				DB::table('invoice_orders')->where('id', $order->id)->update($updateDetails);
			} else {
				echo $order->id . ": " . $order->order_id . " not found. Error: " . $fOrderInfo['error'] . "<br /><br />";
			}
			
		}
		*/
	}
	
	public function fix_ubi_cost33(){
		$UploadHelper = new UploadHelper();
		$Count = 0;
		
		InvoiceOrders::select(['id', 'order_id', 'track_num', 'sku', 'product_cost', 'status'])
								->where([
									['order_id', 'LIKE' , 'SA%'],
									['status', '=' , 'unbilled'],
									['prefix', '=' , 'SA'],
									['id', '<' , 39901],
									//['sku', '=' , 'None'],
									//['ship_company', '=' , 'ubi'],
									//['id', '=' , '36604']
									//['id', '>' , '39902']
									//['id', '>' , '44900']
								])
								->chunk(100, function ($orders) use ($UploadHelper, $Count) {
									foreach ($orders as $order){
										
										$replaceStrings = array("塑料产品", "*1;", "美", "工具盒", "工具集", "工具箱");
										$search_findStrings = array("（", "）");
										$replace_findStrings = array("(", ")");
										
										$fSku = trim(str_replace($replaceStrings, "", str_replace($search_findStrings, $replace_findStrings, $order->sku)));
										
										/*
										if (empty($fSku) && !empty($order_info['data'][0]['order']['orderItems'][0]['sku'])){
											$fSku = $order_info['data'][0]['order']['orderItems'][0]['sku'];
										}
										
										echo "Order number: " . $order->order_id . "<br/ >";
										echo "Track number: " . $order->track_num . "<br/ >";
										echo "Packing List: " . $order_info['data'][0]['order']['packingList'] . "<br/ >";
										echo "Got SKU: " . $order_info['data'][0]['order']['sku'] . "<br/ >";
										echo "Got SKU 2: " . $order_info['data'][0]['order']['orderItems'][0]['sku'] . "<br/ >";
										echo "fSku: " . $fSku . "<br/ >";
										//echo "Order info: " . print_r($orderInfo, true) . "<br/ >";
										echo "F Order info: " . print_r($fOrderInfo, true) . "<br/ >";
										echo "<br />";
										echo "<br />";
										*/
										//echo print_r($fSku, true);
										//echo "<br />";
										//echo "<br />";
										
										$fOrderInfo = $UploadHelper->getProductCost($fSku);
										//$fOrderInfo = $UploadHelper->getProductCost($fSku);
										
										echo $order->order_id . ": " . print_r($fOrderInfo, true);
										echo "<br />";
										echo "<br />";
										
										
										if (!isset($fOrderInfo['error'])){
											$updateDetails = array('sku' => $fSku, 'product_cost' => $fOrderInfo['subtotal']);
											DB::table('invoice_orders')->where('id', $order->id)->update($updateDetails);
											$Count++;
										} else {
											echo $order->id . ": " . $order->order_id . " not found. Error: " . $fOrderInfo['error'] . "<br /><br />";
										}
										
									}
								});
		echo $Count . " Updated";
	}
	
	public function json_encode_unicode($data) {
		if (defined('JSON_UNESCAPED_UNICODE')) {
			return json_encode($data, JSON_UNESCAPED_UNICODE);
		}
		return preg_replace_callback('/(?<!\\\\)\\\\u([0-9a-f]{4})/i',
			function ($m) {
				$d = pack("H*", $m[1]);
				$r = mb_convert_encoding($d, "UTF8", "UTF-16BE");
				return $r!=="?" && $r!=="" ? $r : $m[0];
			}, json_encode($data)
		);
	}
	
    public function readCsvFile($filePath)
    {
        if (!file_exists($filePath)) {
            throw new Exception('File not found');
        }
        $this->sourceFilePath = $filePath;

        $data = [];

        if (($handle = fopen($filePath, 'r')) !== false) {
            while (($row = fgetcsv($handle, 0, ',')) !== false) {
                $data[] = $row;
            }
            fclose($handle);
        }

        if (count($data) < 2) {
            throw new Exception('Invalid CSV file');
        }

        return $data;
    }
	public function transferUbiOrders()
	{
		$ubiOrders = UbiOrders::all();
		//$ubiOrders = UbiOrders::take(20)->get();
        $parse_prefix = array("mz", "sa");
        $duplicateOrders = [];
        $orders = [];
        $orderId = "";
        
        foreach ($ubiOrders as $ubiOrder){
            $orderId = $ubiOrder->order_id;
            $saveOrder = false;
            $orderExist = InvoiceOrders::where('order_id', $orderId)->first();
            $trackNumExist = InvoiceOrders::where('track_num', $ubiOrder->tracking_number)->first();
            
            foreach ($parse_prefix as $search_prefix){
                if (stripos($orderId, trim($search_prefix)) !== FALSE){
                    $saveOrder = true;
                    $prefix = strtoupper($search_prefix);
                    break;
                }
            }
            
            $skus = (uploadedOrders::where('order_id', $orderId)->first()) ? uploadedOrders::where('order_id', $orderId)->first()->skus : "";
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
                    'track_num' => $ubiOrder->tracking_number,
                    'weight' => $ubiOrder->weight,
                    'product_cost' => $product_cost['subtotal'],
                    'shipping_cost' => $ubiOrder->cost,
                    'ship_company' => "ubi",
                    'status' => "unbilled" . $error_message,
                );
            } else {
                if ($orderExist && !$trackNumExist){
                    $orderId = $orderId . "(RMA)";
                }
                $orders[] = array(
                    'order_id' => $orderId,
                    'prefix' => $prefix,
                    'sku' => trim($skus),
                    'track_num' => $ubiOrder->tracking_number,
                    'weight' => $ubiOrder->weight,
                    'product_cost' => $product_cost['subtotal'],
                    'shipping_cost' => $ubiOrder->cost,
                    'ship_company' => "ubi",
                    'status' => "unbilled" . $error_message,
                );
            }
        }
        
        //print_r($orders);
        
        if (InvoiceOrders::insert($orders)){
            $message = "Successfully Inserted " . count($orders) . " orders. Duplicate Orders: " . count($duplicateOrders);
        } else {
            $message = "Error";
        }
        
        return view('invoice-orders.result', [
        'message' => $message,
        'orders' => $orders,
        'duplicateOrders' => $duplicateOrders,
        ]);
	}
    
	public function getProductCost($skus){
		// GET PRODUCT COST
		
		$product_summary = [];
		$cleaned_skus = rtrim(trim(stringAfter(":", $skus)), ",");
		$replace_parts = array(" X ", " + ");
		$replace_with = array("*", ",");
		$cleaned_skus = str_replace($replace_parts, $replace_with, $cleaned_skus);
		
		if (stripos($cleaned_skus, ";") !== false){
			$skus_pieces = explode(";", $cleaned_skus);
		} 
		else {
			$skus_pieces = explode(",", $cleaned_skus);
		}
		
		$total_cost = 0;
		if ((stripos($skus, ";") !== false) || (stripos($skus, ",") !== false) || (stripos($skus, ":") !== false) || ((stripos($skus, "(") !== false) || (stripos($skus, "X") !== false) || (stripos($skus, "*") !== false) && (stripos($skus, ",") === false))){
			foreach ($skus_pieces as $sku_parts){
				if (stripos($sku_parts, "(") !== false){
					$qty = intval(rtrim(stringAfter("(", trim($sku_parts)), ")"));
					$sku = strstr(trim($sku_parts), '(', true);
				}
				if (stripos($sku_parts, "*") !== false){
					$sku_pieces = explode("*", $sku_parts);
					$sku = trim($sku_pieces[0]);
					$qty = trim($sku_pieces[1]);
				}
				
				if (!empty($sku)){
					$vendor_sku_find = array("sa", "da", "GS", "pot", "waist", "lithium", "8244337", "Lid");
					//$sku_cost = (strposa($sku, $vendor_sku_find)) ? Products::where('vendor_sku', $sku)->first()->price : Products::where('our_sku', $sku)->first()->price;
					if (strposa($sku, $vendor_sku_find)){
						$sku_cost = (Products::where('vendor_sku', $sku)->first()) ? Products::where('vendor_sku', $sku)->first()->price : false;
					} else {
						$sku_cost = (Products::where('our_sku', $sku)->first()) ? Products::where('our_sku', $sku)->first()->price : false;
					}
					
					$total_cost = round($total_cost + ($sku_cost * $qty), 2);
					
					$product_summary[$sku] = $sku_cost;
					if (!$sku_cost){
						$product_summary['error'] = "NF: " . $sku;
					}
				}
			}
		}
		$product_summary['subtotal'] = number_format((float)$total_cost, 2, '.', '');
		// END PRODUCT COST
		return $product_summary;
	}
    
    public function testUpload(request $request)
    {
        /*
        $this->validate($request, [
        'file' => [
        'bail',
        'required',
        ],
        ]);
        */
        Excel::load('/home/admin/web/jeff.smartecom.io/public_html/sku/files/Smart_Ecom_Limited.xlsx')->store('xls', "/home/admin/web/jeff.smartecom.io/public_html/sku/files/Smart_Ecom_Limited-converted.xls");
       
    }
}
