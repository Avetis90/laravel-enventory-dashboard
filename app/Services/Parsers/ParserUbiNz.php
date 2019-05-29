<?php

namespace App\Services\Parsers;

class ParserUbiNz extends AbstractShopifyParser
{
    public function getColumns($options = [])
    {
        return [
            'Invoice No' => [
                'attribute' => 'Name',
            ],
            'Customer Name' => [
                'attribute' => 'Shipping Name',
            ],
            'Company' => [
                'attribute' => 'Shipping Company',
            ],
            'Address line 1' => [
                'attribute' => 'Shipping Address1',
            ],
            'Address line 2' => [
                'attribute' => 'Shipping Address2',
            ],
            'Address line 3' => [
                'default' => '',
            ],
            'City' => [
                'attribute' => 'Shipping City',
            ],
            'State' => [
                'attribute' => 'Shipping Province',
            ],
            'Postcode' => [
                'attribute' => 'Shipping Zip',
            ],
            'Tel No' => [
                'attribute' => 'Shipping Phone',
            ],
            'Email' => [
                'attribute' => 'Email',
            ],
            'Invoice Value' => [
                'callback' => function ($items, $sourceHeaders) {
                    $value = 0;
                    $indexPrice = array_search('Lineitem price', $sourceHeaders);
                    $indexQuantity = array_search('Lineitem quantity', $sourceHeaders);
                    foreach ($items as $item) {
                        if (
                            $indexPrice !== false && isset($item[$indexPrice]) &&
                            $indexQuantity !== false && isset($item[$indexQuantity])
                        ) {
                            $value += $item[$indexPrice] * $item[$indexQuantity];
                        }
                    }
                    return $value * 0.33;
                },
            ],
            'Currency' => [
                'attribute' => 'Currency',
            ],
            'Description' => [
                'default' => $options['description'],
            ],
            'Special Instructions' => [
                'default' => '',
            ],
            'Total no of packages' => [
                'default' => '1',
            ],
            'Total weight (kg)' => [
                'default' => '',
            ],
            'SKU' => [
                'callback' => function ($items, $sourceHeaders) {
                    $values = [];
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
                    $indexQuantity = array_search('Lineitem quantity', $sourceHeaders);
                    foreach ($items as $item) {
                        if (
                            $indexSKU !== false && isset($item[$indexSKU]) &&
                            $indexQuantity !== false && isset($item[$indexQuantity])
                        ) {
                            $values[] = $item[$indexSKU] . '*' . $item[$indexQuantity];
                        }
                    }
                    return implode(', ', $values);
                },
            ],
            'Service Option' => [
                'default' => $options['service_options'],
            ],
            'Battery Packing' => [
                'default' => $options['battery_packing'],
            ],
            'Battery Type' => [
                'default' => $options['battery_type'],
            ],
            'Facility' => [
                'default' => 'SZX',
            ],
        ];
    }

    public function parse($pathToFile, $options = [], $set = null)
    {
        $this->buildCsv($pathToFile, $this->getColumns($options));
    }
}