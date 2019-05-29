<?php

namespace App\Services\Parsers;
use App\Models\Products;
use Countries;
use DateTime;

class ParserElife extends AbstractShopifyParser
{
    public function getColumns($options = [])
    {
        return [
			'Company ID' => [
				'default' => '51',
			],
			'Order Date' => [
				'callback' => function ($items, $sourceHeaders) {
					$indexDate = array_search('Created at', $sourceHeaders);
					
					foreach ($items as $item) {
						if (!empty($item[$indexDate])){
							$date = new DateTime();
							$date->setTimestamp($item[$indexDate]);
							$value = $date->format('Y-m-d');
						}
					}
					
					return $value;
				},
			],
			'Bag No.' => [
				'callback' => function ($items, $sourceHeaders) {
					$value = 510103001;
					return $value;
				},
			],
			'Order ID' => [
				'attribute' => 'Name',
				'prefix' => $options['prefix']
			],
			'Buyer ID' => [
				'default' => '',
			],
			'Ship Full Name' => [
				'attribute' => 'Shipping Name',
			],
			'Ship Company' => [
				'default' => '',
			],
			'Ship Address 1' => [
				'attribute' => 'Shipping Address1',
			],
			'Ship Address 2' => [
				'attribute' => 'Shipping Address2',
			],
			'Ship City' => [
				'attribute' => 'Shipping City',
			],
			'Ship State/Province' => [
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
			'Ship Postal Code' => [
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
			'Ship Country' => [
				'attribute' => 'Shipping Country',
			],
			'Ship Phone' => [
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
			'Ship Email' => [
				'default' => '',
			],
			'Requested Shipping' => [
				'default' => 'DHL GM',
			],
			'Cost' => [
				'default' => '',
			],
			'Item 1: SKU' => [
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
							if (stripos($item[$indexSKU], ",") === FALSE) {
								$qty = intval(rtrim(stringAfter("(", trim($item[$indexSKU])), ")"));
								$sku = strstr(trim($item[$indexSKU]), '(', true);
							} else {
								
							}
							
							return $sku;
						}
						
					}
				},
			],
			'Item 1: Quantity' => [
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
							if (stripos($item[$indexSKU], ",") === FALSE) {
								$qty = intval(rtrim(stringAfter("(", trim($item[$indexSKU])), ")"));
								$sku = strstr(trim($item[$indexSKU]), '(', true);
							} else {
								
							}
							
							return $qty;
						}
						
					}
				},
			],
			'Item 1: Unit Weight(LB)' => [
				'default' => '0.66',
			],
			'Item 1: Unit Price' => [
				'default' => '12.99',
			],
			'Item 1: Description' => [
				'default' => '',
			],
			'Item 2: SKU' => [
				'default' => '',
			],
			'Item 2: Quantity' => [
				'default' => '',
			],
			'Item 2: Unit Weight(LB)' => [
				'default' => '',
			],
			'Item 2: Unit Price' => [
				'default' => '',
			],
			'Item 2: Description' => [
				'default' => '',
			],
			'Item 3: SKU' => [
				'default' => '',
			],
			'Item 3: Quantity' => [
				'default' => '',
			],
			'Item 3: Unit Weight(LB)' => [
				'default' => '',
			],
			'Item 3: Unit Price' => [
				'default' => '',
			],
			'Item 3: Description' => [
				'default' => '',
			],
			'Item 4: SKU' => [
				'default' => '',
			],
			'Item 4: Quantity' => [
				'default' => '',
			],
			'Item 4: Unit Weight(LB)' => [
				'default' => '',
			],
			'Item 4: Unit Price' => [
				'default' => '',
			],
			'Item 4: Description' => [
				'default' => '',
			],
			'Item 5: SKU' => [
				'default' => '',
			],
			'Item 5: Quantity' => [
				'default' => '',
			],
			'Item 5: Unit Weight(LB)' => [
				'default' => '',
			],
			'Item 5: Unit Price' => [
				'default' => '',
			],
			'Item 5: Description' => [
				'default' => '',
			],
        ];
    }

    public function parse($pathToFile, $options = [], $set = null)
    {
        $this->buildCsv($pathToFile, $this->getColumns($options));
    }
}