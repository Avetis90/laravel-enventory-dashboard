<?php

namespace App\Services\Parsers;
use App\Models\Products;
use Countries;

class ParserUbiAll extends AbstractShopifyParser
{
    public function getColumns($options = [])
    {
        return [
            'referenceNo' => [
                'attribute' => 'Name',
                'prefix' => $options['prefix']
            ],
            'recipientName' => [
                'attribute' => 'Shipping Name',
            ],
            'recipientCompany' => [
                'attribute' => 'Shipping Company',
            ],
            'email' => [
                'default' => '',
            ],
            'phone' => [
                'callback' => function ($items, $sourceHeaders){
                    $indexPhone = array_search('Shipping Phone', $sourceHeaders);
                    $phonenumber = "";
                    foreach ($items as $item) {
						if (!empty($item[$indexPhone])){
							$phonenumber = str_replace("'", "", $item[$indexPhone]);
						}
                    }
					if (empty($phonenumber)){
						$phonenumber = "000";
					}
					
                    return $phonenumber;
                },
            ],
            'addressLine1' => [
                'attribute' => 'Shipping Address1',
            ],
            'addressline2' => [
                'attribute' => 'Shipping Address2',
            ],
            'addressline3' => [
                'default' => '',
            ],
            'city' => [
                'attribute' => 'Shipping City',
            ],
            'state' => [
                'attribute' => 'Shipping Province',
            ],
            'postcode' => [
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
            'country' => [
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
            'returnName' => [
                'default' => 'Smart Ecom Ltd',
            ],
            'weight' => [
                'default' => '0.3',
            ],
            'volume' => [
                'default' => '0.1',
            ],
            'length' => [
                'default' => '0',
            ],
            'width' => [
                'default' => '0',
            ],
            'height' => [
                'default' => '0',
            ],
            'invoiceValue' => [
                'default' => '13.5',
            ],
            'invoiceCurrency' => [
                'default' => 'USD',
            ],
            'batteryType' => [
                'default' => '0',
            ],
            'batteryPacking' => [
                'default' => '0',
            ],
            'description' => [
                'default' => $options['description'],
            ],
            'nativeDescription' => [
                'default' => $options['description_cn'],
            ],
            'sku' => [
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
            'serviceOption' => [
                'callback' => function ($items, $sourceHeaders) use ($options){
                    $indexShipping = array_search('Shipping Country', $sourceHeaders);
                    $indexAddress1 = array_search('Shipping Address1', $sourceHeaders);
                    $ShippingOptions = "";
					
					$ShippingCountryOptions = array(
						'US' => 'EcoSmart',
						'CA' => 'Expedited',
						'NZ' => 'Tracking-Non-Signature',
						'AU' => 'E-Parcel',
					);
					
                    foreach ($items as $item) {
						if (!empty($item[$indexShipping])){
							$country = getCountryAB($item[$indexShipping]);
                            
                            if (isset($options[$country]['service_options'])){
								$ErrorMsg = (strlen($item[$indexAddress1]) > 40) ? "**ERROR** -" : "";
                                $ShippingOptions = $options[$country]['service_options'];
                            }
                            /*
							if (array_key_exists($country, $ShippingCountryOptions)){
								$ErrorMsg = (strlen($item[$indexAddress1]) > 40) ? "**ERROR** -" : "";
								$ShippingOptions = $ErrorMsg . $ShippingCountryOptions[$country];
							}
                            */
						}
                    }
                    return $ShippingOptions;
                },
            ],
            'facility' => [
                'default' => 'SZX',
            ],
            'packingList' => [
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