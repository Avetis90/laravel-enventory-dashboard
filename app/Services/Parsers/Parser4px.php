<?php

namespace App\Services\Parsers;
use App\Models\Products;
use Countries;

class Parser4px extends AbstractShopifyParser
{
    public function getColumns($options = [])
    {
        return [
            '仓库代码/Warehouse Code' => [
                'default' => 'USLA 美国洛杉矶库/US Los Angeles  Warehouse',
            ],
            '参考编号/Reference Code' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexName = array_search('Name', $sourceHeaders);
                    $indexCountry = array_search('Shipping Country', $sourceHeaders);
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
					
                    foreach ($items as $item) {
                        //$country = getCountryAB($item[$indexCountry]);
						
                        $order_number =  $item[$indexName];
                    }
					
					return $order_number;
                },
            ],
            '派送方式/Delivery Style' => [
                //'default' => 'USEXPBIG 美国优先大包',
                //'default' => 'US-Eco-parcel 美国本地邮政大包特惠',
                'callback' => function ($items, $sourceHeaders){
                    $indexCountry = array_search('Shipping Country', $sourceHeaders);
					$shipping_method = "";
					
                    foreach ($items as $item) {
						if (!empty(trim($item[$indexCountry]))){
							if (stripos($item[$indexCountry], "DHL")!==FALSE){
								$shipping_method = 'USEXPBIG 美国优先大包';
							} else {
								$shipping_method = 'US-Eco-parcel 美国本地邮政大包特惠';
							}
						}
                    }
					
                    return $shipping_method;
                },
            ],
            '保险类型/Insurance Type' => [
                'default' => 'NI 无保/Not  Insurance',
            ],
            '投保金额/Insurance Value' => [
                'default' => '',
            ],
            '销售平台/Sales Platform' => [
                'default' => '',
            ],
            '销售交易号/Sales Transaction Numbers' => [
                'default' => '',
            ],
            '收件人姓名/Consignee Name' => [
                'attribute' => 'Shipping Name',
            ],
            '收件人公司/Consignee Company' => [
                'attribute' => 'Shipping Company',
            ],
            '收件人国家/Consignee Country' => [
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
            '州/Province' => [
                'attribute' => 'Shipping Province',
            ],
            '城市/City' => [
                'attribute' => 'Shipping City',
            ],
            '街道/Street' => [
                'callback' => function ($items, $sourceHeaders) {
                    $indexStreet = array_search('Shipping Street', $sourceHeaders);
                    $indexCompany = array_search('Shipping Company', $sourceHeaders);
					$indexCountry = array_search('Shipping Country', $sourceHeaders);
					$indexProvince = array_search('Shipping Province', $sourceHeaders);
					$indexCity = array_search('Shipping City', $sourceHeaders);
					
                    foreach ($items as $item) {
						/*
						if (!empty($item[$indexStreet])){
							if ($item[$indexCompany] != ""){
								$address = $item[$indexCompany] . ", " . $item[$indexStreet];
							} else {
								$address = $item[$indexStreet];
							}
						}
						*/
						$address = $item[$indexStreet];
						
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
            '门牌号/Doorplate' => [
                'default' => '',
            ],
            '邮编/Zip Code' => [
                'attribute' => 'Shipping Zip',
            ],
            '收件人Email/Consignee Email' => [
                'default' => '',
            ],
            '收件人电话/Consignee Phone' => [
                'callback' => function ($items, $sourceHeaders){
                    $indexPhone = array_search('Shipping Phone', $sourceHeaders);
                    $phonenumber = "";
                    //foreach ($items as $item) {
					//	if (!empty($item[$indexPhone]) && ((substr($item[$indexPhone], 0, 1) == 0) || (substr($item[$indexPhone], 0, 1) == "+"))){
					//		$phonenumber = "'" . $item[$indexPhone];
					//	}
                    //}
                    foreach ($items as $item) {
						if (!empty($item[$indexPhone])){
							$phonenumber = str_replace("+1", "", str_replace("'", "", $item[$indexPhone]));
						}
                    }
					
					if (empty($phonenumber)){
						$phonenumber = "000";
					}
					
                    return $phonenumber;
                },
            ],
            '备注/Remark' => [
                'default' => '',
            ],
            'SKU1' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
					
					foreach ($items as $item) {
						if ($indexSKU !== false && isset($item[$indexSKU])) {
							$productSku = getSku4px($item[$indexSKU], 0);
						}
					}
					
					return $productSku;
                },
            ],
            '数量1/Quantity 1' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
					
					foreach ($items as $item) {
						if ($indexSKU !== false && isset($item[$indexSKU])) {
							$productQty = getQty4px($item[$indexSKU], 0);
						}
					}
					
					return $productQty;
                },
            ],
            'SKU2' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
					
					foreach ($items as $item) {
						if ($indexSKU !== false && isset($item[$indexSKU])) {
							$productSku = getSku4px($item[$indexSKU], 1);
						}
					}
					
					return $productSku;
                },
            ],
            '数量2/Quantity 2' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
					
					foreach ($items as $item) {
						if ($indexSKU !== false && isset($item[$indexSKU])) {
							$productQty = getQty4px($item[$indexSKU], 1);
						}
					}
					
					return $productQty;
                },
            ],
            'SKU3' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
					
					foreach ($items as $item) {
						if ($indexSKU !== false && isset($item[$indexSKU])) {
							$productSku = getSku4px($item[$indexSKU], 2);
						}
					}
					
					return $productSku;
                },
            ],
            '数量3/Quantity 3' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
					
					foreach ($items as $item) {
						if ($indexSKU !== false && isset($item[$indexSKU])) {
							$productQty = getQty4px($item[$indexSKU], 2);
						}
					}
					
					return $productQty;
                },
            ],
            'SKU4' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
					
					foreach ($items as $item) {
						if ($indexSKU !== false && isset($item[$indexSKU])) {
							$productSku = getSku4px($item[$indexSKU], 3);
						}
					}
					
					return $productSku;
                },
            ],
            '数量4/Quantity 4' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
					
					foreach ($items as $item) {
						if ($indexSKU !== false && isset($item[$indexSKU])) {
							$productQty = getQty4px($item[$indexSKU], 3);
						}
					}
					
					return $productQty;
                },
            ],
            'SKU5' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
					
					foreach ($items as $item) {
						if ($indexSKU !== false && isset($item[$indexSKU])) {
							$productSku = getSku4px($item[$indexSKU], 4);
						}
					}
					
					return $productSku;
                },
            ],
            '数量5/Quantity 5' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
					
					foreach ($items as $item) {
						if ($indexSKU !== false && isset($item[$indexSKU])) {
							$productQty = getQty4px($item[$indexSKU], 4);
						}
					}
					
					return $productQty;
                },
            ],
            'SKU6' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
					
					foreach ($items as $item) {
						if ($indexSKU !== false && isset($item[$indexSKU])) {
							$productSku = getSku4px($item[$indexSKU], 5);
						}
					}
					
					return $productSku;
                },
            ],
            '数量6/Quantity 6' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
					
					foreach ($items as $item) {
						if ($indexSKU !== false && isset($item[$indexSKU])) {
							$productQty = getQty4px($item[$indexSKU], 5);
						}
					}
					
					return $productQty;
                },
            ],
            'SKU7' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
					
					foreach ($items as $item) {
						if ($indexSKU !== false && isset($item[$indexSKU])) {
							$productSku = getSku4px($item[$indexSKU], 6);
						}
					}
					
					return $productSku;
                },
            ],
            '数量7/Quantity 7' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
					
					foreach ($items as $item) {
						if ($indexSKU !== false && isset($item[$indexSKU])) {
							$productQty = getQty4px($item[$indexSKU], 6);
						}
					}
					
					return $productQty;
                },
            ],
            'SKU8' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
					
					foreach ($items as $item) {
						if ($indexSKU !== false && isset($item[$indexSKU])) {
							$productSku = getSku4px($item[$indexSKU], 7);
						}
					}
					
					return $productSku;
                },
            ],
            '数量8/Quantity 8' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
					
					foreach ($items as $item) {
						if ($indexSKU !== false && isset($item[$indexSKU])) {
							$productQty = getQty4px($item[$indexSKU], 7);
						}
					}
					
					return $productQty;
                },
            ],
            'SKU9' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
					
					foreach ($items as $item) {
						if ($indexSKU !== false && isset($item[$indexSKU])) {
							$productSku = getSku4px($item[$indexSKU], 8);
						}
					}
					
					return $productSku;
                },
            ],
            '数量9/Quantity 9' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
					
					foreach ($items as $item) {
						if ($indexSKU !== false && isset($item[$indexSKU])) {
							$productQty = getQty4px($item[$indexSKU], 8);
						}
					}
					
					return $productQty;
                },
            ],
            'SKU10' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
					
					foreach ($items as $item) {
						if ($indexSKU !== false && isset($item[$indexSKU])) {
							$productSku = getSku4px($item[$indexSKU], 9);
						}
					}
					
					return $productSku;
                },
            ],
            '数量10/Quantity 10' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
					
					foreach ($items as $item) {
						if ($indexSKU !== false && isset($item[$indexSKU])) {
							$productQty = getQty4px($item[$indexSKU], 9);
						}
					}
					
					return $productQty;
                },
            ],
        ];
    }

    public function parse($pathToFile, $options = [], $set = null)
    {
        $this->buildCsv($pathToFile, $this->getColumns($options));
    }
}