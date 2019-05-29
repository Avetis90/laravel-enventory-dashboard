<?php

namespace App\Services\Parsers;
use App\Models\Products;
use Countries;

class ParserAllinone extends AbstractShopifyParser
{
    public function getColumns($options = [])
    {
        return [
		/*
            '客户单号' => [
                'attribute' => 'Name',
                'prefix' => $options['prefix']
            ],
		*/
            '客户单号' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexName = array_search('Name', $sourceHeaders);
                    $indexCountry = array_search('Shipping Country', $sourceHeaders);
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
					
                    foreach ($items as $item) {
                        $country = getCountryAB($item[$indexCountry]);
                        
                        if (stripos($options['title'], "Woo") !== false)
                        {
                            if (stripos($options[$country]['service_options'], "德国小包") && stripos($item[$indexSKU], "ML251495") === FALSE && stripos($item[$indexSKU], "ML251496") === FALSE)
                            {
                                $order_number = str_replace("-", "0", $item[$indexName]);
                            } else {
                                $order_number =  $item[$indexName];
                            }
                        } else {
                            $order_number = $options['prefix'] . "-" . $item[$indexName];
                        }
                    }
					
					return $order_number;
                },
            ],
            '转单号' => [
                'default' => '',
            ],
            '运输方式' => [
                'callback' => function ($items, $sourceHeaders) use ($options){
                    $indexCountry = array_search('Shipping Country', $sourceHeaders);
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
                    $indexZip = array_search('Shipping Zip', $sourceHeaders);
                    $ShippingOptions = "";
                    $Shipping_Name = "";
                    
                    foreach ($items as $item) {
						if (!empty($item[$indexCountry])){
							$country = getCountryAB($item[$indexCountry]);
							$ShippingOptions = ($country == "MT") ? "淼信比利时龙挂号" : $options[$country]['service_options'];
							
							if ($country == "NZ"){
								if (stripos($item[$indexSKU], "ML25175") !== FALSE){
									$ShippingOptions = "宋L-广州E邮宝";
								}
							}
							
							//if ($country == "GB" && (stripos($item[$indexSKU], "ML25175") !== FALSE)){
							//	$ShippingOptions = "淼信欧洲通专线";
							//}
							
							if (((stripos($item[$indexSKU], "ML25400") !== FALSE) || (stripos($item[$indexSKU], "ML25182") !== FALSE) || (stripos($item[$indexSKU], "ML25981") !== FALSE)) && (stripos($item[$indexSKU], ",") === FALSE)){
								$ShippingOptions = "宋L-广州E邮宝";
							}
							
							if (stripos($item[$indexSKU], "ML251495") !== FALSE || stripos($item[$indexSKU], "ML251496") !== FALSE){
								if ($country == "NO" || $country == "CH"){
									$ShippingOptions = "宋L-广州E邮宝";
								}
							}
							
							$eu_countries = array('BE', 'NO', 'CH', 'BG', 'CZ', 'DK', 'DE', 'EE', 'IE', 'EL', 'ES', 'FR', 'HR', 'IT', 'CY', 'LV', 'LT', 'LU', 'HU', 'MT', 'NL', 'AT', 'PL', 'PT', 'RO', 'SI', 'SK', 'FI', 'SE', 'UK');
							
							if (stripos($item[$indexSKU], "ML25800") !== FALSE && stripos($country, "DHL") === FALSE){
								
								if (in_array($country, $eu_countries)){
									if ($country == "DE"){
										$ShippingOptions = "淼信欧洲通专线";
									} else {
										$ShippingOptions = "淼信比利时龙挂号";
									}
								}
								
								if ($country == "JP"){
									$ShippingOptions = "云途-日本专线";
								}
							}
							
							//if (stripos($item[$indexSKU], "ML25195") !== FALSE && $country == "US"){
							//	$ShippingOptions = "三通-美国专线";
							//}
							
							if ($country == "ES"){
								$zipcode = substr($item[$indexZip], 0, 2);
								$spanish_remote = array("07", "51", "52", "35", "38");
								if (in_array($zipcode, $spanish_remote))
								{
									$ShippingOptions = "淼信比利时龙挂号";
								}
							}
							
							
							if ($country == "PT"){
								$zipcode_first = substr($item[$indexZip], 0, 1);
								if ($zipcode_first == 9)
								{
									$ShippingOptions = "淼信比利时龙挂号";
								}
							}

							if ($country == "IT"){
								$zipcode_first = substr($item[$indexZip], 0, 1);
								$zipcode = substr($item[$indexZip], 0, 2);
								$it_remote = array("09", "08", "07", "87", "88", "89");
								if (in_array($zipcode, $it_remote) || $zipcode_first == 9 || $item[$indexZip] == "22060" || $item[$indexZip] == "23030")
								{
									$ShippingOptions = "淼信比利时龙挂号";
								}
							}
							
							$Shipping_Name = $ShippingOptions;
						}
                    }
                    
                    return $Shipping_Name;
                },
            ],
            '目的国家' => [
                'callback' => function ($items, $sourceHeaders){
                    $indexCountry = array_search('Shipping Country', $sourceHeaders);
                    $country = "";
                    foreach ($items as $item) {
						if (!empty(trim($item[$indexCountry]))){
							if (stripos($item[$indexCountry], "-")!==FALSE){
								$countryArray = explode("-", $item[$indexCountry]);
								$country = $countryArray[1];
							} else {
								$country = $item[$indexCountry];
							}
							
							$country_ab = getCountryAB($country);
						}
                    }
                    return $country_ab;
                },
            ],
            '收件人姓名' => [
                'attribute' => 'Shipping Name',
            ],
            '州,省' => [
                'callback' => function ($items, $sourceHeaders) {
					$indexProvince = array_search('Shipping Province', $sourceHeaders);
					$indexCountry = array_search('Shipping Country', $sourceHeaders);
					
					$province = "";
					
                    foreach ($items as $item) {
						if ($item[$indexProvince] != ""){
							if (stripos($item[$indexCountry], "COD") !== false){
								$province = "";
							} else {
								$province = $item[$indexProvince];
							}
						}
					}
					
					return $province;
                },
            ],
            '城市' => [
                'callback' => function ($items, $sourceHeaders) {
					$indexCity = array_search('Shipping City', $sourceHeaders);
					$indexCountry = array_search('Shipping Country', $sourceHeaders);
					
					$city = "";
					
                    foreach ($items as $item) {
						if ($item[$indexCity] != ""){
							if (stripos($item[$indexCountry], "COD") !== false){
								$city = "";
							} else {
								$city = $item[$indexCity];
							}
						}
                    }
					
					return $city;
                },
            ],
            '联系地址' => [
                'callback' => function ($items, $sourceHeaders) {
                    $indexStreet = array_search('Shipping Street', $sourceHeaders);
                    $indexCompany = array_search('Shipping Company', $sourceHeaders);
					$indexCountry = array_search('Shipping Country', $sourceHeaders);
					$indexProvince = array_search('Shipping Province', $sourceHeaders);
					$indexCity = array_search('Shipping City', $sourceHeaders);
					
                    foreach ($items as $item) {
						if (!empty($item[$indexStreet])){
							if ($item[$indexCompany] != ""){
								$address = $item[$indexCompany] . ", " . $item[$indexStreet];
							} else {
								$address = $item[$indexStreet];
							}
						}
						
						if (stripos($item[$indexCountry], "COD") !== false){
							$city = (stripos($item[$indexStreet], "區") === false) ? $item[$indexCity] : "";
							$province = ((stripos($city . $item[$indexStreet], "縣") === false) && (stripos($city . $item[$indexStreet], "市") === false) && ((stripos($item[$indexProvince], "區") !== false) && (stripos($item[$indexCity], "區") === false) && (stripos($item[$indexStreet], "區") === false))) ? $item[$indexProvince] : "";
							
							
							if ($item[$indexCompany] != ""){
								$address = $item[$indexCompany] . ", " . $province . $city . $item[$indexStreet];
							} else {
								$address = $province . $city . $item[$indexStreet];
							}
						}
                    }
					
					return $address;
                },
            ],
            /*
            '联系地址' => [
                'attribute' => 'Shipping Street',
                'callback' => function ($items, $sourceHeaders) {
                    $indexAddress1 = array_search('Shipping Address1', $sourceHeaders);
                    $indexAddress2 = array_search('Shipping Address2', $sourceHeaders);
                    $fullAddress = '';
                    foreach ($items as $item) {
                        if (
                        $indexAddress1 !== false && isset($item[$indexAddress1]) &&
                        $indexAddress2 !== false && isset($item[$indexAddress2])
                        ) {
                            if (!empty($item[$indexAddress2])){
                                $fullAddress .= $item[$indexAddress1] . ', ' . $item[$indexAddress2];
                            } else {
                                $fullAddress .= $item[$indexAddress1];
                            }
                        }
                    }
                    return $fullAddress;
                },
            ],
            */
            '收件人电话' => [
                'callback' => function ($items, $sourceHeaders){
                    $indexPhone = array_search('Shipping Phone', $sourceHeaders);
                    $phonenumber = "";
                    foreach ($items as $item) {
						if (!empty($item[$indexPhone])){
							$phonenumber = str_replace("+", "00", str_replace("'", "", $item[$indexPhone]));
						}
                    }
					if (empty($phonenumber)){
						$phonenumber = "000";
					}
					
                    return $phonenumber;
                },
            ],
            '收件人邮箱' => [
                'default' => '',
            ],
            '收件人邮编' => [
                'callback' => function ($items, $sourceHeaders){
                    $indexZip = array_search('Shipping Zip', $sourceHeaders);
                    $zip = "";
                    foreach ($items as $item) {
						if (!empty($item[$indexZip])){
							$zip = str_replace("'", "", $item[$indexZip]);
						}
                    }
                    return $zip;
                },
            ],
            '重量' => [
                'callback' => function ($items, $sourceHeaders) {
                    $value = 0;
                    $indexCountry = array_search('Shipping Country', $sourceHeaders);
                    foreach ($items as $item) {
						$weight = array(1, 1.15, 1.2, 1.16, 1.25);
						$rand = rand(0,4);
						$value = $weight[$rand];
                    }
                    return $value;
                },
            ],
            '海关报关品名1' => [
                'default' => $options['description'],
            ],
            '海关编码1' => [
                'callback' => function ($items, $sourceHeaders) use ($options) 
				{
					$value = "";
                    $indexCountry = array_search('Shipping Country', $sourceHeaders);
					
					foreach ($items as $item) {
						$country = getCountryAB($item[$indexCountry]);
						$service_options = $options[$country]['service_options'];

						if (stripos($service_options, "UBI") !== FALSE){
							$value = "420222";
						}
					}
					
                    return $value;
                },
            ],
            '中文品名1' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexName = array_search('Name', $sourceHeaders);
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
                    $indexQuantity = array_search('Lineitem quantity', $sourceHeaders);
                    $indexCountry = array_search('Shipping Country', $sourceHeaders);
                    $name = '';
                    $ourSku = '';
					$products = array();
					
					if (stripos($options['title'], "Woo") !== false)
					{
						foreach ($items as $item) {
						
							$country = getCountryAB($item[$indexCountry]);
							$service_options = $options[$country]['service_options'];
							$add_sku = "";
							
							if ((stripos($service_options, "德国小包") !== FALSE) || (stripos($service_options, "E特快") !== FALSE)){
								$add_sku = ": " . $item[$indexSKU];
							}
						
							
							if (stripos($item[$indexSKU], "ML25800") !== FALSE){
								if ($country == "JP"){
									return $item[$indexSKU];
								}
							}
							
							return $options['description_cn'] . $add_sku;
						}
						
					} else {
						foreach ($items as $item) {
							if (
							$indexName !== false && isset($item[$indexName]) &&
							$indexSKU !== false && isset($item[$indexSKU]) &&
							$indexQuantity !== false && isset($item[$indexQuantity])
							
							) {
								if (empty($name)) {
									$name = $item[$indexName];
								}
								
								if (isset($products[$item[$indexSKU]])){
									$products[$item[$indexSKU]] += $item[$indexQuantity];
								} else {
									$products[$item[$indexSKU]] = $item[$indexQuantity];
								}
							}
						}
						
						foreach ($products as $sku => $qty){
							$Product = Products::where('vendor_sku', trim($sku))->first();
							$ourSku = (!empty($Product->our_sku)) ? $Product->our_sku : $sku;
							
							$values[] = $ourSku . '(' . $qty . ') ';
						}
						
						$country = getCountryAB($items[0][$indexCountry]);
						$service_options = $options[$country]['service_options'];
						$add_sku = "";
						
						if (stripos($service_options, "德国小包") !== FALSE || stripos($service_options, "E特快") !== FALSE){
							$add_sku = ": " . implode(', ', $values);
						}
						
						//return $options['prefix'] . $name . ": " . implode(', ', $values);
						//return implode(', ', $values);
						return $options['description_cn'] . $add_sku;
					}
                },
            ],
            '配货信息1' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexName = array_search('Name', $sourceHeaders);
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
                    $indexQuantity = array_search('Lineitem quantity', $sourceHeaders);
                    $indexCountry = array_search('Shipping Country', $sourceHeaders);
                    $name = '';
                    $ourSku = '';
					$export_sku = "";
					$products = array();
					
					if (stripos($options['title'], "Woo") !== false)
					{
						
						foreach ($items as $item) {
						
							$country = getCountryAB($item[$indexCountry]);
							$service_options = $options[$country]['service_options'];
							$add_chinese_text = "";
							if ($export_sku != "1BACKPACK"){
								$export_sku = $item[$indexSKU];
							}
							
							if (stripos($service_options, "德国小包") !== FALSE){
								$add_chinese_text = $options['description_cn'] . ": ";
							}
							
							if (stripos($item[$indexSKU], "ML25800") !== FALSE){
								if ($country == "JP"){
									$export_sku = "1BACKPACK";
								}
							}
							
							return $add_chinese_text . $export_sku;
						}
						
					} else {
						foreach ($items as $item) {
							if (
							$indexName !== false && isset($item[$indexName]) &&
							$indexSKU !== false && isset($item[$indexSKU]) &&
							$indexQuantity !== false && isset($item[$indexQuantity])
							
							) {
								if (empty($name)) {
									$name = $item[$indexName];
								}
								
								if (isset($products[$item[$indexSKU]])){
									$products[$item[$indexSKU]] += $item[$indexQuantity];
								} else {
									$products[$item[$indexSKU]] = $item[$indexQuantity];
								}
							}
						}
						
						foreach ($products as $sku => $qty){
							$Product = Products::where('vendor_sku', trim($sku))->first();
							$ourSku = (!empty($Product->our_sku)) ? $Product->our_sku : $sku;
							
							$values[] = $ourSku . '(' . $qty . ') ';
						}
						
						$country = getCountryAB($items[0][$indexCountry]);
						$service_options = $options[$country]['service_options'];
						$add_chinese_text = "";
						
						if (stripos($service_options, "德国小包") !== FALSE){
							$add_chinese_text = $options['description_cn'] . ": ";
						}
						
						//return $options['prefix'] . $name . ": " . implode(', ', $values);
						//return implode(', ', $values);
						return $add_chinese_text . implode(', ', $values);
					}
                },
            ],
            '申报价值1' => [
                'callback' => function ($items, $sourceHeaders) {
                    $value = 0;
                    $indexCountry = array_search('Shipping Country', $sourceHeaders);
                    $indexOrderTotal = array_search('Order Total', $sourceHeaders);
                    foreach ($items as $item) {
                        if ($item[$indexCountry] == "DK"){
                            $value = 10.79;
                        } elseif ($item[$indexCountry] == "GB"){
							$value = 17;
                        } elseif ($item[$indexCountry] == "CA"){
							$value = 14;
                        } elseif (stripos($item[$indexCountry], "COD") !== false){
                            $value = "80";
                        } elseif ($item[$indexCountry] == "CH"){
							$ch_custom = array(69.99, 89.99, 64.99, 84.99, 59.99, 89.99);
							$rand = rand(0,5);
                            $value = $ch_custom[$rand];
						} else {
                            $value = 19.99;
                        }
						
                    }
					
					
                    return $value;
                },
            ],
            '申报品数量1' => [
                'default' => '1',
            ],
            '关税类型' => [
                'callback' => function ($items, $sourceHeaders) use ($options) 
				{
					$value = "";
                    $indexCountry = array_search('Shipping Country', $sourceHeaders);
                    foreach ($items as $item) {
                        if ($item[$indexCountry] == "AU" && stripos($options['title'], "Woo") !== false){
                            $value = "DDU";
                        }
                    }
                    return $value;
                },
            ],
            '代收货款' => [
                'callback' => function ($items, $sourceHeaders) use ($options) 
				{
					$value = "";
                    $indexCountry = array_search('Shipping Country', $sourceHeaders);
                    $indexOrderTotal = array_search('Order Total', $sourceHeaders);
                    foreach ($items as $item) {
                        if (stripos($item[$indexCountry], "COD") !== false){
                            $value = round($item[$indexOrderTotal], 0);
                        }
                    }
                    return $value;
                },
            ],
            '代收币种' => [
                'callback' => function ($items, $sourceHeaders) use ($options) 
				{
					$value = "";
                    $indexCountry = array_search('Shipping Country', $sourceHeaders);
                    $indexOrderCurrency = array_search('Order Currency', $sourceHeaders);
                    foreach ($items as $item) {
                        if (stripos($item[$indexCountry], "COD") !== false){
                            $value = $item[$indexOrderCurrency];
                        }
                    }
                    return $value;
                },
            ],
        ];
    }

    public function parse($pathToFile, $options = [], $set = null)
    {
        $this->buildCsv($pathToFile, $this->getColumns($options));
    }
}