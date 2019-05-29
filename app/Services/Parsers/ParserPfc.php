<?php

namespace App\Services\Parsers;
use App\Models\Products;
use Countries;

class ParserPfc extends AbstractShopifyParser
{
    public function getColumns($options = [])
    {
        return [
            '参考号' => [
                'attribute' => 'Name',
                'prefix' => $options['prefix']
            ],
            '店铺' => [
                'default' => '',
            ],
            '运输方式编码' => [
                'callback' => function ($items, $sourceHeaders) use ($options){
                    $indexShipping = array_search('Shipping Country', $sourceHeaders);
                    $indexPhone = array_search('Shipping Phone', $sourceHeaders);
                    $ShippingOptions = "";
                    $MiuList = array("AT", "BE", "BG", "HR", "CY", "CZ", "DK", "EE", "FI", "FR", "DE", "GB", "GR", "HU", "IE", "IT", "LV", "LT", "LU", "MT", "NL", "PL", "PT", "RO", "SK", "SI", "ES", "SE", "NO", "BR", "SA", "AE");
					$UbiList = array("CA", "AU", "NZ", "MX");
					
					$pfcOptions = array(
						'MY' => 'MYEXPRESS',
						'MYR' => 'SGRPOST',
						'SG' => 'SGEXPRESS',
						'SGR' => 'SGRPOST',
						'KR' => 'ETK',
						'JP' => 'ETK',
					);
					
                    foreach ($items as $item) {
						$country = getCountryAB($item[$indexShipping]);
						
						if (isset($options[$country]['service_options'])){
							$ShippingOptions = $options[$country]['service_options'];
						} else {
							if (!empty($item[$indexShipping])){
								if (array_key_exists($item[$indexShipping], $pfcOptions)){
									$haveExpress = (!empty($item[$indexPhone]) && ($item[$indexShipping] == "MY" || $item[$indexShipping] == "SG")) ? "" : "R";
									$ShippingOptions = $pfcOptions[ $item[$indexShipping] . $haveExpress];
								} elseif (in_array($item[$indexShipping], $MiuList)){
									$ShippingOptions = "*MIUTRUST*";
								} elseif (in_array($item[$indexShipping], $UbiList)){
									$ShippingOptions = "*UBI*";
								}
							}
						}
                    }
					
                    return $ShippingOptions;
                },
            ],
            '跟踪单号' => [
                'default' => '',
            ],
            '收件人名称' => [
                'attribute' => 'Shipping Name',
            ],
            '收件人国家' => [
                'callback' => function ($items, $sourceHeaders){
                    $indexCountry = array_search('Shipping Country', $sourceHeaders);
                    $country = "";
                    foreach ($items as $item) {
						if (!empty(trim($item[$indexCountry]))){
							$country = $item[$indexCountry];
							
							if (strlen($country) > 2 && stripos($country, "DHL")===FALSE){
								$country = $country;
								$country_info = Countries::where('name', $country)->first();
								$country_ab = $country_info->iso_3166_2;
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
            '收件人地址行1' => [
                'attribute' => 'Shipping Address1',
            ],
            '收件人地址行2' => [
                'attribute' => 'Shipping Address2',
            ],
            '收件人城市' => [
                'attribute' => 'Shipping City',
            ],
            '收件人省' => [
                'attribute' => 'Shipping Province',
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
            '收件人店铺' => [
                'default' => '',
            ],
            '收件人公司' => [
                'default' => '',
            ],
            '产品编码/SKU' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexName = array_search('Name', $sourceHeaders);
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
                    $indexQuantity = array_search('Lineitem quantity', $sourceHeaders);
                    $name = '';
                    $ourSku = '';
					$previousOrderId = '';
					$products = array();
					$uniqueProducts = array();
					$duplicates = "";
					
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
								
								if (!array_key_exists($item[$indexName], $uniqueProducts)){
									if (isset($products[$item[$indexSKU]])){
										$products[$item[$indexSKU]] += $item[$indexQuantity];
									} else {
										$products[$item[$indexSKU]] = $item[$indexQuantity];
									}
								} else {
									$products[$item[$indexSKU]] = $item[$indexQuantity];
									$duplicates = "废单: ";
								}
								
								if (!empty($previousOrderId) && $item[$indexName] != $previousOrderId){
									$uniqueProducts[$previousOrderId] += 1;
								}
								
								$previousOrderId = $item[$indexName];
							}
						}
						
						foreach ($products as $sku => $qty){
							$Product = Products::where('vendor_sku', trim($sku))->first();
							$ourSku = (!empty($Product->our_sku)) ? $Product->our_sku : $sku;
							
							$values[] = $ourSku . '(' . $qty . ') ';
						}
						
						//return $options['prefix'] . $name . ": " . implode(', ', $values);
						//return implode(', ', $values);
						return $duplicates . implode(', ', $values);
					}
                },
            ],
            '海关申报英文名称' => [
                'default' => $options['description'],
            ],
            '海关申报中文名称' => [
                'default' => $options['description_cn'],
            ],
            '海关货物编码' => [
                'default' => '',
            ],
            '申报品数量' => [
                'default' => '1',
            ],
            '申报品价值' => [
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
            '单件重量' => [
                'default' => '0.3',
            ],
            '产地' => [
                'default' => '',
            ],
            '备注' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexName = array_search('Name', $sourceHeaders);
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
                    $indexQuantity = array_search('Lineitem quantity', $sourceHeaders);
                    $name = '';
                    $ourSku = '';
					
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
								
								$Product = Products::where('vendor_sku', trim($item[$indexSKU]))->first();
								$ourSku = (!empty($Product->our_sku)) ? $Product->our_sku : $item[$indexSKU];
								
								$values[] = $ourSku . ' (' . $item[$indexQuantity] . ')';
							}
						}
						//return $options['prefix'] . $name . ": " . implode(', ', $values);
						return implode(', ', $values);
					}
                },
            ],
        ];
    }
    
    public function parse($pathToFile, $options = [], $set = null)
    {
        $this->buildCsv($pathToFile, $this->getColumns($options));
    }
}