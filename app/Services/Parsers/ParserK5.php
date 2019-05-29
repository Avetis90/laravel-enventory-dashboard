<?php

namespace App\Services\Parsers;
use App\Models\Products;
use Countries;

class ParserK5 extends AbstractShopifyParser
{
    public function getColumns($options = [])
    {
        return [
            '仓库代码/Warehouse Code' => [
                'default' => 'CZ',
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
                'callback' => function ($items, $sourceHeaders) use ($options){
                    $indexCountry = array_search('Shipping Country', $sourceHeaders);
					
					$ctsMethod = array("BE" => "DHLEU1", "MC" => "DHLEU1", "PL" => "DHLEU1", "SE" => "DHLEU1", "CY" => "DHLEU1", "MT" => "DHLEU1", "PT" => "DHLEU1", "GB" => "DHLEU1", "ES" => "DHLEU1", "RO" => "DHLEU1", "AT" => "DHLEU2", "CZ" => "DHLEU2", "LU" => "DHLEU2", "HU" => "DHLEU2", "SK" => "DHLEU2", "SI" => "DHLEU2", "BG" => "DHLEU2", "HR" => "DHLEU2", "EE" => "DHLEU2", "FI" => "DHLEU2", "IE" => "DHLEU2", "LV" => "DHLEU2", "LT" => "DHLEU2", "DK" => "DHLEU3", "FR" => "DHLEU3", "NL" => "DHLEU3", "IT" => "DHLEU3", "GR" => "DHLEU3", "DE" => "DHL_L_SMAL", "DHL-DE" => "DHL_L_SMAL");
					
                    foreach ($items as $item) {
						if (!empty($item[$indexCountry])){
							$country = getCountryAB($item[$indexCountry]);
							$ShippingOptions = $ctsMethod[$country];
						}
                    }
                    
                    return $ShippingOptions;
                },
            ],
            '是否为指定的派送方式' => [
                'default' => '否',
            ],
            '销售平台/Sales Platform' => [
                'default' => '',
            ],
            '收件人姓名/Consignee Name' => [
                'attribute' => 'Shipping Name',
            ],
            '收件人国家/Consignee Country' => [
                'callback' => function ($items, $sourceHeaders){
                    $indexCountry = array_search('Shipping Country', $sourceHeaders);
                    $country = "";
                    //foreach ($items as $item) {
					//	if (!empty($item[$indexPhone]) && ((substr($item[$indexPhone], 0, 1) == 0) || (substr($item[$indexPhone], 0, 1) == "+"))){
					//		$phonenumber = "'" . $item[$indexPhone];
					//	}
                    //}
                    foreach ($items as $item) {
						if (!empty($item[$indexCountry])){
							$country = str_replace("DHL-", "", $item[$indexCountry]);
						}
                    }
					
                    return $country;
                },
            ],
            '州/Province' => [
                'attribute' => 'Shipping Province',
            ],
            '城市/City' => [
                'attribute' => 'Shipping City',
            ],
            '邮编/Zip Code' => [
                'attribute' => 'Shipping Zip',
            ],
            '地址1/Street1' => [
                'callback' => function ($items, $sourceHeaders) {
                    $indexAddress1 = array_search('Shipping Address1', $sourceHeaders);
					
                    foreach ($items as $item) {						
						$address1 = $item[$indexAddress1];
                    }
					
					return $address1;
                },
            ],
            '地址2/Street2' => [
                'callback' => function ($items, $sourceHeaders) {
                    $indexAddress2 = array_search('Shipping Address2', $sourceHeaders);
					
                    foreach ($items as $item) {						
						$address2 = $item[$indexAddress2];
                    }
					
					return $address2;
                },
            ],
            '门牌号/Doorplate' => [
                'default' => '',
            ],
            '收件人公司/Consignee Company' => [
                'attribute' => 'Shipping Company',
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
							$phonenumber = "\t" . str_replace("+", "00", str_replace("'", "", $item[$indexPhone]));
						}
                    }
					
					if (empty($phonenumber)){
						$phonenumber = "\t000";
					}
					
                    return $phonenumber;
                },
            ],
            '签名服务/Signature' => [
                'default' => '',
            ],
            '保险服务/Insurance' => [
                'default' => '',
            ],
            '投保金额/Insurance Amount' => [
                'default' => '',
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
            'SKU11' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
					
					foreach ($items as $item) {
						if ($indexSKU !== false && isset($item[$indexSKU])) {
							$productSku = getSku4px($item[$indexSKU], 10);
						}
					}
					
					return $productSku;
                },
            ],
            '数量11/Quantity 11' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
					
					foreach ($items as $item) {
						if ($indexSKU !== false && isset($item[$indexSKU])) {
							$productQty = getQty4px($item[$indexSKU], 10);
						}
					}
					
					return $productQty;
                },
            ],
            'SKU12' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
					
					foreach ($items as $item) {
						if ($indexSKU !== false && isset($item[$indexSKU])) {
							$productSku = getSku4px($item[$indexSKU], 11);
						}
					}
					
					return $productSku;
                },
            ],
            '数量12/Quantity 12' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
					
					foreach ($items as $item) {
						if ($indexSKU !== false && isset($item[$indexSKU])) {
							$productQty = getQty4px($item[$indexSKU], 11);
						}
					}
					
					return $productQty;
                },
            ],
            'SKU13' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
					
					foreach ($items as $item) {
						if ($indexSKU !== false && isset($item[$indexSKU])) {
							$productSku = getSku4px($item[$indexSKU], 12);
						}
					}
					
					return $productSku;
                },
            ],
            '数量13/Quantity 13' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
					
					foreach ($items as $item) {
						if ($indexSKU !== false && isset($item[$indexSKU])) {
							$productQty = getQty4px($item[$indexSKU], 12);
						}
					}
					
					return $productQty;
                },
            ],
            'SKU14' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
					
					foreach ($items as $item) {
						if ($indexSKU !== false && isset($item[$indexSKU])) {
							$productSku = getSku4px($item[$indexSKU], 13);
						}
					}
					
					return $productSku;
                },
            ],
            '数量14/Quantity 14' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
					
					foreach ($items as $item) {
						if ($indexSKU !== false && isset($item[$indexSKU])) {
							$productQty = getQty4px($item[$indexSKU], 13);
						}
					}
					
					return $productQty;
                },
            ],
            'SKU15' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
					
					foreach ($items as $item) {
						if ($indexSKU !== false && isset($item[$indexSKU])) {
							$productSku = getSku4px($item[$indexSKU], 14);
						}
					}
					
					return $productSku;
                },
            ],
            '数量15/Quantity 15' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
					
					foreach ($items as $item) {
						if ($indexSKU !== false && isset($item[$indexSKU])) {
							$productQty = getQty4px($item[$indexSKU], 14);
						}
					}
					
					return $productQty;
                },
            ],
            'SKU16' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
					
					foreach ($items as $item) {
						if ($indexSKU !== false && isset($item[$indexSKU])) {
							$productSku = getSku4px($item[$indexSKU], 15);
						}
					}
					
					return $productSku;
                },
            ],
            '数量16/Quantity 16' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
					
					foreach ($items as $item) {
						if ($indexSKU !== false && isset($item[$indexSKU])) {
							$productQty = getQty4px($item[$indexSKU], 15);
						}
					}
					
					return $productQty;
                },
            ],
            'SKU17' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
					
					foreach ($items as $item) {
						if ($indexSKU !== false && isset($item[$indexSKU])) {
							$productSku = getSku4px($item[$indexSKU], 16);
						}
					}
					
					return $productSku;
                },
            ],
            '数量17/Quantity 17' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
					
					foreach ($items as $item) {
						if ($indexSKU !== false && isset($item[$indexSKU])) {
							$productQty = getQty4px($item[$indexSKU], 16);
						}
					}
					
					return $productQty;
                },
            ],
            'SKU18' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
					
					foreach ($items as $item) {
						if ($indexSKU !== false && isset($item[$indexSKU])) {
							$productSku = getSku4px($item[$indexSKU], 17);
						}
					}
					
					return $productSku;
                },
            ],
            '数量18/Quantity 18' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
					
					foreach ($items as $item) {
						if ($indexSKU !== false && isset($item[$indexSKU])) {
							$productQty = getQty4px($item[$indexSKU], 17);
						}
					}
					
					return $productQty;
                },
            ],
            'SKU19' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
					
					foreach ($items as $item) {
						if ($indexSKU !== false && isset($item[$indexSKU])) {
							$productSku = getSku4px($item[$indexSKU], 18);
						}
					}
					
					return $productSku;
                },
            ],
            '数量19/Quantity 19' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
					
					foreach ($items as $item) {
						if ($indexSKU !== false && isset($item[$indexSKU])) {
							$productQty = getQty4px($item[$indexSKU], 18);
						}
					}
					
					return $productQty;
                },
            ],
            'SKU20' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
					
					foreach ($items as $item) {
						if ($indexSKU !== false && isset($item[$indexSKU])) {
							$productSku = getSku4px($item[$indexSKU], 10);
						}
					}
					
					return $productSku;
                },
            ],
        ];
    }

    public function parse($pathToFile, $options = [], $set = null)
    {
        $this->buildCsv($pathToFile, $this->getColumns($options));
    }
}