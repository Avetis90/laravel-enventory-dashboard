<?php

namespace App\Services\Parsers;
use App\Models\Products;
use Countries;

class ParserSong extends AbstractShopifyParser
{
    public function getColumns($options = [])
    {
        return [
            '订单标识' => [
                'attribute' => 'Name',
                'prefix' => $options['prefix']
            ],
            '商品交易号' => [
                'default' => '',
            ],
            '品名中文' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexName = array_search('Name', $sourceHeaders);
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
                    $indexQuantity = array_search('Lineitem quantity', $sourceHeaders);
                    $name = '';
                    $ourSku = '';
					$products = array();
					
					if (stripos($options['title'], "Woo") !== false)
					{
						
						foreach ($items as $item) {
							return $options['description_cn'] . "-" . $item[$indexName] .": " . $item[$indexSKU];
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
						
						//return $options['prefix'] . $name . ": " . implode(', ', $values);
						//return implode(', ', $values);
						return $options['description_cn'] . ": " . implode(', ', $values);
					}
                },
            ],
            '品名英文' => [
                'default' => $options['description'],
            ],
            '商品SKU' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexName = array_search('Name', $sourceHeaders);
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
                    $indexQuantity = array_search('Lineitem quantity', $sourceHeaders);
                    $name = '';
                    $ourSku = '';
					$products = array();
                    
					if (stripos($options['title'], "Woo") !== false)
					{
						
						foreach ($items as $item) {
							return $item[$indexSKU];
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
						
						//return $options['prefix'] . $name . ": " . implode(', ', $values);
						//return implode(', ', $values);
						return implode(', ', $values);
					}
                    /*
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
					
					//return $options['prefix'] . $name . ": " . implode(', ', $values);
					//return implode(', ', $values);
					return implode(', ', $values);
                    */
                },
            ],
            '数量' => [
                'default' => "1",
            ],
            '重量' => [
                'default' => '1',
            ],
            '报关价格' => [
                'callback' => function ($items, $sourceHeaders) {
                    $value = 0;
                    $indexCountry = array_search('Shipping Country', $sourceHeaders);
                    foreach ($items as $item) {
                        if ($item[$indexCountry] == "DK"){
                            $value = 10.79;
                        } else {
                            $value = 13.99;
                        }
                    }
                    return $value;
                },
            ],
            '原寄地' => [
                'default' => 'CN',
            ],
            '收件人姓名（英文）' => [
                'attribute' => 'Shipping Name',
            ],
            '收件人地址1（英文）' => [
                'attribute' => 'Shipping Address1',
            ],
            '收件人地址2（英文）' => [
                'attribute' => 'Shipping Address2',
            ],
            '收件人地址3（英文）' => [
                'attribute' => 'Shipping Address3',
            ],
            '收件人城市' => [
                'attribute' => 'Shipping City',
            ],
            '收件人州' => [
                'callback' => function ($items, $sourceHeaders){
                    $indexState = array_search('Shipping Province', $sourceHeaders);
                    $state = "";
					
					$search_array = array("ã", "ḥ", "J̼̣");
					$replace_array = array("a", "h", "J");
					
                    foreach ($items as $item) {
						if (!empty($item[$indexState])){
							$state = str_replace($search_array, $replace_array, $item[$indexState]);
						}
                    }
                    return $state;
                },
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
            '收件人国家' => [
                'callback' => function ($items, $sourceHeaders){
                    $indexCountry = array_search('Shipping Country', $sourceHeaders);
                    $country = "";
                    foreach ($items as $item) {
						if (!empty(trim($item[$indexCountry]))){
							$country = $item[$indexCountry];
							
                            if (strlen($country) > 2 && stripos($country, "DHL")===FALSE){
                                if ($country == "South Korea"){
                                    $country_ab = "KR";
                                } else {
                                    try {
                                        $country_info = Countries::where('name', $country)->first();
                                        $country_ab = $country_info->iso_3166_2;
                                        } catch(Exception $e) {
                                        throw new Exception($country . " is causing error. Might not exist in database. FAILED");
                                    }
                                }
                                break;
                            } else {
                                $country_ab = $country;
                                break;
                            }
						}
                    }
                    return $country_ab;
                },
            ],
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
            '收件人电子邮箱' => [
                'default' => '',
            ],
            '备注' => [
                'default' => '',
            ],
            '自定义信息' => [
                'default' => '',
            ],
            '自定义信息1' => [
                'default' => '',
            ],
            '自定义信息2' => [
                'callback' => function ($items, $sourceHeaders) use ($options){
                    $indexShipping = array_search('Shipping Country', $sourceHeaders);
                    $ShippingOptions = "";
                    
                    foreach ($items as $item) {
						$country = getCountryAB($item[$indexShipping]);
						
						if (isset($options[$country]['service_options'])){
                            $ShippingOptions = $options[$country]['service_options'];
                        } else {
                            if ($item[$indexShipping] == "South Korea"){
                                $ShippingOptions = 2;
                            } else {
                                $ShippingOptions = 0;
                            }
                        }
                    }
                    return $ShippingOptions;
                        
                },
            ]
        ];
    }

    public function parse($pathToFile, $options = [], $set = null)
    {
        $this->buildCsv($pathToFile, $this->getColumns($options));
    }
}