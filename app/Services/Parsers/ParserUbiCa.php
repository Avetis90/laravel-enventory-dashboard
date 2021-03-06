<?php

namespace App\Services\Parsers;

class ParserUbiCa extends AbstractShopifyParser
{
    public function getColumns($options) {
        // Needed columns for result CSV
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
                'default' => '',
            ],
            // 'Total of all Lineitem price * 0.33'// (( Lineitem price ) * (Lineitem quantity) + others skus ) * 0.33
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
            // 'All Lineitem skus', //for example: like "001-112*1, 001-112*1, 001-112*2"
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
                'default' => '',
            ],
            'Battery Packing' => [
                'default' => '',
            ],
            'Battery Type' => [
                'default' => '',
            ],
            'Facility' => [
                'default' => 'SZX',
            ],
            'item no' => [
                'default' => '1',
            ],
            'item sku' => [
                'alias' => 'SKU',
            ],
            'item desc' => [
                'alias' => 'Description',
            ],
            'item hs code' => [
                'default' => '',
            ],
            'item origin' => [
                'default' => 'China',
            ],
            'item unit value' => [
                'alias' => 'Invoice Value',
            ],
            'item count' => [
                'default' => '1',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function parse($filePath, $options = [], $set = null)
    {
        $this->buildCsv($filePath, $this->getColumns($options));
    }
}
