<?php

namespace App\Services\Parsers;
use App\Models\Products;
use Countries;

class ParserWyt extends AbstractShopifyParser
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
					
                    foreach ($items as $item) {
                        $country = getCountryAB($item[$indexCountry]);
                        
                        if (stripos($options['title'], "Woo") !== false)
                        {
                            if (stripos($options[$country]['service_options'], "德国小包") || $item[$indexCountry] == "NO" || $item[$indexCountry] == "CH" || $item[$indexCountry] == "Switzerland")
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
                    $indexShipping = array_search('Shipping Country', $sourceHeaders);
                    $ShippingOptions = "";
                    
					if (stripos(strtolower($options['prefix']), "sa") !== false){
						$MiuEUExpList = array("AT", "BE", "BG", "HR", "CY", "CZ", "DK", "EE", "FI", "FR", "DE", "GR", "HU", "IE", "IT", "LV", "LT", "LU", "MT", "NL", "PL", "PT", "RO", "SK", "SI", "ES", "SE");
						$MiuTRList = array("NO", "BR", "SA");
						$MiuNLList = array("AE");
					} else {
						$MiuEUExpList = array("BG", "CY", "BE", "FR", "DE", "GB", "HU", "IE", "IT", "LV", "LT", "LU", "MT", "NL", "PL", "PT", "SK", "ES", "IS");
						$WYTSwissList = array("AT", "HR", "CZ", "DK", "FI", "RO", "SI", "NO", "SE");
						$WYTGMList = array("GR");
					}
					
                    foreach ($items as $item) {
						$country = getCountryAB($item[$indexShipping]);
						
						if (isset($options[$country]['service_options'])){
							$ShippingOptions = $options[$country]['service_options'];
						} else {
							if (in_array($item[$indexShipping], $MiuEUExpList)){
								$ShippingOptions = "MT-EUEXP";
							} elseif (isset($MiuTRList) && in_array($item[$indexShipping], $MiuTRList)){
								$ShippingOptions = "MT-TR";
							} elseif (isset($MiuNLList) && in_array($item[$indexShipping], $MiuNLList)){
								$ShippingOptions = "MT-NL";
							} elseif (isset($WYTSwissList) && in_array($item[$indexShipping], $WYTSwissList)){
								$ShippingOptions = "WYT-SWISS";
							} elseif (isset($WYTGMList) && in_array($item[$indexShipping], $WYTGMList)){
								$ShippingOptions = "WYT-GM";
							}
						}
                    }
                    return $ShippingOptions;
                },
            ],
            '目的国家' => [
                'callback' => function ($items, $sourceHeaders){
                    $indexCountry = array_search('Shipping Country', $sourceHeaders);
                    $country = "";
                    foreach ($items as $item) {
						if (!empty(trim($item[$indexCountry]))){
							$country_ab = getCountryAB($item[$indexCountry]);
						}
                    }
                    return $country_ab;
                },
            ],
            '收件人姓名' => [
                'attribute' => 'Shipping Name',
            ],
            '州,省' => [
                'attribute' => 'Shipping Province',
            ],
            '城市' => [
                'attribute' => 'Shipping City',
            ],
            '联系地址' => [
                'callback' => function ($items, $sourceHeaders) {
                    $indexStreet = array_search('Shipping Street', $sourceHeaders);
                    $indexCompany = array_search('Shipping Company', $sourceHeaders);
					
                    foreach ($items as $item) {
                        if ($item[$indexCompany] != ""){
							$address = $item[$indexCompany] . ", " . $item[$indexStreet];
						} else {
							$address = $item[$indexStreet];
						}
                    }
					
					return $address;
                },
            ],
            /*
            '联系地址' => [
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
                'default' => '0.85',
            ],
            '海关报关品名1' => [
                'default' => $options['description'],
            ],
            '配货信息1' => [
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
                },
            ],
            '申报价值1' => [
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
            '申报品数量1' => [
                'default' => '1',
            ],
        ];
    }

    public function parse($pathToFile, $options = [], $set = null)
    {
        $this->buildCsv($pathToFile, $this->getColumns($options));
    }
}