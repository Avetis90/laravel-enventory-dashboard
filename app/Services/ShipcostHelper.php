<?php

namespace App\Services;

use App\Models\uploadedOrders;
use App\Models\Products;

use Illuminate\Support\Facades\DB;
use Exception;

class ShipcostHelper
{
    public static function getShipcost($shipping_cost, $weight, $track_num, $order_id)
    {
        $cost = $shipping_cost;
        $shipping_cost = $shipping_cost + 1;
        $packaging_cost = ($weight > 0.5) ? 3 : 2;
        
        if (!empty($weight)){
            if ($weight < 0.2 ){
                $us_service_fee = "10";
                $us_per_kg = "81.28";
                $us_margin_multiplier = 1.10;
                
                $ca_service_fee = "27";
                $ca_per_kg = "65";
                $ca_margin_multiplier = 1.20;
            } else if ($weight < 0.5) {
                $us_service_fee = "10";
                $us_per_kg = "76";
                $us_margin_multiplier = 1.10;
                
                $ca_service_fee = "29";
                $ca_per_kg = "60";
                $ca_margin_multiplier = 1.20;
            } else {
                $us_service_fee = "10";
                $us_per_kg = "76";
                $us_margin_multiplier = 1.10;
                
                $ca_service_fee = "35";
                $ca_per_kg = "55";
                $ca_margin_multiplier = 1.10;
            }
            
            $us_price = (( $weight * $us_per_kg ) + $us_service_fee) * $us_margin_multiplier;
            $ca_price = (( $weight * $ca_per_kg ) + $ca_service_fee) * $ca_margin_multiplier;
            
            $us_multiplier = round($us_price / $cost, 4);
            $ca_multiplier = round($ca_price / $cost, 4);
            
            $country = (uploadedOrders::where('order_id', $order_id)->first()) ? uploadedOrders::where('order_id', $order_id)->first()->country : "";
            $mt_cost = array(
                "AT" => (((($weight*78)+19) * 1.1) + $packaging_cost),
                "IT" => (((($weight*58)+44) * 1.1) + $packaging_cost),
                "DE" => (((($weight*52)+27) * 1.2) + $packaging_cost),
                "FR" => (((($weight*60)+32) * 1.05) + $packaging_cost),
                "IE" => (((($weight*45)+60) * 1.05) + $packaging_cost),
                "BE" => (((($weight*68)+29) * 1.1) + $packaging_cost),
                "GB" => (((($weight*40)+25) * 1.1) + $packaging_cost),
                "ES" => (((($weight*52)+27) * 1.1) + $packaging_cost),
                "HR" => (((($weight*110)+25) * 1.1) + $packaging_cost),
                "SE" => (((($weight*85)+40) * 1.1) + $packaging_cost),
                "DK" => (((($weight*85)+42) * 1.1) + $packaging_cost),
                "CZ" => (((($weight*65)+25) * 1.1) + $packaging_cost),
                "EE" => (((($weight*70)+35) * 1.1) + $packaging_cost),
                "PL" => (((($weight*63)+25) * 1.1) + $packaging_cost),
                "GR" => (((($weight*65)+45) * 1.1) + $packaging_cost),
                "NO" => (((($weight*90)+30) * 1.1) + $packaging_cost),
                "NL" => (((($weight*73)+25) * 1.1) + $packaging_cost),
                "PT" => (((($weight*80)+25) * 1.05) + $packaging_cost),
                "LU" => (((($weight*55)+37) * 1.1) + $packaging_cost),
                "FI" => (((($weight*75)+25) * 1.1) + $packaging_cost),
                "CY" => (((($weight*121)+15) * 1.1) + $packaging_cost),
                "HU" => (((($weight*71)+19) * 1.1) + $packaging_cost),
                "LV" => (((($weight*70)+20) * 1.1) + $packaging_cost),
                "LT" => (((($weight*70)+20) * 1.1) + $packaging_cost),
                "MT" => (((($weight*92)+25) * 1.1) + $packaging_cost),
                "RO" => (((($weight*70)+25) * 1.1) + $packaging_cost),
                "SK" => (((($weight*62)+25) * 1.1) + $packaging_cost),
                "SI" => (((($weight*65)+26) * 1.1) + $packaging_cost),
                "BG" => (((($weight*115)+15) * 1.1) + $packaging_cost),
            );
            
            $mt_shipcost = (array_key_exists($country, $mt_cost)) ? $mt_cost[$country] : ($shipping_cost * 1.1) + $packaging_cost;
            
            
            // $2 is package box cost
            $needles = array(
                array(2, 'br', '1.1717' * $shipping_cost), //AU
                array(2, 'bj', '1.1717' * $shipping_cost), //AU
                array(4, '9109', '1.329' * $shipping_cost), //NZ
                array(2, 'rr', $us_price), //US
                array(4, '4006', $ca_price), //CA
                array(2, "JD", ((($weight*40)+25) * 1.1) + $packaging_cost), //UK-YODEL
                array(2, "JL", ((($weight*40)+25) * 1.1) + $packaging_cost), //UK-TP2
                array(2, "CE", ((($weight*114)+27) * 1.1) + $packaging_cost), //OW-IRELAND
                array(4, "PQ41", ((($weight*52)+27) * 1.1) + $packaging_cost), //OW-SPAIN
                array(7, "0028037", ((($weight*52)+27) * 1.1) + $packaging_cost), //OW-SPAIN
                array(3, "241", ((($weight*52)+27) * 1.1) + $packaging_cost), //DEEXP 
                array(3, "242", ((($weight*52)+27) * 1.1) + $packaging_cost), //DEEXP
                array(3, "622", ((($weight*58)+44) * 1.1) + $packaging_cost), //OW-ITALY
                array(4, "9L22", ((($weight*60)+32) * 1.1) + $packaging_cost), //OW-FRANCE
                
                array(3, "TYP", $mt_shipcost), //MT EU
                array(2, "LB", $mt_shipcost), //MT BPost
                array(2, "RL", $mt_shipcost), //Swiss Post
                
                array(2, "LW", $shipping_cost + $packaging_cost), //EUB
                array(2, "LX", $shipping_cost + $packaging_cost), //EUB
                array(2, "LM", $shipping_cost + $packaging_cost), //EUB
                array(2, "LZ", $shipping_cost + $packaging_cost), //EUB
                //array(4, "6061", '1.1' * $shipping_cost), //PFC-Malay
                //array(2, "RS", '1.1' * $shipping_cost), //PFC-NLPOST
                //array(2, "RX", '1.1' * $shipping_cost), //WYT-DHLECommerce
                //array(2, "RX", '1.1' * $shipping_cost), //WYT-DHLECommerce
            );
            
            if(!is_array($needles)) $needles = array($needles);
            foreach($needles as $query) {
                if (strtolower(substr($track_num, 0, $query[0])) == strtolower($query[1])) return $query[2]; //stop on first true result
            }
        }
        return ($shipping_cost * 1.1) + $packaging_cost;
    }
	
	public static function getCustomerSku($skus){
		$product_summary = [];
		$skus2 = rtrim(trim($skus), ":");
		$cleaned_skus = rtrim(trim(stringAfter(":", $skus2)), ",");
		$replace_parts = array(" X ", " + ", "塑料产品", "*1;", "美", "工具盒", "（", "）");
		$replace_with = array("*", ",", "", "", "", "", "(", ")");
		$cleaned_skus = str_replace($replace_parts, $replace_with, $cleaned_skus);
		
		if (stripos($cleaned_skus, ";") !== false){
			$skus_pieces = explode(";", $cleaned_skus);
		} 
		else {
			$skus_pieces = explode(",", $cleaned_skus);
		}
		
		$total_cost = 0;
		$allReplacedSkus = array();
		$sku = "";
		$qty = "";
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
				
				if (!empty($sku) && stripos($sku, "001") !== FALSE) {		
					$allReplacedSkus[] = DB::table('products')->where('our_sku', $sku)->value('vendor_sku') . " (" . $qty . ")";
				} else {
					$allReplacedSkus[] = $sku . " (" . $qty . ")";
				}
			}
		}
			
		return implode(", ", $allReplacedSkus);
	}
    
    
}