<?php

namespace App\Services\Parsers;
use App\Models\Products;
use Countries;

class ParserOwEuDirectLine extends AbstractShopifyParser
{
    public function getColumns($options) {
        return [
            'Product Name-渠道名称' => [
                'callback' => function ($items, $sourceHeaders){
                    $indexShipping = array_search('Shipping Country', $sourceHeaders);
                    $ShippingOptions = "";
					
                    foreach ($items as $item) {
						if (!empty($item[$indexShipping]) && $item[$indexShipping] == "GB"){
							$ShippingOptions = "TP2";
						}
                    }
                    return $ShippingOptions;
                },
            ],
            'Customer Mainifest Date-客户预报日期' => [
                'default' => '',
            ],
            'HAWB-客户单号' => [
                'attribute' => 'Name',
                'prefix' => $options['prefix'],
            ],
            'Company Name-公司名' => [
                'attribute' => 'Shipping Company',
            ],
            'Contact-收件人' => [
                'attribute' => 'Shipping Name',
            ],
            'Address Line1-地址1' => [
                'attribute' => 'Shipping Address1',
            ],
            'Address Line2-地址2' => [
                'attribute' => 'Shipping Address2',
            ],
            'Address Line3-地址3' => [
                'default' => '',
            ],
            'City-城市' => [
                'attribute' => 'Shipping City',
            ],
            'State-州' => [
                'attribute' => 'Shipping Province',
            ],
            'Country-国家' => [
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
            'PostCode-邮编' => [
                'attribute' => 'Shipping Zip',
            ],
            'Telephone-电话' => [
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
            'Email-邮箱' => [
                'attribute' => '',
            ],
            'Weight-重量（kg）' => [
                'default' => '0.3',
            ],
            'Description-申报品名' => [
                'default' => $options['description'],
            ],
            'Description_CN-中文申报品名' => [
                'default' => $options['description_cn'],
            ],
            'Number of Pieces-产品总pcs' => [
                'default' => '1',
            ],
            'Value-申报总金额' => [
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
            'Currency-币种' => [
                'default' => 'USD',
            ],
            'Item Type-是否含电' => [
                'default' => '',
            ],
            'Length-长' => [
                'default' => '',
            ],
            'Width-宽' => [
                'default' => '',
            ],
            'Heigh-高' => [
                'default' => '',
            ],
            'Notes-备注' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexName = array_search('Name', $sourceHeaders);
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
                    $indexQuantity = array_search('Lineitem quantity', $sourceHeaders);
                    $name = '';
                    $ourSku = '';
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
                },
            ],
            'DeliveryAddress-交货地址' => [
                'default' => '深圳',
            ],
            '关税（DDP/DDU)' => [
                'default' => '',
            ],
            'Shipper-发货人/公司' => [
                'default' => '',
            ],
            'ShippingAddress-发货地址' => [
                'default' => '',
            ],
            'Shipper Tel/Email-发货人电话/邮箱' => [
                'default' => '',
            ],
            'ShippersVATNO-税号' => [
                'default' => '',
            ],
            '货物类型（L/P）' => [
                'default' => '',
            ],
            '产品网址' => [
                'default' => '',
            ],
            '交易类型（B2B/B2C）' => [
                'default' => '',
            ],
            'EORI' => [
                'default' => '',
            ],
            '一票多件类型' => [
                'default' => '',
            ],
            'SKU' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexName = array_search('Name', $sourceHeaders);
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
                    $indexQuantity = array_search('Lineitem quantity', $sourceHeaders);
                    $name = '';
                    $ourSku = '';
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
                },
            ],
        ];
    }

    public function parse($pathToFile, $options = [], $set = null)
    {
        $this->buildCsv($pathToFile, $this->getColumns($options));
    }
}
