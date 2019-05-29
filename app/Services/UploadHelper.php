<?php

namespace App\Services;

use App\Models\InvoiceOrders;
use App\Models\uploadedOrders;
use App\Models\Products;

use Exception;

class UploadHelper
{
	public static function parseOrderCsv($csvFile, $format){
		$orders = [];
        $orders['ok'] = [];
        $orders['dup'] = [];
        
		$duplicateOrders = array();
		
		//order_id, skus, shipping_method, country, name, state, city, address, zip, phone, email, track_num
		$formatFileOrder = array(
		'ow' => array(6, 30, 7, 28, 29),
		'pfc' => array(0, 17, 23, 16, 25),
		'ubi' => array(1, 12, 13, 9, 2, 7, 6, 3, 8, 10, 11, 0),
		'ubigen' => array(0, 24, 25, 11, 1, 9, 8, 5, 10, 4, 3, 0),
		'wyt' => array(1, 12, 11, 4, 3, 6, 7, 5, 8, 9, 1, 2)
		);
		//$format = "ubi";
		
		$orderId_order = $formatFileOrder[$format][0];
		$sku_order = $formatFileOrder[$format][1];
		$track_num_order = $formatFileOrder[$format][2];
		$weight_order = $formatFileOrder[$format][3];
		$shipping_order = $formatFileOrder[$format][4];
		$repalceStrings = array("塑料产品", "*1;", "美", "工具盒");
		
		if (($handle = fopen($csvFile, "r")) !== FALSE) {
			$header = null;

			while ($row = fgetcsv($handle)) {
				if ($header === null) {
					$header = remove_utf8_bom($row);
					continue;
				}
				
				$orderId = strtoupper($row[$orderId_order]);
				
				if (stripos($orderId, "#") === FALSE && (stripos($orderId, "-") === FALSE || stripos($orderId, "RMA") !== FALSE)){
					$orderId = substr_replace($orderId, "#", 2, 0);
				}
				
				$orderExist = uploadedOrders::where('order_id', $orderId)->first();
                $trackNumExist = uploadedOrders::where('track_num', $row[$formatFileOrder[$format][11]])->first();
                
				if ($orderExist){
					$order_key = "dup";
					/*
					DB::table('uploaded_orders')
					->where('order_id', $orderId)
					->update(['track_num' => $row[$formatFileOrder[$format][11]]]);
					*/
				} else {
					if ($orderExist && !$trackNumExist){
						$orderId = $orderId . "(RMA)";
					}
					
					$order_key = "ok";
				}
				
				$orders[$order_key][] =  array(
					'order_id' => $orderId,
					'skus' => trim(str_replace($repalceStrings, "", $row[$formatFileOrder[$format][1]])),
					'shipping_method' => $row[$formatFileOrder[$format][2]],
					'country' => $row[$formatFileOrder[$format][3]],
					'name' => $row[$formatFileOrder[$format][4]],
					'state' => $row[$formatFileOrder[$format][5]],
					'city' => $row[$formatFileOrder[$format][6]],
					'address' => $row[$formatFileOrder[$format][7]],
					'zip' => $row[$formatFileOrder[$format][8]],
					'phone' => $row[$formatFileOrder[$format][9]],
					'email' => "",
					'track_num' => $row[$formatFileOrder[$format][11]],
				);
			}
            fclose($handle);
		}
        
        $info['message'] = "No order inserted. Total duplicate " . count($orders['dup']) . " orders";
        if (count($orders['ok']) > 0){
            if (uploadedOrders::insert($orders['ok'])){
                $info['message'] = "Successfully Inserted " . count($orders['ok']) . " orders. Duplicate orders: " . count($orders['dup']);
            } else {
                $info['message'] = "Error. Total okay orders: " . count($orders['ok']) . ". Duplicate orders: " . count($orders['dup']);
            }
        }
		
		$info['orders'] = $orders['ok'];
		$info['dupOrders'] = $orders['dup'];
		
		return $info;
	}
	
	public function parseInvoiceOrders($file, $format, $request = null)
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
					
					$repalceStrings = array("塑料产品", "*1;", "美", "工具盒");
                    $skus = trim(str_replace($repalceStrings, "", $skus));
                    
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
	
	public function getProductCost($skus){
		// GET PRODUCT COST
		
		$product_summary = [];
		$skus2 = rtrim(trim($skus), ":");
		$cleaned_skus = rtrim(trim(stringAfter(":", $skus2)), ",");
		$replace_parts = array(" X ", " + ", "塑料产品", "*1;", "美", "工具盒", "（", "）", ":");
		$replace_with = array("*", ",", "", "", "", "", "(", ")", "");
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
}