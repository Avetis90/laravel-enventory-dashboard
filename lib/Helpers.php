<?php
use Webpatser\Countries\Countries;

function stringAfter ($thestring, $inthat)
{
    if (!is_bool(stripos($inthat, $thestring))){
        return substr($inthat, stripos($inthat,$thestring)+strlen($thestring));
    } else {
        return $inthat;
    }
}

function remove_utf8_bom($line)
{
	$newline = array();
	foreach ($line as $item){
		$bom = pack('H*','EFBBBF');
		$newline[] = preg_replace("/^$bom/", '', $item);
	}
	return $newline;
}

function strposa($haystack, $needle, $offset=0) {
    if(!is_array($needle)) $needle = array($needle);
    foreach($needle as $query) {
        if(stripos($haystack, $query, $offset) !== false) return true; // stop on first true result
    }
    return false;
}

function getCountryAB($country){
    $country_ab = "";
	$search_replace_countries = array("South Korea" => "Korea, Republic of", "Brunei" => "Brunei Darussalam", "Taiwan" => "Taiwan, Province of China", "Russia" => "Russian Federation");
		
    if (strlen($country) > 2 && stripos($country, "-")===FALSE){
		if (array_key_exists($country, $search_replace_countries)){
			$country = $search_replace_countries[$country];
		}
		
		try {
			$country_info = Countries::where('name', $country)->first();
			$country_ab = $country_info->iso_3166_2;
		} catch(Exception $e) {
			throw new Exception($country . " is causing error. Might not exist in database.");
		}
		
    } else {
        $country_ab = $country;
    }
    
    return $country_ab;
}

function getSku4px($skus, $num){
	$prepSkus = substr($skus, strpos($skus, "}") + 1);
	$allSkus = explode(", ", $prepSkus);
	if (isset($allSkus[$num])){
		$oneSku = substr($allSkus[$num], 0, strpos($allSkus[$num], "("));
		return $oneSku;
	} else {
		return "";
	}
}

function getQty4px($skus, $num){
	$prepSkus = substr($skus, strpos($skus, "}") + 1);
	$allSkus = explode(", ", $prepSkus);
	if (isset($allSkus[$num])){
		$oneQty = substr($allSkus[$num], strpos($allSkus[$num], "(") + 1);
		return rtrim(trim($oneQty), ")");
	} else {
		return "";
	}
}

function checkCTSitems($skus, $ctsSkus){
	$prepSkus = substr($skus, strpos($skus, "}") + 1);
	$allSkus = explode(", ", $prepSkus);
	
	foreach ($allSkus as $skuprep){
		$sku = substr($skuprep, 0, strpos($skuprep, "("));
		//file_put_contents('/home/smartecom/web/jeffadmin2.smartecom.io/public_html/app/Services/Parsers/debug_csv.txt', $sku . "\n\n\n", FILE_APPEND);
		if (!in_array($sku, $ctsSkus)){
			return FALSE;
		}
	}
	
	return TRUE;
}