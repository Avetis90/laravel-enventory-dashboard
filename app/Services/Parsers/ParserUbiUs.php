<?php

namespace App\Services\Parsers;

class ParserUbiUs extends AbstractShopifyParser
{
    public function getColumns($options) {
        return [
            'Ref No.' => [
                'attribute' => 'Name',
                'prefix' => $options['prefix']
            ],
            'Recipient Name' => [
                'attribute' => 'Shipping Name',
            ],
            'Recipient Company' => [
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
            'City/Suburb' => [
                'attribute' => 'Shipping City',
            ],
            'State' => [
                'attribute' => 'Shipping Province',
            ],
            'Postcode' => [
                'attribute' => 'Shipping Zip',
            ],
            'Phone' => [
                'attribute' => 'Shipping Phone',
            ],
            'Email' => [
                'attribute' => '',
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
            'Goods Description' => [
                'default' => $options['description'],
            ],
            'Total weight' => [
                'default' => '',
            ],
            'Packing List' => [
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
            'Battery Packing' => [
                'default' => '',
            ],
            'Battery Type' => [
                'default' => '',
            ],
            'Item No' => [
                'default' => '1',
            ],
            'Item SKU' => [
                'callback' => function ($items, $sourceHeaders) use ($options) {
                    $values = [];
                    $indexName = array_search('Name', $sourceHeaders);
                    $indexSKU = array_search('Lineitem sku', $sourceHeaders);
                    $indexQuantity = array_search('Lineitem quantity', $sourceHeaders);
                    $name = '';
                    foreach ($items as $item) {
                        if (
                            $indexName !== false && isset($item[$indexName]) &&
                            $indexSKU !== false && isset($item[$indexSKU]) &&
                            $indexQuantity !== false && isset($item[$indexQuantity])
                        ) {
                            if (empty($name)) {
                                $name = $item[$indexName];
                            }
                            $values[] = $item[$indexSKU] . '*' . $item[$indexQuantity];
                        }
                    }
                    return $options['prefix'] . $name . implode(', ', $values);
                },
            ],
            'Item Native Desc' => [
                'default' => $options['description_cn'],
            ],
            'Item Desc' => [
                'alias' => 'Goods Description',
            ],
            'Item Origin' => [
                'default' => 'CN',
            ],
            'Item Weight' => [
                'default' => '',
            ],
            'Item Unit Value' => [
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
            'Item Count' => [
                'default' => '1',
            ],
            'Warehouse No' => [
                'default' => '',
            ],

        ];
    }

    public function parse($pathToFile, $options = [], $set = null)
    {
        $this->buildCsv($pathToFile, $this->getColumns($options));
    }
}