<?php

namespace App\Services\Parsers;

class ParserUbiEu extends AbstractShopifyParser
{
    public function getColumns($options) {
        // Needed columns for result CSV
        return [
                'Ref No.' => [
                    'attribute' => 'Name',
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
                'Country' => [
                    'default' => '',
                ],
                'Phone' => [
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
                'Invoice Currency' => [
                    'attribute' => 'Currency',
                ],
                'Native Description' => [
                    'default' => $options['description_cn'],
                ],
                'Goods Description' => [
                    'default' => $options['description'],
                ],
                'Total weight' => [
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
                'Shipper Facility/Origin' => [
                    'default' => 'SZX',
                ],
                'Item No' => [
                    'default' => '1',
                ],
                'Item SKU' => [
                    'alias' => 'SKU',
                ],
                'Item Native Desc' => [
                    'default' => '',
                ],
                'Item Desc' => [
                    'alias' => 'Goods Description',
                ],
                'Item HSCode' => [
                    'alias' => 'Goods Description',
                ],
                'Item Origin' => [
                    'default' => 'China',
                ],
                'Item Weight' => [
                    'default' => '',
                ],
                'Item Unit Value' => [
                    'alias' => 'Invoice Value',
                ],
                'Item Count' => [
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
